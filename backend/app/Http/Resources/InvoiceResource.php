<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Invoice
 */
final class InvoiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'supplier_name' => $this->supplier_name,
            'supplier_tax_id' => $this->supplier_tax_id,
            'net_amount' => (float) $this->net_amount,
            'vat_amount' => (float) $this->vat_amount,
            'gross_amount' => (float) $this->gross_amount,
            'currency' => $this->currency->value,
            'status' => $this->status->value,
            'issue_date' => $this->issue_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
