<?php
namespace lewisjenkins\rrulewrapper\services;

use yii\base\Component;
use RRule\RRule;
use RRule\RSet;

final class RRuleApi extends Component
{
    public function rrule(string|array $spec): RRule
    {
        return new RRule($spec);
    }

    public function rset(): RSet
    {
        return new RSet();
    }
}
