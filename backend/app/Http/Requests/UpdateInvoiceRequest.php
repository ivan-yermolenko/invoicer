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
        $invoiceId = $this->route('invoice')?->id;

        return [
            'number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('invoices', 'number')->ignore($invoiceId)],
            'supplier_name' => ['sometimes', 'required', 'string', 'max:255'],
            'supplier_tax_id' => ['sometimes', 'required', 'string', 'max:50'],
            'net_amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'vat_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'currency' => ['sometimes', Rule::enum(InvoiceCurrency::class)],
            'status' => ['sometimes', Rule::enum(InvoiceStatus::class)],
            'issue_date' => ['sometimes', 'required', 'date'],
            'due_date' => ['sometimes', 'required', 'date', 'after_or_equal:issue_date'],
        ];
    }
}
