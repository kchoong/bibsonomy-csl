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

// plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
$publicationListPluginSignature = 'bibsonomycsl_publicationlist';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$publicationListPluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $publicationListPluginSignature,
    // Flexform configuration schema file
    'FILE:EXT:bibsonomy_csl/Configuration/FlexForms/flexform_publicationlist.xml'
);

$tagCloudPluginSignature = 'bibsonomycsl_tagcloud';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$tagCloudPluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $tagCloudPluginSignature,
    // Flexform configuration schema file
    'FILE:EXT:bibsonomy_csl/Configuration/FlexForms/flexform_tagcloud.xml'
);