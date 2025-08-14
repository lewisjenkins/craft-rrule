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
     * If $spec is provided, it should be a multi-line RFC block containing lines like
     * DTSTART, RRULE, EXRULE, EXDATE, RDATE. When omitted, an empty RSet is created.
     *
     * @param string|null $spec Optional multi-line RFC block
     * @return RSet
     */
    public function rset(?string $spec = null): RSet
    {
        return $spec !== null ? new RSet($spec) : new RSet();
    }
}
