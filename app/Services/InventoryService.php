<?php

namespace App\Services;

use App\Models\ProductShade;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Record a stock movement atomically with row-level locking.
     */
    public function recordMovement(
        ProductShade $shade,
        string $type,
        float $quantity,
        string $direction,
        ?float $unitCost = null,
        ?string $reason = null,
        ?Model $related = null,
        ?User $user = null,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use (
            $shade, $type, $direction, $quantity, $unitCost, $reason, $related, $user, $notes
        ) {
            // Lock the shade row for update
            $lockedShade = ProductShade::where('id', $shade->id)->lockForUpdate()->firstOrFail();

            $prevStock = (float) $lockedShade->stock_quantity;
            $qty = abs($quantity);

            if ($direction === 'out' && $lockedShade->stock_quantity < $qty) {
                // Warning or check
                // For strict safety:
                if ($type === 'sale' && !config('flooros.allow_negative_stock', false)) {
                    throw ValidationException::withMessages([
                        'quantity' => "Insufficient stock for SKU '{$lockedShade->sku}'. Available: {$prevStock} {$lockedShade->unit}, requested: {$qty} {$lockedShade->unit}."
                    ]);
                }
            }

            $newStock = $direction === 'in' ? ($prevStock + $qty) : ($prevStock - $qty);
            $lockedShade->update(['stock_quantity' => $newStock]);

            // Cost for movement
            $effectiveCost = $unitCost ?? (float) $lockedShade->cost_price;

            // Generate unique reference
            $refNumber = 'MOV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            return StockMovement::create([
                'reference_number' => $refNumber,
                'product_shade_id' => $lockedShade->id,
                'sku' => $lockedShade->sku,
                'type' => $type,
                'direction' => $direction,
                'quantity' => $qty,
                'unit' => $lockedShade->unit,
                'unit_cost' => $effectiveCost,
                'previous_stock' => $prevStock,
                'new_stock' => $newStock,
                'related_type' => $related ? get_class($related) : null,
                'related_id' => $related ? $related->id : null,
                'reason' => $reason,
                'notes' => $notes,
                'user_id' => $user ? $user->id : (auth()->id() ?? null),
                'occurred_at' => now(),
            ]);
        });
    }
}
