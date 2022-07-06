<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\RestClient\Model\Tag;
use Closure;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class HeaderViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('group', 'string', 'The group name', true);
        $this->registerArgument('listHash', 'string', 'The list hash', true);
        $this->registerArgument('groupingKey', 'string', 'The grouping key', true);
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

        $labelContent = $groupingKey == 'entrytype' ?
            LocalizationUtility::translate("entrytype.$group", 'BibsonomyCsl') :
            $group;
        $label = new TagBuilder('span');
        $label->addAttribute('class', 'bibsonomy-group-name');
        $label->setContent($labelContent);

        $link = new TagBuilder('a');
        $link->addAttribute('href', "#bibsonomy-container-$listHash");
        $link->addAttribute('target', '_blank');
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

}