<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

use AcademicPuma\RestClient\RESTClient;

class ApiUtils
{
    public static function getRestClient($accessor, array $settings): RESTClient
    {
        if ($settings['auth']['ssl'] != 'path') {
            return new RESTClient($accessor, ['verify' => filter_var($settings['auth']['ssl'], FILTER_VALIDATE_BOOLEAN)]);
        } else {
            return new RESTClient($accessor, ['verify' => $settings['auth']['sslPath']]);
        }
    }
}