<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


use AcademicPuma\BibsonomyCsl\Utils\ApiUtils;
use AcademicPuma\RestClient\Config\Sorting;
use AcademicPuma\RestClient\Config\TagOrder;
use AcademicPuma\RestClient\Config\TagRelation;
use AcademicPuma\RestClient\Model\Tags;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 *  PUMA/BibSonomy CSL (bibsonomy_csl) is a TYPO3 extension which
 *  enables users to render publication lists from PUMA or BibSonomy in
 *  various styles.
 *
 *  Copyright notice
 * (c) 2022 Kevin Choong <choong.kvn@gmail.com>
 *          Sebastian Böttger <boettger@cs.uni-kassel.de>
 *
 *  HothoData GmbH (http://www.academic-puma.de)
 *  Knowledge and Data Engineering Group (University of Kassel)
 *
 *  All rights reserved
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * TagController
 */
class TagController extends ApiActionController
{

    /**
     * action list
     *
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        // create API accessor
        $this->makeAccessor();

        // get tags
        $tags = $this->getTags($this->settings['tags']);

        // filter & sort tags
        $this->filterByBlacklist($tags, $this->settings['tags']);
        $tags->sort('name', Sorting::ORDER_ASC);

        // get max used count for tags to determine size
        if ($tags->count() > 0) {
            $maxcount = 0;
            foreach ($tags as $tag) {
                $count = $tag->getUsercount();
                $maxcount = ($count > $maxcount) ? $count : $maxcount;
                $this->view->assign('maxcount', $maxcount);
            }
        }

        // assign tags
        $this->view->assign('tags', $tags);

        return $this->htmlResponse();
    }

    public function getTags(array $settings): Tags
    {
        // create REST client
        $client = ApiUtils::getRestClient($this->accessor, $settings);

        $grouping = $settings['sourceType'];
        $groupingName = $settings['sourceId'];
        $relatedTags = $settings['related'] ? explode(" ", trim($settings['related'])) : [];
        $limit = intval($settings['maxcount']);

        if (count($relatedTags) > 0) {
            try {
                $client->getTagRelation($grouping, $groupingName, TagRelation::RELATED, $relatedTags,
                    TagOrder::FREQUENCY, 0, $limit);
                $result = $client->model()->toArray();
            } catch (BadResponseException|GuzzleException|Exception $e) {
                return new Tags();
            }
        } else {
            try {
                $client->getTags($grouping, $groupingName, '', TagOrder::FREQUENCY, 0, $limit);
                $result = $client->model()->toArray();
            } catch (BadResponseException|GuzzleException|Exception $e) {
                return new Tags();
            }
        }

        return new Tags($result);
    }

    private function filterByBlacklist(Tags &$tags, array $settings): void
    {
        $blacklistTags = $settings['blacklist'] ? explode(" ", trim($settings['blacklist'])) : [];

        foreach ($tags as $key => $tag) {
            if (in_array($tag->getName(), $blacklistTags)) {
                unset($tags[$key]);
            }
        }
    }

}
