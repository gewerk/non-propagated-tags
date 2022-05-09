<?php

/**
 * @link https://gewerk.dev/plugins/non-propagated-tags
 * @copyright 2022 gewerk, Dennis Morhardt
 * @license https://github.com/gewerk/non-propagated-tags/blob/main/LICENSE.md
 */

namespace Gewerk\NonPropagatedTags\Element\Query;

use craft\db\Connection;
use craft\elements\db\TagQuery;
use Gewerk\NonPropagatedTags\Element\NonPropagatedTag;

/**
 * Query class for non-propagated tags
 *
 * @method NonPropagatedTag[]|array all($db = null)
 * @method NonPropagatedTag|array|null one($db = null)
 * @method NonPropagatedTag|array|null nth(int $n, Connection $db = null)
 * @package Gewerk\NonPropagatedTags\Element\Query
 */
class NonPropagatedTagQuery extends TagQuery
{
}
