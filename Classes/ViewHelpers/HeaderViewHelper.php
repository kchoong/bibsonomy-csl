<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use Closure;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

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
 * HeaderViewHelper
 */
class HeaderViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('group', 'string', 'The group name', true);
        $this->registerArgument('listHash', 'string', 'The list hash', true);
        $this->registerArgument('groupingKey', 'string', 'The grouping key', true);
        $this->registerArgument('entrytypes', 'array', 'The entrytypes in BibTeX format and their labels', true);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $group = $arguments['group'];
        $listHash = $arguments['listHash'];
        $groupingKey = $arguments['groupingKey'];
        $entrytypes = $arguments['entrytypes'];

        $labelContent = $groupingKey == 'entrytype' ? self::getEntrytypeLabel($group, $entrytypes): $group;
        $label = new TagBuilder('span');
        $label->addAttribute('class', 'bibsonomy-group-name');
        $label->setContent($labelContent);

        $link = new TagBuilder('a');
        $link->addAttribute('href', "#bibsonomy-container-$listHash");
        $link->setContent(LocalizationUtility::translate('post.links.toTop', 'BibsonomyCsl'));

        $button = new TagBuilder('span');
        $button->addAttribute('class', 'bibsonomy-group-jumper');
        $button->setContent("[ {$link->render()} ]");

        $header = new TagBuilder('div');
        $header->addAttribute('class', 'bibsonomy-group-header');
        $header->addAttribute('id', "posts_$group");
        $header->setContent($label->render() . $button->render());

        return $header->render();
    }

    private static function getEntrytypeLabel(string $entrytype, array $entrytypes): string
    {
        if (array_key_exists($entrytype, $entrytypes)) {
            return $entrytypes[$entrytype]['label'];
        }

        return $entrytype;
    }

}