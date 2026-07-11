<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class InvoiceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_list_of_invoices(): void
    {
        Invoice::factory()->count(3)->create();

        $response = $this->getJson('/api/invoices');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'number',
                        'supplier_name',
                        'net_amount',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_can_get_single_invoice(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->getJson("/api/invoices/{$invoice->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $invoice->id)
            ->assertJsonPath('data.number', $invoice->number);
    }

    public function test_can_create_invoice(): void
    {
        $payload = [
            'number' => 'INV-TEST-001',
            'supplier_name' => 'Test Supplier',
            'supplier_tax_id' => '1234567890',
            'net_amount' => 100,
            'vat_amount' => 20,
            'issue_date' => '2023-01-01',
            'due_date' => '2023-01-31',
        ];

        $response = $this->postJson('/api/invoices', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.number', 'INV-TEST-001')
            ->assertJsonPath('data.gross_amount', 120);

        $this->assertDatabaseHas('invoices', ['number' => 'INV-TEST-001']);
    }

    public function test_can_update_pending_invoice(): void
    {
        $invoice = Invoice::factory()->create(['status' => 'pending']);

        $payload = ['net_amount' => 200, 'vat_amount' => 40];

        $response = $this->putJson("/api/invoices/{$invoice->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.gross_amount', 240);

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'net_amount' => 200]);
    }

    public function test_cannot_update_approved_invoice(): void
    {
        $invoice = Invoice::factory()->create(['status' => 'approved']);

        $response = $this->putJson("/api/invoices/{$invoice->id}", ['net_amount' => 200]);

        $response->assertForbidden();
    }
}
