<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Domain\Model;


use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

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
 * Authentication
 */
class Authentication extends AbstractEntity
{
    protected $hostAddress = '';
    protected $userName = '';
    protected $apiKey = '';
    protected $accessToken = '';
    protected $oAuthEnabled = false;

    /**
     * @param string $hostAddress
     * @param string $userName
     * @param string $apiKey
     * @param string $accessToken
     * @param bool $oAuthEnabled
     */
    public function __construct(string $hostAddress = '', string $userName = '', string $apiKey = '', string $accessToken = '', bool $oAuthEnabled = false)
    {
        $this->hostAddress = $hostAddress;
        $this->userName = $userName;
        $this->apiKey = $apiKey;
        $this->accessToken = $accessToken;
        $this->oAuthEnabled = $oAuthEnabled;
    }

    /**
     * @return string
     */
    public function getHostAddress(): string
    {
        return $this->hostAddress;
    }

    /**
     * @param string $hostAddress
     */
    public function setHostAddress(string $hostAddress): void
    {
        $this->hostAddress = $hostAddress;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return bool
     */
    public function isOAuthEnabled(): bool
    {
        return $this->oAuthEnabled;
    }

    /**
     * @param bool $oAuthEnabled
     */
    public function setOAuthEnabled(bool $oAuthEnabled): void
    {
        $this->oAuthEnabled = $oAuthEnabled;
    }

}
