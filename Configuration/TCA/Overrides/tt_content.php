<?php
defined('TYPO3') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'BibsonomyCsl',
    'PublicationList',
    'Publication List',
    'EXT:bibsonomy_csl/Resources/Public/Icons/Extension.svg'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'BibsonomyCsl',
    'TagCloud',
    'Tag Cloud',
    'EXT:bibsonomy_csl/Resources/Public/Icons/Extension.svg'
);
