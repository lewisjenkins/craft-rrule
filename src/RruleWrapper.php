<?php
namespace lewisjenkins\rrulewrapper;

use craft\web\twig\variables\CraftVariable;
use yii\base\Module as BaseModule;
use yii\base\Event;
use lewisjenkins\rrulewrapper\services\RRuleApi;

/**
 * @property-read RRuleApi $rrule
 */
final class RruleWrapper extends BaseModule
{
    public function init(): void
    {
        parent::init();

        $this->setComponents([
            'rrule' => RRuleApi::class,
        ]);

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function($e) {
                /** @var CraftVariable $variable */
                $variable = $e->sender;
                $variable->set('rrule', $this->get('rrule'));
            }
        );
    }

    public function getRrule(): RRuleApi
    {
        return $this->get('rrule');
    }
}
