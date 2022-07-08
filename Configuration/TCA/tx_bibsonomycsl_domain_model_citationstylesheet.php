<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.citationstylesheet',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => '',
        'iconfile' => 'EXT:bibsonomy_csl/Resources/Public/Icons/tx_bibsonomycsl_domain_model_citationstylesheet.gif'
    ],
    'types' => [
        '0' => ['showitem' => 'uid, cruser_id, name, xml_source'],
    ],
    'columns' => [
        'name' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.citationstylesheet.name',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => 'trim'
            ],
        ],
        'xml_source' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.citationstylesheet.xmlSource',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
            ],
        ]
    ],
];
