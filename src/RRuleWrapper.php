<?php
namespace lewisjenkins\rrulewrapper;

use craft\web\twig\variables\CraftVariable;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Module as BaseModule;
use lewisjenkins\rrulewrapper\services\RRuleApi;

/**
 * RRuleWrapper module
 *
 * Provides Twig and PHP access to the php-rrule library via the RRuleApi service.
 *
 * @link https://github.com/rlanvin/php-rrule
 *
 * @property-read RRuleApi $rrule
 */
final class RRuleWrapper extends BaseModule implements BootstrapInterface
{
    public const MODULE_ID = 'craft-rrule';

    public function bootstrap($app): void
    {
        $app->setModule(self::MODULE_ID, $this);

        $app->getModule(self::MODULE_ID);
    }

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