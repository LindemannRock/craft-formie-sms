<?php
/**
 * @copyright Copyright (c) 2026 LindemannRock
 */

namespace lindemannrock\formiesms\tests\Support;

use yii\base\Component;

/**
 * Safe status projection fixture, without transport or persistence.
 * @since 3.11.3
 */
final class StatusTransport extends Component
{
    public array $result = ['available' => true, 'holds' => []];

    public function status(object $context): array
    {
        return $this->result;
    }
}
