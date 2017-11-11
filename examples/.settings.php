<?php
return [
    'utf_mode'           =>
        [
            'value'    => true,
            'readonly' => true,
        ],
    'cache_flags'        =>
        [
            'value'    =>
                [
                    'config_options' => 3600,
                    'site_domain'    => 3600,
                ],
            'readonly' => false,
        ],
    'cookies'            =>
        [
            'value'    =>
                [
                    'secure'    => false,
                    'http_only' => true,
                ],
            'readonly' => false,
        ],
    'exception_handling' =>
        [
            'value'    =>
                [
                    'debug'                      => true,
                    'handled_errors_types'       => 4437,
                    'exception_errors_types'     => 4437,
                    'ignore_silence'             => false,
                    'assertion_throws_exception' => true,
                    'assertion_error_type'       => 256,
                    'log'                        => null,
                ],
            'readonly' => false,
        ],
    'connections'        =>
        [
            'value'    =>
                [
                    'default' =>
                        [
                            'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
                            'host'      => 'localhost',
                            'database'  => 'заменить на название базы данных',
                            'login'     => 'заменить на имя пользователя базы данных',
                            'password'  => 'заменить на пароль пользователя базы данных',
                            'options'   => 2,
                        ],
                ],
            'readonly' => true,
        ],
];
