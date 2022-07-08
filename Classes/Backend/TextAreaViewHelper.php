<?php

namespace AcademicPuma\BibsonomyCsl\Backend;

use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class TextAreaViewHelper
{
    public function render(array &$params): string
    {
        $textarea = new TagBuilder('textarea');
        $textarea->addAttribute('id', "em-bibsonomy_csl-{$params['fieldName']}");
        $textarea->addAttribute('name', $params['fieldName']);
        $textarea->addAttribute('rows', '15');
        $textarea->addAttribute('cols', '60');
        $textarea->setContent($params['fieldValue']);

        return $textarea->render();
    }
}