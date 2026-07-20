<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Unit;

use ShamimStack\BreachPHP\Exceptions\ParserException;
use ShamimStack\BreachPHP\Parsers\HibpParser;

/*
|--------------------------------------------------------------------------
| HibpParser Unit Tests
|--------------------------------------------------------------------------
*/

beforeEach(function () {
    $this->parser = new HibpParser();
});

it('parses a valid multi-line HIBP response', function () {
    $s1 = '0000005C46E06C6B8C0536DBE37C44B6A5C'; // 35 chars
    $s2 = '000000213A6B3636AC6B9C25A7D40CB5D58'; // 35 chars
    $s3 = '00000058C5CBBB05B4F858DCA90B05E82F2'; // 35 chars
    $raw = "{$s1}:14\r\n{$s2}:3\r\n{$s3}:7";

    $result = $this->parser->parse('00000', $raw);

    expect($result->prefix())->toBe('00000')
        ->and($result->suffixCount())->toBe(3)
        ->and($result->suffixes())->toHaveCount(3);
});

it('parses a single-line response', function () {
    $suffix = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'; // exactly 35 chars
    $raw = "{$suffix}:100";

    $result = $this->parser->parse('AAAAA', $raw);

    expect($result->prefix())->toBe('AAAAA')
        ->and($result->suffixCount())->toBe(1)
        ->and($result->suffixes()[0]['count'])->toBe(100)
        ->and($result->suffixes()[0]['suffix'])->toBe(strtoupper($suffix));
});

it('returns empty suffixes for empty response', function () {
    $result = $this->parser->parse('AAAAA', '');

    expect($result->prefix())->toBe('AAAAA')
        ->and($result->suffixCount())->toBe(0)
        ->and($result->suffixes())->toBe([]);
});

it('uppercases the prefix in the response', function () {
    $result = $this->parser->parse('abcde', '');

    expect($result->prefix())->toBe('ABCDE');
});

it('uppercases suffix values', function () {
    $suffix = 'aabbccddeeff00112233445566778899abc'; // exactly 35 chars
    $raw = "{$suffix}:5";

    $result = $this->parser->parse('AAAAA', $raw);

    expect($result->suffixes()[0]['suffix'])->toBe(strtoupper($suffix));
});

it('handles responses with extra whitespace', function () {
    $suffix = 'AABBCCDDEEFF00112233445566778899001'; // exactly 35 chars
    $raw = "  {$suffix}  :  42  ";

    $result = $this->parser->parse('AAAAA', $raw);

    expect($result->suffixCount())->toBe(1)
        ->and($result->suffixes()[0]['count'])->toBe(42);
});

it('throws ParserException for invalid format without colon', function () {
    $this->parser->parse('AAAAA', 'INVALIDLINE');
})->throws(ParserException::class, 'SUFFIX:COUNT');

it('throws ParserException for suffix with wrong length', function () {
    $this->parser->parse('AAAAA', 'SHORT:10');
})->throws(ParserException::class, '35 characters');

it('handles zero count correctly', function () {
    $suffix = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'; // exactly 35 chars
    $raw = "{$suffix}:0";

    $result = $this->parser->parse('AAAAA', $raw);

    expect($result->suffixes()[0]['count'])->toBe(0);
});

it('handles very large breach counts', function () {
    $suffix = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'; // exactly 35 chars
    $raw = "{$suffix}:999999999";

    $result = $this->parser->parse('AAAAA', $raw);

    expect($result->suffixes()[0]['count'])->toBe(999999999);
});

it('skips empty lines in response', function () {
    $s1 = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'; // exactly 35 chars
    $s2 = 'BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBB'; // exactly 35 chars
    $raw = "{$s1}:5\r\n\r\n{$s2}:10\r\n\r\n";

    $result = $this->parser->parse('AAAAA', $raw);

    expect($result->suffixCount())->toBe(2);
});

it('throws ParserException for negative count', function () {
    $suffix = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'; // exactly 35 chars
    $raw = "{$suffix}:-5";

    $this->parser->parse('AAAAA', $raw);
})->throws(ParserException::class, 'non-negative');
