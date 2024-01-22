<?php

return [
    'points' => [
        'win' => 3,
        'draw' => 1,
        'bonus_points' => [
            'home_win' => 1,
            'away_win' => 2,
            'conditions' => [
                'home_win_by_3_goals' => 3,
                'away_win_by_3_goals' => 3,
            ],
        ],
        'penalty_points' => [
            'home_defeat' => -2,
            'away_defeat' => -1,
            'conditions' => [
                'home_defeat_goals' => 3,
                'away_defeat_goals' => 3
            ]
        ],
    ],
];