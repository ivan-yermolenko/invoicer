<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\InvoiceCurrency;
use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');
        return $invoice && $invoice->status->value === 'pending';
    }

    public function rules(): array
    {
        $invoice = $this->route('invoice');
        $issueDateToCheck = $this->input('issue_date') ?? $invoice?->issue_date?->toDateString();

        return [
            'number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('invoices', 'number')->ignore($invoice?->id)],
            'supplier_name' => ['sometimes', 'required', 'string', 'max:255'],
            'supplier_tax_id' => ['sometimes', 'required', 'string', 'max:50'],
            'net_amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'vat_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'currency' => ['sometimes', Rule::enum(InvoiceCurrency::class)],
            'status' => ['sometimes', Rule::enum(InvoiceStatus::class)],
            'issue_date' => ['sometimes', 'required', 'date'],
            'due_date' => ['sometimes', 'required', 'date', 'after_or_equal:' . $issueDateToCheck],
        ];
    }
}
