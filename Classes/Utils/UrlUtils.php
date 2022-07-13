<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

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
 * UrlUtils
 */
class UrlUtils
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