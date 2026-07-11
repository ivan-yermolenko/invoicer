<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvoiceCurrency;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Invoice>
 */
final class InvoiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $netAmount = $this->faker->randomFloat(2, 100, 100000);
        $vatAmount = round($netAmount * 0.20, 2);
        $grossAmount = $netAmount + $vatAmount;

        $issueDate = Carbon::parse($this->faker->dateTimeBetween('-1 year', 'now'));
        $dueDate = $issueDate->copy()->addDays($this->faker->randomElement([14, 30, 45, 60]));

        return [
            'number' => 'INV-' . $this->faker->unique()->numberBetween(100000, 999999),
            'supplier_name' => $this->faker->company(),
            'supplier_tax_id' => $this->faker->numerify('###########'),
            'net_amount' => $netAmount,
            'vat_amount' => $vatAmount,
            'gross_amount' => $grossAmount,
            'currency' => $this->faker->randomElement(InvoiceCurrency::cases())->value,
            'status' => $this->faker->randomElement(InvoiceStatus::cases())->value,
            'issue_date' => $issueDate->format('Y-m-d'),
            'due_date' => $dueDate->format('Y-m-d'),
        ];
    }
}
