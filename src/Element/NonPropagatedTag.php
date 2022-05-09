<?php

/**
 * @link https://gewerk.dev/plugins/non-propagated-tags
 * @copyright 2022 gewerk, Dennis Morhardt
 * @license https://github.com/gewerk/non-propagated-tags/blob/main/LICENSE.md
 */

namespace Gewerk\NonPropagatedTags\Element;

use craft\elements\db\ElementQueryInterface;
use craft\elements\Tag;
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
     * @return NonPropagatedTagQuery
     */
    public static function find(): ElementQueryInterface
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
            'siteId' => $this->siteId,
            'propagate' => true,
            'enabledByDefault' => true,
        ];
    }
}
