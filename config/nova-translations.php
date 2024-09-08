<?php

return [
    'resource' => \Ferdiunal\NovaTranslations\Nova\TranslationResource::class,

    /**
     * The model to use for translations.
     */
    'model' => \Ferdiunal\NovaTranslations\Models\Translation::class,

    /**
     * The path to the directory containing the language files.
     */
    'paths' => [
        app_path(), resource_path('views'), base_path('vendor'),
    ],

    'excludedPaths' => [],

    // See https://regex101.com/r/jS5fX0/4
    'patternA' => implode('', [
        '[^\w]', // Must not start with any alphanum or _
        '(?<!->)', // Must not start with ->
        '('.implode('|', [
            'trans',
            'trans_choice',
            'Lang::get',
            'Lang::choice',
            'Lang::trans',
            'Lang::transChoice',
            '@lang',
            '@choice',
            '__',
        ]),
        ')', // Must start with one of the functions
        "\(", // Match opening parentheses
        "[\'\"]", // Match " or '
        '(', // Start a new group to match:
        '([a-zA-Z0-9_\/-]+::)?',
        '[a-zA-Z0-9_-]+', // Must start with group
        "([.][^\1)$]+)+", // Be followed by one or more items/keys
        ')', // Close group
        "[\'\"]", // Closing quote
        "[\),]",  // Close parentheses or new parameter
    ]),

    'patternB' => implode('', [
        // See https://regex101.com/r/2EfItR/2
        '[^\w]', // Must not start with any alphanum or _
        '(?<!->)', // Must not start with ->
        '(__|Lang::getFromJson)', // Must start with one of the functions
        '\(', // Match opening parentheses
        '[\"]', // Match "
        '(', // Start a new group to match:
        '[^"]+', //Can have everything except "
        //            '(?:[^"]|\\")+' . //Can have everything except " or can have escaped " like \", however it is not working as expected
        ')', // Close group
        '[\"]', // Closing quote
        '[\)]',  // Close parentheses or new parameter
    ]),

    'patternC' => implode('', [
        // See https://regex101.com/r/VaPQ7A/2
        '[^\w]', // Must not start with any alphanum or _
        '(?<!->)', // Must not start with ->
        '(__|Lang::getFromJson)', // Must start with one of the functions
        '\(', // Match opening parentheses
        '[\']', // Match '
        '(', // Start a new group to match:
        "[^']+", //Can have everything except '
        //            "(?:[^']|\\')+" . //Can have everything except 'or can have escaped ' like \', however it is not working as expected
        ')', // Close group
        '[\']', // Closing quote
        '[\)]',  // Close parentheses or new parameter
    ]),

    /**
     * The locales you want to manage.
     */
    'locals' => [
        'az' => [
            'label' => 'Azerbaijani',
            'flag' => 'az',
        ],
        'de' => [
            'label' => 'German',
            'flag' => 'de',
        ],
        'en' => [
            'label' => 'English',
            'flag' => 'us',
        ],
        'it' => [
            'label' => 'Italian',
            'flag' => 'it',
        ],
        'pt' => [
            'label' => 'Portuguese',
            'flag' => 'pt',
        ],
        'tr' => [
            'label' => 'Turkish',
            'flag' => 'tr',
        ],
        'ru' => [
            'label' => 'Russian',
            'flag' => 'ru',
        ],
    ],

    'translaters' => [
        \Ferdiunal\NovaTranslations\Translators\BingTranslator::class,
        // \Ferdiunal\NovaTranslations\Translators\GoogleTranslator::class, // Before use install this package composer require stichoza/google-translate-php
        \Ferdiunal\NovaTranslations\Translators\MyMemoryTranslator::class,
        // \Ferdiunal\NovaTranslations\Translators\NLPCloudTranslator::class, // Before use install this package composer require nlpcloud/nlpcloud-client
        // \Ferdiunal\NovaTranslations\Translators\DeepLTranslator::class,  // Before use install this package composer require
    ],

    'services' => [
        'deepl' => [
            // https://www.deepl.com/
            'api_key' => env('DEEPL_API_KEY'),
        ],
        'nlpcloud' => [
            // https://nlpcloud.com/
            'api_key' => env('NLPCLOUD_API_KEY'),
            'languages' => [
                // https://docs.nlpcloud.com/#translation
                'az' => 'azb_Arab',
                'az' => 'azj_Latn',
                'de' => 'deu_Latn',
                'en' => 'eng_Latn',
                'it' => 'ita_Latn',
                'pt' => 'por_Latn',
                'tr' => 'tur_Latn',
                'ru' => 'rus_Cyrl',
            ],
        ],
    ],
];
