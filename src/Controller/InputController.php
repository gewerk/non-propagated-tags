<?php

/**
 * @link https://gewerk.dev/plugins/non-propagated-tags
 * @copyright 2022 gewerk, Dennis Morhardt
 * @license https://github.com/gewerk/non-propagated-tags/blob/main/LICENSE.md
 */

namespace Gewerk\NonPropagatedTags\Controller;

use Craft;
use craft\helpers\Db;
use craft\helpers\Search;
use craft\helpers\StringHelper;
use craft\web\Controller;
use Gewerk\NonPropagatedTags\Element\NonPropagatedTag;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Actions for non-propagated tags input field
 *
 * @package Gewerk\NonPropagatedTags\Controller
 */
class InputController extends Controller
{
    /**
     * Searches for tags
     *
     * @return Response
     */
    public function actionSearchForTags(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $search = trim($this->request->getBodyParam('search'));
        $tagGroupId = $this->request->getBodyParam('tagGroupId');
        $siteId = $this->request->getBodyParam('siteId');
        $excludeIds = $this->request->getBodyParam('excludeIds', []);
        $allowSimilarTags = Craft::$app->getConfig()->getGeneral()->allowSimilarTags;

        /** @var NonPropagatedTag[] $tags */
        $tags = NonPropagatedTag::find()
            ->siteId($siteId)
            ->groupId($tagGroupId)
            ->title(Db::escapeParam($search) . '*')
            ->orderBy(['LENGTH([[title]])' => SORT_ASC])
            ->limit(5)
            ->all();

        $return = [];
        $exactMatches = [];
        $excludes = [];
        $tagTitleLengths = [];
        $exactMatch = false;

        if ($allowSimilarTags) {
            $search = Search::normalizeKeywords($search, [], false);
        } else {
            $search = Search::normalizeKeywords($search);
        }

        foreach ($tags as $tag) {
            $exclude = in_array($tag->id, $excludeIds, false);

            $return[] = [
                'id' => $tag->id,
                'title' => $tag->title,
                'exclude' => $exclude,
            ];

            $tagTitleLengths[] = StringHelper::length($tag->title);

            if ($allowSimilarTags) {
                $title = Search::normalizeKeywords($tag->title, [], false);
            } else {
                $title = Search::normalizeKeywords($tag->title);
            }

            if ($title == $search) {
                $exactMatches[] = 1;
                $exactMatch = true;
            } else {
                $exactMatches[] = 0;
            }

            $excludes[] = $exclude ? 1 : 0;
        }

        array_multisort($excludes, SORT_ASC, $exactMatches, SORT_DESC, $tagTitleLengths, $return);

        return $this->asJson([
            'tags' => $return,
            'exactMatch' => $exactMatch,
        ]);
    }

    /**
     * Creates a new tag
     *
     * @return Response
     * @throws BadRequestHttpException if the groupId param is missing or invalid
     */
    public function actionCreateTag(): Response
    {
        $this->requireAcceptsJson();

        $siteId = $this->request->getRequiredBodyParam('siteId');
        if (($site = Craft::$app->getSites()->getSiteById($siteId)) === null) {
            throw new BadRequestHttpException('Invalid site ID: ' . $siteId);
        }

        $groupId = $this->request->getRequiredBodyParam('groupId');
        if (($group = Craft::$app->getTags()->getTagGroupById($groupId)) === null) {
            throw new BadRequestHttpException('Invalid tag group ID: ' . $groupId);
        }

        $tag = new NonPropagatedTag();
        $tag->siteId = $site->id;
        $tag->groupId = $group->id;
        $tag->title = trim($this->request->getRequiredBodyParam('title'));

        // Don't validate required custom fields
        if (!Craft::$app->getElements()->saveElement($tag)) {
            return $this->asFailure();
        }

        return $this->asSuccess(data: [
            'id' => $tag->id,
        ]);
    }
}
