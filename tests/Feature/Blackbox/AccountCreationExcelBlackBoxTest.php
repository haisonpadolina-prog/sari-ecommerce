<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Support\AccountCreationExcelHarness;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
    Storage::fake('local');
    Storage::fake('public');
    Mail::fake();
    config(['mail.default' => 'array']);
});

$excelCases = require dirname(__DIR__, 2).'/Support/account_creation_excel_cases.php';

foreach ($excelCases as $id => $scenario) {
    test("{$id} | {$scenario}", function () use ($id): void {
        AccountCreationExcelHarness::run($this, $id);
    });
}
