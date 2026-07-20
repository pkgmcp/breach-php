<?php

declare(strict_types=1);

/**
 * Sample password breach data for testing.
 */
return [
    // Well-known breached passwords
    'password123' => 23_576_845,
    '123456' => 39_034_912,
    'qwerty' => 4_213_214,
    'admin' => 1_872_453,
    'letmein' => 3_456_231,
    'welcome' => 1_234_567,
    'monkey' => 987_654,
    'dragon' => 876_543,
    'master' => 765_432,
    'login' => 654_321,

    // Common prefixes for testing
    'prefixes' => [
        '00000', '00001', '00002',
        '11111', '22222', '33333',
        'AAAAA', 'BBBBB', 'CCCCC',
    ],

    // Sample hash prefixes
    'hash_prefixes' => [
        '5BAA6' => '1E4C9B93F3F06822509CBE396E32E298',
        '7C4A8' => 'B3A7F85C0B3C4A7E9F1D2C3B4A5F6E7D',
        'ADC83' => 'D4D909C3F6B4D4E5F6A7B8C9D0E1F2A3',
    ],
];
