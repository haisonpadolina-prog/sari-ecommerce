<?php

use App\Support\LogisticsServiceArea;

test('last mile identifies the barangay from order address formats', function (?string $address, string $municipality, string $expected): void {
    expect(LogisticsServiceArea::fromAddress($address, $municipality))->toBe($expected);
})->with([
    ['Barangay Bubukal, Santa Cruz, Laguna', 'Santa Cruz', 'Bubukal'],
    ['123 Buyer Street, Bubukal, Santa Cruz, Laguna', 'Santa Cruz', 'Bubukal'],
    ['Bubukal, Santa Cruz, Laguna', 'Santa Cruz', 'Bubukal'],
    ['123 Buyer Street, Brgy. Bubukal, Sta. Cruz, Laguna', 'Santa Cruz', 'Bubukal'],
    ['123 Buyer Street, Bubukal, Sta. Cruz, Laguna', 'Santa Cruz', 'Bubukal'],
    [null, 'Santa Cruz', 'Unknown Area'],
]);