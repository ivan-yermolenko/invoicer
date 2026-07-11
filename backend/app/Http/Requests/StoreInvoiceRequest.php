<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\InvoiceCurrency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'max:255', Rule::unique('invoices', 'number')],
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_tax_id' => ['required', 'string', 'max:50'],
            'net_amount' => ['required', 'numeric', 'min:0.01'],
            'vat_amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['sometimes', Rule::enum(InvoiceCurrency::class)],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
        ];
    }
}
