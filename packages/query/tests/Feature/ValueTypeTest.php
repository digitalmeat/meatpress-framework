<?php

declare(strict_types=1);

use Meatpress\Query\Utils\Types\BooleanType;
use Meatpress\Query\Utils\Types\CharType;
use Meatpress\Query\Utils\Types\DatetimeType;
use Meatpress\Query\Utils\Types\NumberType;
use Meatpress\Query\Utils\ValueTypeDetector;

$dataProvider = [
    ['value' => null, 'expected' => CharType::CHAR, 'message' => 'with null value'],
    ['value' => -5, 'expected' => NumberType::SIGNED, 'message' => 'with negative integer value'],
    ['value' => 5, 'expected' => NumberType::UNSIGNED, 'message' => 'with positive integer value'],
    ['value' => 0, 'expected' => BooleanType::BINARY, 'message' => 'with negative binary value'],
    ['value' => 1, 'expected' => BooleanType::BINARY, 'message' => 'with positive binary value'],
    ['value' => 5.5, 'expected' => NumberType::DECIMAL, 'message' => 'with float value'],
    ['value' => '5', 'expected' => NumberType::NUMERIC, 'message' => 'with numeric string value'],
    ['value' => '2021-01-01', 'expected' => DatetimeType::DATE, 'message' => 'with date value'],
    ['value' => '12:34:56', 'expected' => DatetimeType::TIME, 'message' => 'with time value'],
    ['value' => '2021-01-01 12:34:56', 'expected' => DatetimeType::DATETIME, 'message' => 'with datetime value'],
    ['value' => 'SomeString', 'expected' => CharType::CHAR, 'message' => 'with other string value'],
    ['value' => 'SomeString', 'type' => 'DATETIME', 'expected' => DatetimeType::DATETIME, 'message' => 'with forced type'],
];

foreach ($dataProvider as $data) {
    $value = $data['value'];
    $expected = $data['expected'];
    $message = $data['message'];
    $forcedType = $data['type'] ?? null;

    test("MetaQueryBuilder: detect $message", function () use ($value, $expected, $forcedType) {
        expect((new ValueTypeDetector($value, $forcedType))->detect())->toBe($expected);
    });
}
