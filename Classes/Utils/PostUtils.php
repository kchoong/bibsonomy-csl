<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

use AcademicPuma\RestClient\Config\Entrytype;
use AcademicPuma\RestClient\Model\Posts;

class PostUtils
{
    private static $TYPE_MAP = [
        Entrytype::BOOK => 'Book (Book or Conference Proceedings)',
        Entrytype::ARTICLE => 'Article (in journal, newspaper, or magazine)',
        Entrytype::INPROCEEDINGS => 'Article in Conference Proceedings',
        Entrytype::PHDTHESIS => 'Thesis (PhD Thesis and Master Thesis)',
        Entrytype::INCOLLECTION => 'Chapter (part of a book)',
        Entrytype::TECHREPORT => 'Report',
        Entrytype::PATENT => 'Patent',
        Entrytype::STANDARD => 'Legislation',
        Entrytype::BOOKLET => 'Pamphlet',
        Entrytype::PRESENTATION => 'Presentation',
        Entrytype::ELECTRONIC => 'Web page',
        Entrytype::PREPRINT => 'Manuscript',
        Entrytype::UNPUBLISHED => 'Unpublished',
        Entrytype::MISC => 'Misc'
    ];

    public static $DEFAULT_TYPE_ORDER = [
        Entrytype::BOOK,
        Entrytype::ARTICLE,
        Entrytype::INPROCEEDINGS,
        Entrytype::PHDTHESIS,
        Entrytype::INCOLLECTION,
        Entrytype::TECHREPORT,
        Entrytype::PATENT,
        Entrytype::STANDARD,
        Entrytype::BOOKLET,
        Entrytype::PRESENTATION,
        Entrytype::ELECTRONIC,
        Entrytype::PREPRINT,
        Entrytype::UNPUBLISHED,
        Entrytype::MISC
    ];

    public static $TYPE_MAP_EXTEND = [
        Entrytype::BOOK => [Entrytype::BOOK, Entrytype::PROCEEDINGS, Entrytype::PERIODICAL],
        Entrytype::ARTICLE => [Entrytype::ARTICLE],
        Entrytype::INPROCEEDINGS => [Entrytype::INPROCEEDINGS, Entrytype::CONFERENCE],
        Entrytype::PHDTHESIS => [Entrytype::PHDTHESIS, Entrytype::MASTERTHESIS],
        Entrytype::INCOLLECTION => [Entrytype::INCOLLECTION, Entrytype::INBOOK],
        Entrytype::TECHREPORT => [Entrytype::TECHREPORT],
        Entrytype::PATENT => [Entrytype::PATENT],
        Entrytype::STANDARD => [Entrytype::STANDARD],
        Entrytype::BOOKLET => [Entrytype::BOOKLET],
        Entrytype::PRESENTATION => [Entrytype::PRESENTATION],
        Entrytype::ELECTRONIC => [Entrytype::ELECTRONIC],
        Entrytype::PREPRINT => [Entrytype::PREPRINT],
        Entrytype::UNPUBLISHED => [Entrytype::UNPUBLISHED],
        Entrytype::MISC => [Entrytype::MISC]
    ];

    private static $TYPE_MAP_CSL = [
        Entrytype::BOOK => Entrytype::BOOK,
        Entrytype::PROCEEDINGS => Entrytype::BOOK,
        Entrytype::PERIODICAL => Entrytype::BOOK,
        Entrytype::ARTICLE => Entrytype::ARTICLE,
        Entrytype::INPROCEEDINGS => Entrytype::INPROCEEDINGS,
        Entrytype::CONFERENCE => Entrytype::INPROCEEDINGS,
        Entrytype::PHDTHESIS => Entrytype::PHDTHESIS,
        Entrytype::MASTERTHESIS => Entrytype::PHDTHESIS,
        Entrytype::INCOLLECTION => Entrytype::INCOLLECTION,
        Entrytype::INBOOK => Entrytype::INCOLLECTION,
        Entrytype::TECHREPORT => Entrytype::TECHREPORT,
        Entrytype::PATENT => Entrytype::PATENT,
        Entrytype::STANDARD => Entrytype::STANDARD,
        Entrytype::BOOKLET => Entrytype::BOOKLET,
        Entrytype::PRESENTATION => Entrytype::PRESENTATION,
        Entrytype::ELECTRONIC => Entrytype::ELECTRONIC,
        Entrytype::PREPRINT => Entrytype::PREPRINT,
        Entrytype::UNPUBLISHED => Entrytype::UNPUBLISHED,
        Entrytype::MISC => Entrytype::MISC
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

    public static function areBibtexTypes(array $types): bool
    {
        foreach ($types as $type) {
            if (!PostUtils::isBibTexType($type)) {
                return false;
            }
        }

        return true;
    }

    public static function getTypeOfType(string $type): string
    {
        if (array_key_exists($type, self::$TYPE_MAP_CSL)) {
            return self::$TYPE_MAP_CSL[$type];
        }

        return $type;
    }

    public static function isBibTexType(string $type): bool
    {
        return array_key_exists($type, self::$TYPE_MAP_CSL);
    }

    public static function isDefaultEntrytype(string $type): bool
    {
        return in_array($type, self::$DEFAULT_TYPE_ORDER);
    }

    public static function getTypeTitle(string $type): string
    {
        return self::$TYPE_MAP[self::getTypeOfType($type)];
    }

}