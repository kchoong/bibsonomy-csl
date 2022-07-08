<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Domain\Model;


use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * This file is part of the "BibSonomy CSL" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Kevin Choong <choong.kvn@gmail.com>
 *          Sebastian Böttger <boettger@cs.uni-kassel.de>
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
