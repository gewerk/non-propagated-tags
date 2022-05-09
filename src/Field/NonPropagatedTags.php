<?php

/**
 * @link https://gewerk.dev/plugins/non-propagated-tags
 * @copyright 2022 gewerk, Dennis Morhardt
 * @license https://github.com/gewerk/non-propagated-tags/blob/main/LICENSE.md
 */

namespace Gewerk\NonPropagatedTags\Field;

use Craft;
use craft\base\ElementInterface;
use craft\elements\db\ElementQueryInterface;
use craft\fields\Tags;
use craft\models\TagGroup;
use Gewerk\NonPropagatedTags\AssetBundle\NonPropagatedTagSelectInputAssetBundle;
use Gewerk\NonPropagatedTags\Element\NonPropagatedTag;
use Gewerk\NonPropagatedTags\Element\Query\NonPropagatedTagQuery;

/**
 * Extended tags field for non-propagated tags
 *
 * @package Gewerk\NonPropagatedTags\Field
 */
class NonPropagatedTags extends Tags
{
    /**
     * @var string|null
     */
    private $tagGroupId = null;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('non-propagated-tags', 'Non-Propagated Tags');
    }

    /**
     * @inheritdoc
     */
    protected static function elementType(): string
    {
        return NonPropagatedTag::class;
    }

    /**
     * @inheritdoc
     */
    public static function valueType(): string
    {
        return NonPropagatedTagQuery::class;
    }

    /**
     * @inheritdoc
     */
    protected function inputHtml($value, ElementInterface $element = null): string
    {
        if ($element !== null && $element->hasEagerLoadedElements($this->handle)) {
            $value = $element->getEagerLoadedElements($this->handle);
        }

        if ($value instanceof ElementQueryInterface) {
            $value = $value
                ->anyStatus()
                ->all();
        } elseif (!is_array($value)) {
            $value = [];
        }

        $tagGroup = $this->getTagGroup();

        if ($tagGroup) {
            $view = Craft::$app->getView();
            $view->registerAssetBundle(NonPropagatedTagSelectInputAssetBundle::class);

            return $view->renderTemplate(
                'non-propagated-tags/input',
                [
                    'elementType' => static::elementType(),
                    'id' => $this->getInputId(),
                    'name' => $this->handle,
                    'elements' => $value,
                    'tagGroupId' => $tagGroup->id,
                    'targetSiteId' => $this->targetSiteId($element),
                    'sourceElementId' => $element !== null ? $element->id : null,
                    'selectionLabel' => $this->selectionLabel ?
                        Craft::t('site', $this->selectionLabel) :
                        static::defaultSelectionLabel(),
                ]
            );
        }

        return '<p class="error">' . Craft::t('app', 'This field is not set to a valid source.') . '</p>';
    }

    /**
     * Returns the tag group associated with this field.
     *
     * @return TagGroup|null
     */
    private function getTagGroup()
    {
        $tagGroupId = $this->getTagGroupId();

        if ($tagGroupId !== false) {
            return Craft::$app->getTags()->getTagGroupByUid($tagGroupId);
        }

        return null;
    }

    /**
     * Returns the tag group ID this field is associated with.
     *
     * @return int|false
     */
    private function getTagGroupId()
    {
        if ($this->tagGroupId !== null) {
            return $this->tagGroupId;
        }

        if (!preg_match('/^taggroup:([0-9a-f\-]+)$/', $this->source, $matches)) {
            return $this->tagGroupId = false;
        }

        return $this->tagGroupId = $matches[1];
    }
}
