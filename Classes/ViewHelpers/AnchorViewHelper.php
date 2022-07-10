<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use Closure;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class AnchorViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('group', 'string', 'The group name', true);
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
        $groupingKey = $arguments['groupingKey'];
        $entrytypes = $arguments['entrytypes'];

        $label= $groupingKey == 'entrytype' ? self::getEntrytypeLabel($group, $entrytypes): $group;

        $link = new TagBuilder('a');
        $link->addAttribute('href', "#posts_$group");
        $link->addAttribute('target', '_blank');
        $link->addAttribute('onclick', 'resetFilterPosts()');
        $link->setContent($label);

        $button = new TagBuilder('li');
        $button->addAttribute('class', 'bibsonomy-link bibsonomy-link-anchor');
        $button->setContent('[ ' . $link->render() . ' ]');

        return $button->render();
    }

    private static function getEntrytypeLabel(string $entrytype, array $entrytypes): string
    {
        if (array_key_exists($entrytype, $entrytypes)) {
            return $entrytypes[$entrytype]['label'];
        }

        return $entrytype;
    }

}