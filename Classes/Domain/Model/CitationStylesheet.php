<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Domain\Model;


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
 * Represents the XML source of a citation stylesheet (CSL).
 */
class CitationStylesheet extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected $name = '';
    protected $xmlSource = '';

    /**
     * @param string $name
     * @param string $xmlSource
     */
    public function __construct(string $name, string $xmlSource)
    {
        $this->name = $name;
        $this->xmlSource = $xmlSource;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getXmlSource(): string
    {
        return $this->xmlSource;
    }

    /**
     * @param string $xmlSource
     */
    public function setXmlSource(string $xmlSource): void
    {
        $this->xmlSource = $xmlSource;
    }

}
