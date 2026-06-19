<?php

use App\Models\Client;
use App\Models\ClientAgreement;
use App\Models\User;
use App\Support\ClientAgreements\ClientAgreementExportQuery;
use App\Support\ClientAgreements\ClientAgreementSpreadsheetColumns;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @param  array<int, array<int, mixed>>  $rows
 */
function makeClientAgreementImportFile(array $rows): UploadedFile
{
    $spreadsheet = new Spreadsheet;
    $sheet = $spreadsheet->getActiveSheet();

    foreach (ClientAgreementSpreadsheetColumns::IMPORT_HEADERS as $columnIndex => $header) {
        $sheet->getCell([$columnIndex + 1, 1])->setValue($header);
    }

    foreach ($rows as $rowIndex => $row) {
        $sheet->fromArray($row, null, 'A'.($rowIndex + 2));
    }

    $path = tempnam(sys_get_temp_dir(), 'client-agreement-import-').'.xlsx';
    (new Xlsx($spreadsheet))->save($path);

    return new UploadedFile($path, 'client-agreements-import.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
}

test('authenticated user can manage client agreements', function () {
    $this->actingAs(User::factory()->create());

    $client = Client::query()->create([
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971501111111',
        'company' => 'ADNOC',
    ]);

    $this->get(route('client-agreements.index'))->assertSuccessful();
    $this->get(route('client-agreements.create'))->assertSuccessful();

    $response = $this->post(route('client-agreements.store'), [
        'client_id' => $client->id,
        'agreement_ref' => 'OMS-AGR-2026-001',
        'scope_of_work' => 'Offshore crew supply services.',
        'duration_days' => 30,
        'start_date' => '2026-06-01',
        'monthly_invoice_value' => 12500.50,
    ]);

    $agreement = ClientAgreement::query()->firstOrFail();

    $response->assertRedirect(route('client-agreements.index', absolute: false));

    expect($agreement->client_id)->toBe($client->id)
        ->and($agreement->agreement_ref)->toBe('OMS-AGR-2026-001')
        ->and($agreement->start_date->toDateString())->toBe('2026-06-01')
        ->and($agreement->end_date->toDateString())->toBe('2026-06-30')
        ->and((float) $agreement->monthly_invoice_value)->toBe(12500.50);

    $this->put(route('client-agreements.update', $agreement), [
        'client_id' => $client->id,
        'agreement_ref' => 'OMS-AGR-2026-001',
        'scope_of_work' => 'Updated offshore crew supply services.',
        'duration_days' => 60,
        'start_date' => '2026-06-01',
        'monthly_invoice_value' => 15000,
    ])->assertRedirect(route('client-agreements.index', absolute: false));

    $agreement->refresh();

    expect($agreement->scope_of_work)->toBe('Updated offshore crew supply services.')
        ->and($agreement->duration_days)->toBe(60)
        ->and($agreement->end_date->toDateString())->toBe('2026-07-30');

    $this->delete(route('client-agreements.destroy', $agreement))
        ->assertRedirect(route('client-agreements.index', absolute: false));

    expect(ClientAgreement::query()->count())->toBe(0);
});

test('client agreement store requires client and unique agreement ref', function () {
    $this->actingAs(User::factory()->create());

    $client = Client::query()->create([
        'name' => 'DP World',
        'email' => 'ops@dpworld.test',
        'phone' => '+971502222222',
        'company' => 'DP World',
    ]);

    ClientAgreement::query()->create([
        'client_id' => $client->id,
        'agreement_ref' => 'OMS-AGR-2026-002',
        'scope_of_work' => 'Existing agreement.',
        'duration_days' => 30,
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-30',
        'monthly_invoice_value' => 5000,
    ]);

    $this->post(route('client-agreements.store'), [
        'agreement_ref' => 'OMS-AGR-2026-002',
        'scope_of_work' => 'Duplicate ref.',
        'duration_days' => 30,
        'start_date' => '2026-02-01',
        'monthly_invoice_value' => 6000,
    ])->assertSessionHasErrors(['client_id', 'agreement_ref']);

    expect(ClientAgreement::query()->count())->toBe(1);
});

test('authenticated user can filter client agreements list', function () {
    $this->actingAs(User::factory()->create());

    $adnoc = Client::query()->create([
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971500000001',
        'company' => 'ADNOC',
    ]);

    $dpWorld = Client::query()->create([
        'name' => 'DP World',
        'email' => 'ops@dpworld.test',
        'phone' => '+971500000002',
        'company' => 'DP World',
    ]);

    ClientAgreement::query()->create([
        'client_id' => $adnoc->id,
        'agreement_ref' => 'OMS-AGR-2026-010',
        'scope_of_work' => 'ADNOC offshore support.',
        'duration_days' => 30,
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-30',
        'monthly_invoice_value' => 8000,
    ]);

    ClientAgreement::query()->create([
        'client_id' => $dpWorld->id,
        'agreement_ref' => 'OMS-AGR-2026-011',
        'scope_of_work' => 'DP World logistics support.',
        'duration_days' => 45,
        'start_date' => '2026-02-01',
        'end_date' => '2026-03-17',
        'monthly_invoice_value' => 9000,
    ]);

    $this->get(route('client-agreements.index', [
        'q' => 'ADNOC',
        'client_id' => $adnoc->id,
    ]))
        ->assertSuccessful()
        ->assertSee('OMS-AGR-2026-010')
        ->assertDontSee('OMS-AGR-2026-011');
});

test('authenticated user can control client agreements pagination size', function () {
    $this->actingAs(User::factory()->create());

    $client = Client::query()->create([
        'name' => 'Pagination Client',
        'email' => 'ops@pagination.test',
        'phone' => null,
        'company' => 'Pagination Co',
    ]);

    collect(range(1, 12))->each(function (int $index) use ($client): void {
        ClientAgreement::query()->create([
            'client_id' => $client->id,
            'agreement_ref' => "OMS-AGR-2026-{$index}",
            'scope_of_work' => "Agreement {$index}",
            'duration_days' => 30,
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-30',
            'monthly_invoice_value' => 1000 + $index,
        ]);
    });

    $this->get(route('client-agreements.index', ['per_page' => 10]))
        ->assertSuccessful()
        ->assertSee('Showing')
        ->assertSee('1')
        ->assertSee('10');
});

test('authenticated user can export client agreements to excel and pdf', function () {
    $this->actingAs(User::factory()->create());

    $client = Client::query()->create([
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971500000001',
        'company' => 'ADNOC',
    ]);

    ClientAgreement::query()->create([
        'client_id' => $client->id,
        'agreement_ref' => 'OMS-AGR-2026-100',
        'scope_of_work' => 'Export test agreement.',
        'duration_days' => 30,
        'start_date' => '2026-06-01',
        'end_date' => '2026-06-30',
        'monthly_invoice_value' => 7500,
    ]);

    $excelResponse = $this->get(route('client-agreements.export.excel'));
    $excelResponse->assertOk();
    expect($excelResponse->headers->get('content-type'))->toContain('spreadsheetml.sheet');
    expect($excelResponse->headers->get('content-disposition'))->toContain('client-agreements-');

    $pdfResponse = $this->get(route('client-agreements.export.pdf'));
    $pdfResponse->assertOk();
    expect($pdfResponse->headers->get('content-type'))->toContain('application/pdf');
    expect($pdfResponse->headers->get('content-disposition'))->toContain('client-agreements-');
});

test('client agreement export query respects list filters', function () {
    $adnoc = Client::query()->create([
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971500000001',
        'company' => 'ADNOC',
    ]);

    $dpWorld = Client::query()->create([
        'name' => 'DP World',
        'email' => 'ops@dpworld.test',
        'phone' => '+971500000002',
        'company' => 'DP World',
    ]);

    ClientAgreement::query()->create([
        'client_id' => $adnoc->id,
        'agreement_ref' => 'OMS-AGR-2026-201',
        'scope_of_work' => 'ADNOC export row.',
        'duration_days' => 30,
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-30',
        'monthly_invoice_value' => 5000,
    ]);

    ClientAgreement::query()->create([
        'client_id' => $dpWorld->id,
        'agreement_ref' => 'OMS-AGR-2026-202',
        'scope_of_work' => 'DP World export row.',
        'duration_days' => 30,
        'start_date' => '2026-02-01',
        'end_date' => '2026-03-02',
        'monthly_invoice_value' => 6000,
    ]);

    $request = Request::create('/', 'GET', [
        'q' => 'ADNOC',
        'client_id' => (string) $adnoc->id,
    ]);

    $results = ClientAgreementExportQuery::fromRequest($request)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->agreement_ref)->toBe('OMS-AGR-2026-201');
});

test('authenticated user can download client agreement import template', function () {
    $this->actingAs(User::factory()->create());

    $response = $this->get(route('client-agreements.import.template'));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('spreadsheetml.sheet');
    expect($response->headers->get('content-disposition'))->toContain('client-agreements-import-template.xlsx');
});

test('authenticated user can import valid client agreements from excel', function () {
    $this->actingAs(User::factory()->create());

    Client::query()->create([
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971500000001',
        'company' => 'ADNOC',
    ]);

    $file = makeClientAgreementImportFile([
        ['ADNOC Offshore', 'OMS-AGR-2026-301', 'Imported agreement.', 30, '2026-06-01', 12500],
    ]);

    $response = $this->post(route('client-agreements.import'), ['file' => $file]);

    $response->assertRedirect(route('client-agreements.index', absolute: false));
    $response->assertSessionHas('status');

    $agreement = ClientAgreement::query()->firstOrFail();

    expect($agreement->agreement_ref)->toBe('OMS-AGR-2026-301')
        ->and($agreement->start_date->toDateString())->toBe('2026-06-01')
        ->and($agreement->end_date->toDateString())->toBe('2026-06-30')
        ->and((float) $agreement->monthly_invoice_value)->toBe(12500.0);
});

test('client agreement import skips invalid rows and imports valid ones', function () {
    $this->actingAs(User::factory()->create());

    Client::query()->create([
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971500000001',
        'company' => 'ADNOC',
    ]);

    $file = makeClientAgreementImportFile([
        ['Unknown Client', 'OMS-AGR-2026-302', 'Should fail.', 30, '2026-06-01', 5000],
        ['ADNOC Offshore', 'OMS-AGR-2026-303', 'Should import.', 30, '2026-06-01', 8000],
    ]);

    $response = $this->post(route('client-agreements.import'), ['file' => $file]);

    $response->assertRedirect(route('client-agreements.index', absolute: false));
    $response->assertSessionHas('status');
    $response->assertSessionHas('import_errors');

    expect(ClientAgreement::query()->count())->toBe(1);
    expect(ClientAgreement::query()->value('agreement_ref'))->toBe('OMS-AGR-2026-303');
    expect(session('import_errors'))->toContain('Row 2: Client "Unknown Client" not found.');
});

test('client agreement import rejects duplicate agreement refs', function () {
    $this->actingAs(User::factory()->create());

    $client = Client::query()->create([
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971500000001',
        'company' => 'ADNOC',
    ]);

    ClientAgreement::query()->create([
        'client_id' => $client->id,
        'agreement_ref' => 'OMS-AGR-2026-304',
        'scope_of_work' => 'Existing agreement.',
        'duration_days' => 30,
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-30',
        'monthly_invoice_value' => 5000,
    ]);

    $file = makeClientAgreementImportFile([
        ['ADNOC Offshore', 'OMS-AGR-2026-304', 'Duplicate ref.', 30, '2026-06-01', 6000],
    ]);

    $response = $this->post(route('client-agreements.import'), ['file' => $file]);

    $response->assertRedirect(route('client-agreements.index', absolute: false));
    $response->assertSessionHas('error');
    $response->assertSessionHas('import_errors');

    expect(ClientAgreement::query()->count())->toBe(1);
    expect(session('import_errors'))->toContain('Row 2: Agreement Ref. "OMS-AGR-2026-304" already exists.');
});

test('client agreement import rejects invalid file types', function () {
    $this->actingAs(User::factory()->create());

    $file = UploadedFile::fake()->create('agreements.txt', 10, 'text/plain');

    $this->post(route('client-agreements.import'), ['file' => $file])
        ->assertSessionHasErrors('file');

    expect(ClientAgreement::query()->count())->toBe(0);
});
