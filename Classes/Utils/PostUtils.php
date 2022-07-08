<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

use AcademicPuma\RestClient\Config\Entrytype;
use AcademicPuma\RestClient\Model\Posts;

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