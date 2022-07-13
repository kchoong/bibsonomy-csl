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
 * AuthorsViewHelper
 */
class AuthorsViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('post', '\AcademicPuma\RestClient\Model\Post', 'The post to render as citation', true);
        $this->registerArgument('authorLinks', 'array', 'The list of personal author links', true);
        $this->registerArgument('mode', 'string', 'Link or Snippet', true);
    }

    public static function renderStatic(
        array                     $arguments,
        Closure                   $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $post = $arguments['post'];
        $authorLinks = $arguments['authorLinks'];
        $mode = $arguments['mode'];

        $authors = explode(' and ', $post->getResource()->getAuthor());
        $exisitingAuthors = [];
        foreach ($authors as $author) {
            if (array_key_exists($author, $authorLinks)) {
                $exisitingAuthors[] = $author;
            }
        }

        if (count($exisitingAuthors) > 0) {
            $intrahash = $post->getResource()->getIntraHash();
            $snippetId = "bibsonomy-authors-$intrahash";

            if ($mode == 'snippet') {
                $table = new TagBuilder('table');
                $tableRows = [];
                $tableRows[] = "<tr><th>Name</th><th>Website</th><th>E-Mail</th></tr>";
                foreach ($exisitingAuthors as $author) {
                    $tableRows[] = "<tr><td>{$author}</td><td>{$authorLinks[$author]['url']}</td><td>{$authorLinks[$author]['email']}</td></tr>";
                }
                $table->setContent(implode('', $tableRows));
                return self::createSnippet($snippetId, $table->render());
            } else {
                $postId = "bibsonomy-post-$intrahash";
                $label = LocalizationUtility::translate('post.snippet.authors', 'BibsonomyCsl');
                return self::createLink($postId, $snippetId, $label);
            }
        }

        return '';
    }

    static private function createLink(string $postId, string $snippetId, string $label): string
    {
        $link = new TagBuilder('a');
        $link->addAttribute('href', "#$postId");
        $link->addAttribute('onclick', "toggleSnippet(this)");
        $link->addAttribute('data-post', $postId);
        $link->addAttribute('data-snippet', $snippetId);
        $link->setContent($label);

        $button = new TagBuilder('li');
        $button->addAttribute('class', "bibsonomy-link bibsonomy-link-authors");
        $button->setContent('[ ' . $link->render() . ' ]');

        return $button->render();
    }

    static private function createSnippet(string $snippetId, string $content): string
    {
        $container = new TagBuilder('div');
        $container->addAttribute('id', $snippetId);
        $container->addAttribute('class', "bibsonomy-snippet bibsonomy-snippet-authors");
        $container->setContent($content);
        return $container->render();
    }

}