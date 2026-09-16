import assert from 'node:assert/strict';
import {spawnSync} from 'node:child_process';
import {copyFileSync, existsSync, mkdirSync, mkdtempSync, readFileSync, readdirSync, rmSync, writeFileSync} from 'node:fs';
import {tmpdir} from 'node:os';
import {dirname, join, resolve} from 'node:path';
import {fileURLToPath} from 'node:url';
import test from 'node:test';

const packageRoot = resolve(dirname(fileURLToPath(import.meta.url)), '../..');

function withOwnedRoot(run) {
    const root = mkdtempSync(join(tmpdir(), 'formie-sms-runner-test-'));
    try {
        run(root);
    } finally {
        rmSync(root, {recursive: true, force: true});
    }
}

function fakeCommand(root, name, body) {
    const bin = join(root, 'bin');
    mkdirSync(bin, {recursive: true});
    const path = join(bin, name);
    writeFileSync(path, `#!/usr/bin/env bash\n${body}\n`, {mode: 0o755});
    return path;
}

function invoke(script, root, extraEnv = {}, cwd = packageRoot) {
    return spawnSync('bash', [script], {
        cwd,
        env: {...process.env, PATH: `${join(root, 'bin')}:${process.env.PATH}`, ...extraEnv},
        encoding: 'utf8',
        timeout: 15000,
    });
}

test('workspace hook propagates DDEV failure without invoking host Composer', () => withOwnedRoot((root) => {
    const workspacePackage = join(root, 'plugins/formie-sms');
    mkdirSync(join(workspacePackage, '.githooks'), {recursive: true});
    mkdirSync(join(root, 'vendor'));
    writeFileSync(join(root, 'bootstrap.php'), '<?php');
    const hook = join(workspacePackage, '.githooks/pre-commit');
    copyFileSync(join(packageRoot, '.githooks/pre-commit'), hook);
    const ddevLog = join(root, 'ddev-args');
    const composerLog = join(root, 'composer-args');
    fakeCommand(root, 'ddev', 'printf "%s\\n" "$@" > "$PROBE_DDEV_LOG"\nexit 42');
    fakeCommand(root, 'composer', 'printf "%s\\n" "$@" > "$PROBE_COMPOSER_LOG"\nexit 0');

    const result = invoke(hook, root, {
        PROBE_DDEV_LOG: ddevLog,
        PROBE_COMPOSER_LOG: composerLog,
    }, root);

    assert.equal(result.status, 42, result.stderr);
    assert.equal(readFileSync(ddevLog, 'utf8'), 'exec\ncd plugins/formie-sms && composer ci\n');
    assert.equal(existsSync(composerLog), false);
}));

test('Act wrapper propagates failure and requests exact runner cleanup', () => withOwnedRoot((root) => {
    const actLog = join(root, 'act-args');
    fakeCommand(root, 'act', 'printf "%s\\n" "$@" > "$PROBE_ACT_LOG"\nexit 37');

    const result = invoke(join(packageRoot, 'scripts/act-quality-gates'), root, {PROBE_ACT_LOG: actLog}, root);

    assert.equal(result.status, 37, result.stderr);
    const args = readFileSync(actLog, 'utf8').trim().split('\n');
    assert.deepEqual(args.slice(0, 6), ['push', '-W', '.github/workflows/ci.yml', '-j', 'quality-gates', '-P']);
    assert.equal(args.filter((arg) => arg === '--rm').length, 1);
}));

test('Act wrapper rejects a workflow without the package gate before starting Act', () => withOwnedRoot((root) => {
    const scripts = join(root, 'package/scripts');
    const workflowDir = join(root, 'package/.github/workflows');
    mkdirSync(scripts, {recursive: true});
    mkdirSync(workflowDir, {recursive: true});
    copyFileSync(join(packageRoot, 'scripts/act-quality-gates'), join(scripts, 'act-quality-gates'));
    const workflow = readFileSync(join(packageRoot, '.github/workflows/ci.yml'), 'utf8');
    writeFileSync(join(workflowDir, 'ci.yml'), workflow.replace('run: composer quality-gate', 'run: composer ci'));
    const actLog = join(root, 'act-args');
    fakeCommand(root, 'act', 'printf "%s\\n" "$@" > "$PROBE_ACT_LOG"\nexit 0');

    const result = invoke(join(scripts, 'act-quality-gates'), root, {PROBE_ACT_LOG: actLog}, root);

    assert.notEqual(result.status, 0);
    assert.match(result.stderr, /composer quality-gate exactly once/);
    assert.equal(existsSync(actLog), false);
}));

test('failed CI project setup preserves its failure and removes only its new project', () => withOwnedRoot((root) => {
    const projectLog = join(root, 'project-path');
    const sibling = join(root, 'formie-sms-ci.unrelated');
    mkdirSync(sibling);
    writeFileSync(join(sibling, 'keep'), 'owner');
    fakeCommand(root, 'composer', 'if [[ "$1" == create-project ]]; then\n  mkdir -p "$3"\n  touch "$3/partial"\n  printf "%s" "$3" > "$PROBE_PROJECT_LOG"\n  exit 23\nfi\nexit 99');

    const result = invoke(join(packageRoot, 'scripts/prepare-ci-test-project'), root, {
        RUNNER_TEMP: root,
        GITHUB_ENV: join(root, 'github-env'),
        PROBE_PROJECT_LOG: projectLog,
    }, root);

    assert.equal(result.status, 23, result.stderr);
    assert.equal(existsSync(dirname(readFileSync(projectLog, 'utf8'))), false);
    assert.equal(readFileSync(join(sibling, 'keep'), 'utf8'), 'owner');
    assert.equal(existsSync(join(root, 'github-env')), false);
}));

test('CI project cleanup deletes the recorded directory and rejects another target', () => withOwnedRoot((root) => {
    const owned = join(root, 'formie-sms-ci.owned');
    const sibling = join(root, 'formie-sms-ci.other');
    mkdirSync(owned);
    mkdirSync(sibling);
    writeFileSync(join(sibling, 'keep'), 'owner');
    const script = join(packageRoot, 'scripts/cleanup-ci-test-project');

    const valid = invoke(script, root, {RUNNER_TEMP: root, FORMIE_SMS_CI_OWNED_PARENT: owned}, root);
    assert.equal(valid.status, 0, valid.stderr);
    assert.equal(existsSync(owned), false);
    assert.equal(readFileSync(join(sibling, 'keep'), 'utf8'), 'owner');

    const invalid = invoke(script, root, {RUNNER_TEMP: root, FORMIE_SMS_CI_OWNED_PARENT: join(root, 'unrelated')}, root);
    assert.equal(invalid.status, 1);
    assert.match(invalid.stderr, /Refusing to remove unexpected CI project path/);
    assert.equal(readFileSync(join(sibling, 'keep'), 'utf8'), 'owner');
}));

test('audit runner cleans its exact directory and propagates Composer failure', () => withOwnedRoot((root) => {
    const scripts = join(root, 'package/scripts');
    mkdirSync(scripts, {recursive: true});
    copyFileSync(join(packageRoot, 'scripts/check-audit'), join(scripts, 'check-audit'));
    copyFileSync(join(packageRoot, 'composer.json'), join(root, 'package/composer.json'));
    const auditLog = join(root, 'audit-path');
    const sibling = join(root, 'owner-data');
    mkdirSync(sibling);
    writeFileSync(join(sibling, 'keep'), 'owner');
    fakeCommand(root, 'composer', 'printf "%s" "$PWD" > "$PROBE_AUDIT_LOG"\ntouch owned-partial\nexit 29');

    const result = invoke(join(scripts, 'check-audit'), root, {
        TMPDIR: root,
        PROBE_AUDIT_LOG: auditLog,
    }, root);

    assert.equal(result.status, 29, result.stderr);
    assert.equal(existsSync(readFileSync(auditLog, 'utf8')), false);
    assert.equal(readdirSync(root).filter((name) => name.startsWith('formie-sms-audit.')).length, 0);
    assert.equal(readFileSync(join(sibling, 'keep'), 'utf8'), 'owner');
}));

test('audit runner reports cleanup failure without replacing the Composer failure', () => withOwnedRoot((root) => {
    const scripts = join(root, 'package/scripts');
    mkdirSync(scripts, {recursive: true});
    copyFileSync(join(packageRoot, 'scripts/check-audit'), join(scripts, 'check-audit'));
    copyFileSync(join(packageRoot, 'composer.json'), join(root, 'package/composer.json'));
    fakeCommand(root, 'composer', 'exit 29');
    fakeCommand(root, 'rm', 'exit 41');

    const result = invoke(join(scripts, 'check-audit'), root, {TMPDIR: root}, root);

    assert.equal(result.status, 29, result.stderr);
    assert.match(result.stderr, /Failed to remove owned audit directory/);
    assert.equal(readdirSync(root).filter((name) => name.startsWith('formie-sms-audit.')).length, 1);
}));

test('audit runner removes its project after success', () => withOwnedRoot((root) => {
    const scripts = join(root, 'package/scripts');
    mkdirSync(scripts, {recursive: true});
    copyFileSync(join(packageRoot, 'scripts/check-audit'), join(scripts, 'check-audit'));
    copyFileSync(join(packageRoot, 'composer.json'), join(root, 'package/composer.json'));
    fakeCommand(root, 'composer', 'exit 0');

    const result = invoke(join(scripts, 'check-audit'), root, {TMPDIR: root}, root);

    assert.equal(result.status, 0, result.stderr);
    assert.equal(readdirSync(root).filter((name) => name.startsWith('formie-sms-audit.')).length, 0);
}));

test('audit runner fails when cleanup alone fails', () => withOwnedRoot((root) => {
    const scripts = join(root, 'package/scripts');
    mkdirSync(scripts, {recursive: true});
    copyFileSync(join(packageRoot, 'scripts/check-audit'), join(scripts, 'check-audit'));
    copyFileSync(join(packageRoot, 'composer.json'), join(root, 'package/composer.json'));
    fakeCommand(root, 'composer', 'exit 0');
    fakeCommand(root, 'rm', 'exit 41');

    const result = invoke(join(scripts, 'check-audit'), root, {TMPDIR: root}, root);

    assert.equal(result.status, 1, result.stderr);
    assert.match(result.stderr, /Failed to remove owned audit directory/);
    assert.equal(readdirSync(root).filter((name) => name.startsWith('formie-sms-audit.')).length, 1);
}));
