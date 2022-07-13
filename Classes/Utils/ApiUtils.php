<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

use AcademicPuma\RestClient\RESTClient;

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
 * ApiUtils
 */
class ApiUtils
{
    public static function getRestClient($accessor, array $settings): RESTClient
    {
        if ($settings['auth']['ssl'] != 'path') {
            return new RESTClient($accessor, ['verify' => filter_var($settings['auth']['ssl'], FILTER_VALIDATE_BOOLEAN)]);
        } else {
            return new RESTClient($accessor, ['verify' => $settings['auth']['sslPath']]);
        }
    }
}