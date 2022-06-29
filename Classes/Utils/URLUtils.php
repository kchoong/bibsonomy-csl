<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

use AcademicPuma\RestClient\Model\Post;

class URLUtils
{

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