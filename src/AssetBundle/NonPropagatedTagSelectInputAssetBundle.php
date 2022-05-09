<?php

/**
 * @link https://gewerk.dev/plugins/non-propagated-tags
 * @copyright 2022 gewerk, Dennis Morhardt
 * @license https://github.com/gewerk/non-propagated-tags/blob/main/LICENSE.md
 */

namespace Gewerk\NonPropagatedTags\AssetBundle;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use craft\web\View;

/**
 * Asset bundle for tags input
 *
 * @package Gewerk\NonPropagatedTags\AssetBundle
 */
class NonPropagatedTagSelectInputAssetBundle extends AssetBundle
{
    /** @inheritdoc */
    public $sourcePath = '@non-propagated-tags/resources/assets/dist';

    /** @inheritdoc */
    public $depends = [
        CpAsset::class,
    ];

    /** @inheritdoc */
    public $js = [
        'non-propagated-tag-select-input.js',
    ];

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        /** @var View $view */
        parent::registerAssetFiles($view);

        // Register translations
        $view->registerTranslations('app', [
            'Remove',
            'A server error occurred.',
        ]);
    }
}
