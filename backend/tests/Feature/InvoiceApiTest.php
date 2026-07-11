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
}
