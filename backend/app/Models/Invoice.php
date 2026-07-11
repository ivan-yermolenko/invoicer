<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceCurrency;
use App\Enums\InvoiceStatus;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $number
 * @property string $supplier_name
 * @property string $supplier_tax_id
 * @property string $net_amount
 * @property string $vat_amount
 * @property string $gross_amount
 * @property InvoiceCurrency $currency
 * @property InvoiceStatus $status
 * @property Carbon $issue_date
 * @property Carbon $due_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Invoice newModelQuery()
 * @method static Builder|Invoice newQuery()
 * @method static Builder|Invoice query()
 * @method static Invoice create(array $attributes = [])
 * @method static Invoice updateOrCreate(array $attributes, array $values = [])
 * @method static Builder|Invoice latest($column = 'created_at')
 */
final class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'number',
        'supplier_name',
        'supplier_tax_id',
        'net_amount',
        'vat_amount',
        'gross_amount',
        'currency',
        'status',
        'issue_date',
        'due_date',
    ];

    protected $casts = [
        'net_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'status' => InvoiceStatus::class,
        'currency' => InvoiceCurrency::class,
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Invoice $invoice) {
            $invoice->gross_amount = (float) $invoice->net_amount + (float) $invoice->vat_amount;
        });
    }
}
