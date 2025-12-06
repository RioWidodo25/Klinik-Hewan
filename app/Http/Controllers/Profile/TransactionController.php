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
        $query = $user->orders()->with(['items.product.images']);

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
        // Check ownership
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product.images', 'payment', 'reviews']);

        return response()->json([
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $this->getStatusLabel($order->status),
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
        // Check ownership
        if ($order->user_id !== Auth::id()) {
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
        // Check ownership
        if ($order->user_id !== Auth::id()) {
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
     * Get status label in Indonesian
     */
    private function getStatusLabel($status)
    {
        return match ($status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Pesanan Dibatalkan',
            default => $status,
        };
    }
}
