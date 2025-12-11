<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
        'payment_status',
        'notes',
        'admin_notes',
        'tracking_number',
        'delivery_type',
        'delivery_option',
        'delivery_date',
        'delivery_time',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'shipping_address' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Status Check Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isShipped()
    {
        return $this->status === 'shipped';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'paid', 'processing']);
    }

    // Action Methods
    public function markAsPaid()
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            // Status tetap 'pending' sampai admin konfirmasi
        ]);

        // Reduce product stock after successful payment
        foreach ($this->items as $item) {
            if ($item->variant_id) {
                $item->variant->decrement('stock', $item->quantity);
            } else {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        // Remove items from user's cart after successful payment
        $this->removeFromCart();

        // Send notification to user
        $this->user->notifications()->create([
            'type' => 'order_placed',
            'title' => 'Pembayaran Berhasil',
            'message' => "Pembayaran untuk pesanan {$this->order_number} berhasil. Menunggu konfirmasi admin.",
            'data' => [
                'order_id' => $this->id,
                'order_number' => $this->order_number,
                'total' => $this->total,
                'items_count' => $this->items->count(),
                'product_image' => $this->items->first()->product->images->first()?->image_path
                    ? asset('storage/' . $this->items->first()->product->images->first()->image_path)
                    : null,
                'product_name' => $this->items->first()->product->name ?? '',
            ],
        ]);
    }

    // Remove order items from user's cart
    private function removeFromCart()
    {
        $cart = $this->user->cart;
        if (!$cart) {
            \Log::info("Order {$this->order_number}: User has no cart");
            return;
        }

        \Log::info("Order {$this->order_number}: Removing {$this->items->count()} items from cart");

        foreach ($this->items as $orderItem) {
            $query = $cart->items()->where('product_id', $orderItem->product_id);
            
            // Handle variant_id which can be null
            if ($orderItem->variant_id) {
                $query->where('variant_id', $orderItem->variant_id);
            } else {
                $query->whereNull('variant_id');
            }
            
            $cartItem = $query->first();
            
            if ($cartItem) {
                \Log::info("Deleting cart item: product_id={$orderItem->product_id}, variant_id={$orderItem->variant_id}");
                $cartItem->delete();
            } else {
                \Log::warning("Cart item not found: product_id={$orderItem->product_id}, variant_id={$orderItem->variant_id}");
            }
        }
        
        \Log::info("Cart cleanup completed for order {$this->order_number}");
    }

    public function markAsShipped($trackingNumber = null)
    {
        $this->update([
            'status' => 'shipped',
            'shipped_at' => now(),
            'tracking_number' => $trackingNumber,
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markAsCancelled($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'admin_notes' => $reason ? "Cancelled: {$reason}" : $this->admin_notes,
        ]);

        // Restore stock
        foreach ($this->items as $item) {
            if ($item->variant_id) {
                $item->variant->increment('stock', $item->quantity);
            } else {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }

    // Static Methods
    public static function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        return "ORD-{$date}-{$random}";
    }
}
