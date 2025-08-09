<?php
namespace lewisjenkins\rrulewrapper\services;

use yii\base\Component;
use RRule\RRule;
use RRule\RSet;

/**
 * Provides access to the php‑rrule library within Craft.
 * Fully exposes the original API (RRule & RSet).
 *
 * @link https://github.com/rlanvin/php-rrule/wiki/RRule
 * @link https://github.com/rlanvin/php-rrule/wiki/RSet
 */
final class RRuleApi extends Component
{
    /**
     * Create a new RRule instance.
     *
     * @param string|array $spec Recurrence rule spec (RRULE string or options array)
     * @return RRule
     */
    public function rrule(string|array $spec): RRule
    {
        return new RRule($spec);
    }

    /**
     * Create a new RSet instance.
     *
     * @return RSet
     */
    public function rset(): RSet
    {
        return new RSet();
    }
}