<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'invoice_number',
        'duration_hours',
        'nurse_hourly_rate',
        'amount',
        'payment_status',
        'payment_method',
        'stripe_payment_intent_id',
        'transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'paid_at'          => 'datetime',
        'amount'           => 'decimal:2',
        'nurse_hourly_rate' => 'decimal:2',
        'duration_hours'   => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Formatted amount in BDT.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '৳' . number_format($this->amount, 2);
    }

    /**
     * Formatted rate in BDT.
     */
    public function getFormattedRateAttribute(): string
    {
        return '৳' . number_format($this->nurse_hourly_rate, 2);
    }

    /**
     * Human-readable status badge colour.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'paid'     => 'success',
            'refunded' => 'warning',
            default    => 'danger',
        };
    }

    // ─── Static Helpers ───────────────────────────────────────────────────────

    /**
     * Generate a unique invoice number like INV-20260410-00042.
     */
    public static function generateInvoiceNumber(): string
    {
        $date  = now()->format('Ymd');
        $count = static::whereDate('created_at', now()->toDateString())->count() + 1;
        return 'INV-' . $date . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate cost based on service type and nurse experience.
     */
    public static function calculateCost(string $serviceType, int $experienceYears, float $durationHours): array
    {
        $rates            = config('service_rates.base_rates');
        $baseRate         = $rates[$serviceType] ?? config('service_rates.base_rates.Other');
        $bonusPerYear     = config('service_rates.experience_bonus_per_year', 50);
        $maxYears         = config('service_rates.max_experience_years', 5);
        $cappedExperience = min($experienceYears, $maxYears);
        $hourlyRate       = $baseRate + ($cappedExperience * $bonusPerYear);
        $hourlyRate       = min(max($hourlyRate, config('service_rates.min_rate', 400)), config('service_rates.max_rate', 2000));
        $totalAmount      = round($hourlyRate * $durationHours, 2);

        return [
            'hourly_rate'  => $hourlyRate,
            'total_amount' => $totalAmount,
        ];
    }
}
