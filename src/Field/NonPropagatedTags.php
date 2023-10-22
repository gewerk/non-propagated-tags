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
use craft\elements\ElementCollection;
use craft\fields\Tags;
use craft\models\TagGroup;
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
     * @var string|false
     */
    private string|false $tagGroupUid;

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
    public static function elementType(): string
    {
        return NonPropagatedTag::class;
    }

    /**
     * @inheritdoc
     */
    public static function valueType(): string
    {
        return sprintf('\\%s|\\%s<\\%s>', NonPropagatedTagQuery::class, ElementCollection::class, NonPropagatedTag::class);
    }

    /**
     * @inheritdoc
     */
    protected function inputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        if ($element !== null && $element->hasEagerLoadedElements($this->handle)) {
            $value = $element->getEagerLoadedElements($this->handle)->all();
        }

        if ($value instanceof ElementQueryInterface) {
            $value = $value
                ->status(null)
                ->all();
        } elseif (!is_array($value)) {
            $value = [];
        }

        $tagGroup = $this->getTagGroup();

        if ($tagGroup) {
            return Craft::$app->getView()->renderTemplate(
                'non-propagated-tags/input.twig',
                [
                    'elementType' => static::elementType(),
                    'id' => $this->getInputId(),
                    'describedBy' => $this->describedBy,
                    'name' => $this->handle,
                    'elements' => $value,
                    'tagGroupId' => $tagGroup->id,
                    'siteId' => $this->targetSiteId($element),
                    'sourceElementId' => $element?->id,
                    'selectionLabel' => $this->selectionLabel ? Craft::t('site', $this->selectionLabel) : static::defaultSelectionLabel(),
                    'allowSelfRelations' => (bool)$this->allowSelfRelations,
                ],
            );
        }

        return '<p class="error">' . Craft::t('app', 'This field is not set to a valid source.') . '</p>';
    }

    /**
     * Returns the tag group associated with this field.
     *
     * @return TagGroup|null
     */
    private function getTagGroup(): ?TagGroup
    {
        $groupUid = $this->getTagGroupUid();

        return $groupUid ? Craft::$app->getTags()->getTagGroupByUid($groupUid) : null;
    }

    /**
     * Returns the tag group ID this field is associated with.
     *
     * @return string|null
     */
    private function getTagGroupUid(): ?string
    {
        if (!isset($this->tagGroupUid)) {
            if (preg_match('/^taggroup:([0-9a-f\-]+)$/', $this->source, $matches)) {
                $this->tagGroupUid = $matches[1];
            } else {
                $this->tagGroupUid = false;
            }
        }

        return $this->tagGroupUid ?: null;
    }
}
