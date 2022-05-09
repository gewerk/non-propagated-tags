<?php

/**
 * @link https://gewerk.dev/plugins/non-propagated-tags
 * @copyright 2022 gewerk, Dennis Morhardt
 * @license https://github.com/gewerk/non-propagated-tags/blob/main/LICENSE.md
 */

namespace Gewerk\NonPropagatedTags;

use Craft;
use craft\base\Plugin;
use craft\events\DefineBehaviorsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\i18n\PhpMessageSource;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use yii\base\Event;

/**
 * Inits the plugin and acts as service locator
 *
 * @package Gewerk\NonPropagatedTags
 */
class NonPropagatedTags extends Plugin
{
    /**
     * Current plugin instance
     *
     * @var self
     */
    public static $plugin;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        // Save current instance
        self::$plugin = $this;

        // Set alias
        Craft::setAlias('@non-propagated-tags', $this->getRootPath());

        // Set controller namespaces
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'Gewerk\\NonPropagatedTags\\Console\\Controller';
        } else {
            $this->controllerNamespace = 'Gewerk\\NonPropagatedTags\\Controller';
        }

        // Load translations
        Craft::$app->getI18n()->translations['non-propagated-tags'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath' => '@non-propagated-tags/resources/translations',
            'forceTranslation' => true,
            'allowOverrides' => true,
        ];

        // Base template directory
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $event) {
                $event->roots['non-propagated-tags'] = Craft::getAlias(
                    '@non-propagated-tags/resources/templates'
                );
            }
        );

        // Register field
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Field\NonPropagatedTags::class;
            }
        );

        // Register behavior
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_DEFINE_BEHAVIORS,
            function (DefineBehaviorsEvent $event) {
                /** @var CraftVariable */
                $sender = $event->sender;
                $sender->attachBehaviors([
                    Behavior\CraftVariableBehavior::class,
                ]);
            }
        );
    }

    /**
     * Returns the plugin root path
     *
     * @return string
     */
    public function getRootPath()
    {
        return dirname(dirname(__FILE__));
    }
}
