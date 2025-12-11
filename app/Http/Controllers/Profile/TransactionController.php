<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    /**
     * Display user's transaction list
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = $user->orders()
            ->with(['items.product.images'])
            ->where('order_number', 'NOT LIKE', 'POS-%') // Exclude POS transactions
            ->where('payment_status', 'paid'); // Only show paid orders in transaction history

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        // Transform orders for frontend
        $orders->getCollection()->transform(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $this->getStatusLabel($order->status),
                'total' => $order->total,
                'items_count' => $order->items->count(),
                'created_at' => $order->created_at->timezone('Asia/Jakarta')->format('d F Y - H:i') . ' WIB',
                'shipping_service' => $order->shipping_service ?? 'Belanja Xpress',
                'first_product' => [
                    'name' => $order->items->first()?->product_name,
                    'image' => $order->items->first()?->product->images->first()?->image_path
                        ? asset('storage/' . $order->items->first()->product->images->first()->image_path)
                        : null,
                ],
            ];
        });

        return Inertia::render('Profile/Transactions/Index', [
            'orders' => $orders,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
                'date' => $request->date,
            ],
        ]);
    }

    /**
     * Get detailed transaction info
     */
    public function show(Order $order)
    {
        // Check ownership and prevent POS transactions access
        if ($order->user_id !== Auth::id() || str_starts_with($order->order_number, 'POS-')) {
            abort(403);
        }

        $order->load(['items.product.images', 'payment', 'reviews']);

        return response()->json([
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $this->getStatusLabel($order->status, $order->payment_status),
                'payment_status' => $order->payment_status,
                'snap_token' => $order->snap_token,
                'total' => $order->total,
                'subtotal' => $order->subtotal,
                'shipping_cost' => $order->shipping_cost,
                'created_at' => $order->created_at->timezone('Asia/Jakarta')->format('d F Y - H:i') . ' WIB',
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
                'shipping_address' => $order->shipping_address,
                'shipping_city' => $order->shipping_city,
                'shipping_district' => $order->shipping_district,
                'shipping_province' => $order->shipping_province,
                'shipping_postal_code' => $order->shipping_postal_code,
                'shipping_service' => $order->shipping_service ?? 'Belanja Xpress',
                'notes' => $order->notes,
                'items' => $order->items->map(fn($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_slug' => $item->product?->slug,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                    'image' => $item->product?->images->first()?->image_path
                        ? asset('storage/' . $item->product->images->first()->image_path)
                        : null,
                ]),
                'payment' => $order->payment ? [
                    'status' => $order->payment->status,
                    'payment_type' => $order->payment->payment_type,
                    'payment_method' => $order->payment->bank
                        ? strtoupper($order->payment->bank) . ' Virtual Account'
                        : ($order->payment->payment_type ?? 'Virtual Account'),
                    'va_number' => $order->payment->va_number,
                    'bank' => $order->payment->bank,
                ] : null,
                'has_reviews' => $order->reviews->count() > 0,
                'reviews' => $order->reviews->map(fn($review) => [
                    'product_id' => $review->product_id,
                    'rating' => $review->rating,
                    'review' => $review->review,
                ]),
            ],
        ]);
    }

    /**
     * Mark order as delivered/completed
     */
    public function complete(Order $order)
    {
        // Check ownership and prevent POS transactions access
        if ($order->user_id !== Auth::id() || str_starts_with($order->order_number, 'POS-')) {
            abort(403);
        }

        // Check if order is shipped
        if ($order->status !== 'shipped') {
            return back()->with('error', 'Pesanan belum dikirim');
        }

        $order->markAsDelivered();

        return back()->with('success', 'Pesanan berhasil diselesaikan. Silakan berikan ulasan Anda!');
    }

    /**
     * Submit product review
     */
    public function submitReview(Request $request, Order $order)
    {
        // Check ownership and prevent POS transactions access
        if ($order->user_id !== Auth::id() || str_starts_with($order->order_number, 'POS-')) {
            abort(403);
        }

        // Check if order is delivered
        if ($order->status !== 'delivered') {
            return back()->with('error', 'Anda hanya dapat memberikan ulasan setelah pesanan selesai');
        }

        $validated = $request->validate([
            'reviews' => 'required|array',
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.review' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['reviews'] as $reviewData) {
            // Check if user already reviewed this product for this order
            $existingReview = \App\Models\ProductReview::where('order_id', $order->id)
                ->where('product_id', $reviewData['product_id'])
                ->where('user_id', Auth::id())
                ->first();

            if ($existingReview) {
                // Update existing review
                $existingReview->update([
                    'rating' => $reviewData['rating'],
                    'review' => $reviewData['review'] ?? null,
                    'is_approved' => true, // Keep approved status
                ]);
            } else {
                // Create new review (auto-approved)
                \App\Models\ProductReview::create([
                    'user_id' => Auth::id(),
                    'product_id' => $reviewData['product_id'],
                    'order_id' => $order->id,
                    'rating' => $reviewData['rating'],
                    'review' => $reviewData['review'] ?? null,
                    'is_approved' => true, // Auto-approve review from verified purchases
                ]);
            }

            // Update product rating statistics
            $product = \App\Models\Product::find($reviewData['product_id']);
            if ($product) {
                $product->updateRating();
            }
        }

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    /**
     * Cancel order (only before admin confirms)
     */
    public function cancelOrder(Order $order)
    {
        // Check ownership and prevent POS transactions access
        if ($order->user_id !== Auth::id() || str_starts_with($order->order_number, 'POS-')) {
            abort(403);
        }

        // Only allow cancellation for pending or paid status (before admin confirms)
        if (!in_array($order->status, ['pending', 'paid'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah diproses');
        }

        // Return stock for each item before cancelling
        foreach ($order->items as $item) {
            if ($item->variant_id) {
                // Return stock to variant
                $variant = \App\Models\ProductVariant::find($item->variant_id);
                if ($variant) {
                    $variant->increment('stock', $item->quantity);
                }
            } else {
                // Return stock to product
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        // Update order status to cancelled
        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Pesanan berhasil dibatalkan');
    }

    /**
     * Generate new snap token for unpaid order
     */
    public function generateSnapToken(Order $order)
    {
        // Check ownership and prevent POS transactions access
        if ($order->user_id !== Auth::id() || str_starts_with($order->order_number, 'POS-')) {
            abort(403);
        }

        // Only allow for unpaid orders
        if ($order->payment_status !== 'unpaid') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan sudah dibayar atau tidak valid'
            ], 400);
        }

        try {
            // Set Midtrans configuration
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Prepare transaction details
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total,
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'item_details' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->product_id,
                        'price' => (int) $item->price,
                        'quantity' => $item->quantity,
                        'name' => $item->product_name,
                    ];
                })->toArray(),
            ];

            // Add shipping cost as separate item if exists
            if ($order->shipping_cost > 0) {
                $params['item_details'][] = [
                    'id' => 'shipping',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Biaya Pengiriman',
                ];
            }

            // Generate new snap token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Update order with new snap token
            $order->update(['snap_token' => $snapToken]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status label in Indonesian
     */
    private function getStatusLabel($status, $paymentStatus = null)
    {
        if ($status === 'pending') {
            return $paymentStatus === 'unpaid' ? 'Menunggu Pembayaran' : 'Menunggu Konfirmasi';
        }

        return match ($status) {
            'paid' => 'Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $status,
        };
    }
}
