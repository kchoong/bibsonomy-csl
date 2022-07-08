<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.authentication',
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
        'iconfile' => 'EXT:bibsonomy_csl/Resources/Public/Icons/tx_bibsonomycsl_domain_model_authentication.gif'
    ],
    'types' => [
        '0' => ['showitem' => 'uid, cruser_id, host_address, user_name, api_key, access_token, o_auth_enabled'],
    ],
    'columns' => [
        'host_address' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.authentication.hostAddress',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => 'trim'
            ],
        ],
        'user_name' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.authentication.userName',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'api_key' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.authentication.apiKey',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'access_token' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.authentication.accessToken',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => 'trim'
            ],
        ],
        'o_auth_enabled' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.authentication.oAuthEnabled',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
    ],
];
