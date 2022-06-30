<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

use TYPO3\CMS\Core\Exception;

class URLUtils
{
    const URL_PATTERN = '#((https?|ftp):\/\/(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i';

    public static function isUrl($string): bool
    {
        $match = preg_match(self::URL_PATTERN, $string);

        return $match > 0;
    }

    public static function getDOIUrl(string $doi): string
    {
        // Check, if already a DOI URL
        if (strpos($doi, 'doi.org') !== false) {
            return $doi;
        } else {
            return 'https://dx.doi.org/' . urlencode($doi);
        }
    }

    public static function getURNUrl(string $urn): string
    {
        return 'https://nbn-resolving.org/' . urlencode($urn);
    }
}