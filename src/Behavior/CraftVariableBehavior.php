<?php

/**
 * @link https://gewerk.dev/plugins/non-propagated-tags
 * @copyright 2022 gewerk, Dennis Morhardt
 * @license https://github.com/gewerk/non-propagated-tags/blob/main/LICENSE.md
 */

namespace Gewerk\NonPropagatedTags\Behavior;

use Craft;
use craft\web\twig\variables\CraftVariable;
use Gewerk\NonPropagatedTags\Element\NonPropagatedTag;
use Gewerk\NonPropagatedTags\Element\Query\NonPropagatedTagQuery;
use yii\base\Behavior;

/**
 * Extends the element with external ID properties and methods
 *
 * @property CraftVariable $owner
 * @package Gewerk\NonPropagatedTags\Behavior
 */
class CraftVariableBehavior extends Behavior
{
    /**
     * Returns a new non-propagated tag query
     *
     * @param array $criteria
     * @return NonPropagatedTagQuery
     */
    public function nonPropagatedTags(array $criteria = []): NonPropagatedTagQuery
    {
        $query = NonPropagatedTag::find();
        Craft::configure($query, $criteria);

        return $query;
    }
}
