<?php

return [
    'points' => [
        'win' => 3,
        'draw' => 1,
        'bonus_points' => [
            'home' => 1,
            'away' => 2,
            'conditions' => [
                'home' => 3,
                'away' => 3,
            ],
        ],
        'penalty_points' => [
            'home' => -2,
            'away' => -1,
            'conditions' => [
                'home' => 3,
                'away' => 3
            ]
        ],
    ],
];