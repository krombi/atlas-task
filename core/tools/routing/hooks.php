<?php
return [
    'base' => [
        'm' => '/^$/',
        'l' => '',
        'h' => [
            'c' => 'Form\Detail',
            'm' => 'creating'
        ]
    ],
    'detail' => [
        'm' => '/^user\/([a-z\-]+)-([0-9]+)$/',
        'l' => 'user/{alias}-{id}',
        'h' => [
            'c' => 'Form\Detail',
            'm' => 'profile'
        ]
    ],
    'tools' => [
        'm' => '/^tools\/form.json$/',
        'l' => 'tools/form.json',
        'h' => [
            'c' => 'Form\Json',
            'm' => 'tools'
        ]
    ]
];