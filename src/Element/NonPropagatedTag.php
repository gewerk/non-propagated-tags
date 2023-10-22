<?php

/**
 * @link https://gewerk.dev/plugins/non-propagated-tags
 * @copyright 2022 gewerk, Dennis Morhardt
 * @license https://github.com/gewerk/non-propagated-tags/blob/main/LICENSE.md
 */

namespace Gewerk\NonPropagatedTags\Element;

use craft\elements\Tag;
use craft\helpers\UrlHelper;
use Gewerk\NonPropagatedTags\Element\Query\NonPropagatedTagQuery;

/**
 * Extended tag with propagation set to none
 *
 * @package Gewerk\NonPropagatedTags\Element
 */
class NonPropagatedTag extends Tag
{
    /**
     * @inheritdoc
     */
    protected function cpEditUrl(): ?string
    {
        return UrlHelper::actionUrl('elements/edit', [
            'elementId' => $this->id,
            'siteId' => $this->siteId,
        ]);
    }

    /**
     * @inheritdoc
     * @return NonPropagatedTagQuery
     */
    public static function find(): NonPropagatedTagQuery
    {
        return new NonPropagatedTagQuery(static::class);
    }

    /**
     * @inheritdoc
     */
    public function getSupportedSites(): array
    {
        // Save tag only to current site
        return [
            [
                'siteId' => $this->siteId,
                'propagate' => true,
                'enabledByDefault' => true,
            ],
        ];
    }
}
