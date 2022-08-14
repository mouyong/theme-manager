<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

return [
    'multi' => env('THEME_MULTI', false),
    
    'namespace' => $themesNamespace = 'Themes',

    // YOU COULD CUSTOM HERE
    'namespaces' => [
        $themesNamespace => [
            base_path('extensions/themes'),
        ],
    ],

    'autoload_files' => [
        // base_path('demo.php'),
    ],

    'merge_plugin_config' => [
        'include' => [
            ltrim(str_replace(base_path(), '', base_path('extensions/themes/*/composer.json')), '/'),
        ],
        'recurse' => true,
        'replace' => false,
        'ignore-duplicates' => false,
        'merge-dev' => true,
        'merge-extra' => true,
        'merge-extra-deep' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | YOU COULD CUSTOM HERE
    |
    */
    'composer'  => [
        'vendor' => 'fresns',
        'author' => [
            [
                'name'  => 'Jarvis Tang',
                'email' => 'jarvis.okay@gmail.com',
                'homepage' => 'https://github.com/jarvis-tang',
                'role' => 'Creator',
            ],
            [
                'name'  => 'mouyong',
                'email' => 'my24251325@gmail.com',
                'homepage' => 'https://github.com/mouyong',
                'role' => 'Developer',
            ],
        ],
    ],

    'paths' => [
        'base' => base_path('extensions'),
        'backups' => base_path('extensions/backups/themes'),
        'themes' => base_path('extensions/themes'),
        'assets' => public_path('assets/themes'),

        'generator' => [
            'provider'          => ['path' => 'app/Providers', 'generate' => true, 'in_multi' => true],
            'assets'            => ['path' => 'assets', 'generate' => true, 'in_multi' => false],
            'lang'              => ['path' => 'lang', 'generate' => true, 'in_multi' => true],
            'views'             => ['path' => 'views', 'generate' => true, 'in_multi' => true],
        ],
    ],

    'stubs' => [
        'path'         => dirname(__DIR__).'/src/Commands/stubs',
        'files'        => [
            'scaffold/provider'     => 'app/Providers/$STUDLY_NAME$ServiceProvider.php',
            'assets/js/app'         => 'assets/js/app.js',
            'assets/sass/app'       => 'assets/sass/app.scss',
            'composer.json'         => 'composer.json',
            'theme.json'            => 'theme.json',
            'readme'                => 'README.md',
        ],
        'gitkeep'      => true,
    ],

    'manager' => [
        'default' => [
            'file' => base_path('fresns.json'),
        ],
    ],
];
