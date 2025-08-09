<?php
namespace lewisjenkins\rrulewrapper\twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use RRule\RRule;

final class RRuleTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('rrule_next', fn($spec,$after='now',$inc=false)
                => (new RRule($spec))->getNextOccurrence($after,$inc)),
            new TwigFunction('rrule_between', fn($spec,$start,$end,$limit=0)
                => (new RRule($spec))->getOccurrencesBetween($start,$end,$limit)),
        ];
    }
}
