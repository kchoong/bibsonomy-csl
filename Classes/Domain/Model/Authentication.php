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
    protected $enabledOAuth = false;
    protected $createdDate = 0;

    /**
     * @param string $hostAddress
     * @param string $userName
     * @param string $apiKey
     * @param string $accessToken
     * @param bool $enabledOAuth
     * @param int $createdDate
     */
    public function __construct(string $hostAddress = '', string $userName = '', string $apiKey = '', string $accessToken = '', bool $enabledOAuth = false, int $createdDate = 0)
    {
        $this->hostAddress = $hostAddress;
        $this->userName = $userName;
        $this->apiKey = $apiKey;
        $this->accessToken = $accessToken;
        $this->enabledOAuth = $enabledOAuth;
        $this->createdDate = $createdDate;
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
    public function isEnabledOAuth(): bool
    {
        return $this->enabledOAuth;
    }

    /**
     * @param bool $enabledOAuth
     */
    public function setEnabledOAuth(bool $enabledOAuth): void
    {
        $this->enabledOAuth = $enabledOAuth;
    }

    /**
     * @return int
     */
    public function getCreatedDate(): int
    {
        return $this->createdDate;
    }

    /**
     * @param int $createdDate
     */
    public function setCreatedDate(int $createdDate): void
    {
        $this->createdDate = $createdDate;
    }
}
