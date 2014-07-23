<?php

use Mobileka\MosaiqHelpers\MosaiqArray;

return MosaiqArray::make([
    // first case: plain scopes with no configuration
    'firstCase' => [
        'allowedScopes' => ['scope', 'anotherScope'],

        'result' => [
            'scope' => ['alias' => 'scope'],
            'anotherScope' => ['alias' => 'anotherScope']
        ]
    ],

    // second case: when all possible scope configuration is provided
    'secondCase' => [
        'allowedScopes' => [
            'scope' => [
                'alias' => 'scopeAlias',
                'keys' => ['firstKey', 'secondKey'],
                'type' => 'int'
            ]
        ],
        'result' => [
            'scope' => [
                'alias' => 'scopeAlias',
                'keys' => ['firstKey', 'secondKey'],
                'type' => 'int'
            ],
        ],
    ],

    // third case: partial scope configuration
    'thirdCase' => [
        'allowedScopes' => [
            'scope' => [
                'type' => 'boolean'
            ]
        ],
        'result' => [
            'scope' => [
                'alias' => 'scope',
                'type' => 'boolean'
            ],
        ],
    ],
]);
