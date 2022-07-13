<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

use AcademicPuma\RestClient\Config\Entrytype;
use AcademicPuma\RestClient\Model\Posts;


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
 * PostUtils
 */
class PostUtils
{
    public static $DEFAULT_ENTRYTYPES = [
        Entrytype::ARTICLE,
        Entrytype::BOOK,
        Entrytype::BOOKLET,
        Entrytype::COLLECTION,
        Entrytype::CONFERENCE,
        Entrytype::DATASET,
        Entrytype::ELECTRONIC,
        Entrytype::INBOOK,
        Entrytype::INCOLLECTION,
        Entrytype::INPROCEEDINGS,
        Entrytype::MANUAL,
        Entrytype::MASTERTHESIS,
        Entrytype::MISC,
        Entrytype::PATENT,
        Entrytype::PERIODICAL,
        Entrytype::PHDTHESIS,
        Entrytype::PREAMBLE,
        Entrytype::PREPRINT,
        Entrytype::PRESENTATION,
        Entrytype::PROCEEDINGS,
        Entrytype::STANDARD,
        Entrytype::TECHREPORT,
        Entrytype::UNPUBLISHED,
    ];

    /**
     * transforms publications list to multidimensional Posts Array List:
     * 1st dimension represents the year of the data content
     *
     * @param Posts $posts pre-sorted (by year) publication list
     */
    public static function transformToYearGroupedArray(Posts &$posts)
    {
        $groupedPosts = [];
        if (!empty($posts->toArray())) {
            $year = $posts[0]->getResource()->getYear();
            foreach ($posts as $post) {
                if ($post->getResource()->getYear() !== $year) {
                    $year = $post->getResource()->getYear();
                }
                $groupedPosts[$year][] = $post;
            }
            $posts->setArray($groupedPosts);
        }
    }

    public static function isDefaultEntrytype(string $type): bool
    {
        return in_array($type, self::$DEFAULT_ENTRYTYPES);
    }

}