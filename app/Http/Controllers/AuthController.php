<?php

namespace App\Http\Controllers;

use App\Models\admins;
use App\Models\customers;
use App\Models\order_items;
use App\Models\orders;
use App\Models\product;
use App\Models\users;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Predis\Client as Redis;
class AuthController extends Controller
{
    public function signup(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
                'name' => 'required',
                'phone_no' => 'nullable',
                'address' => 'nullable',
                'dob' => 'nullable|date',
                'gender' => 'nullable|in:Male,Female,Other',
                'profile_pic' => 'nullable|string',
            ]);
            // if (!Role::where('name', 'Customer')->exists()) {
            //     Role::create([
            //         'name' => 'Customer',
            //         'guard_name' => 'web',
            //     ]);
            // }

            // if (!Role::where('name', 'Admin')->exists()) {
            //     Role::create([
            //         'name' => 'Admin',
            //         'guard_name' => 'web',
            //     ]);
            // }
            $customerRole = Role::where('name', 'Customer')->first();
            if (!$customerRole) {
                return response()->json(['error' => 'Customer role not found.'], 500);
            }

            $user = users::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'role_id' => $customerRole->id,
            ]);

            $user->assignRole('Customer');
            if (!$user->id) {
                return response()->json(['error' => 'User creation failed.'], 500);
            }
            $customer = customers::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'address' => $request->address,
                'phone_no' => $request->phone_no,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'profile_pic' => $request->profile_pic,
            ]);

            if (!$customer) {
                return response()->json(['error' => 'Customer creation failed.'], 500);
            }

            return response()->json(['message' => 'Signup successful'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Signup failed: ' . $e->getMessage());
            return response()->json(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'login' => 'required',
                'password' => 'required'
            ]);

            $loginInput = $request->input('login');
            $password = $request->input('password');


            $user = users::where(function ($query) use ($loginInput) {
                $query->whereRaw('BINARY email = ?', [$loginInput])
                    ->orWhereRaw('BINARY username = ?', [$loginInput]);
            })
                ->first();

            if (!$user) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            if ($user->password !== $password) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }


            if (!$user->hasAnyRole(['Admin', 'Customer', 'Manager'])) {
                return response()->json(['message' => 'Unauthorized role'], 403);
            }

            Auth::login($user);
            session([
                'user' => $user,
                'role' => $user->getRoleNames()->first(),
            ]);

            $role = $user->getRoleNames()->first();
            $profile = null;

            if ($role === 'Admin') {

                $admin = admins::with('user:id,email')->where('user_id', $user->id)->first();

                if ($admin) {
                    $admin->profile_pic = $admin->profile_pic ? asset(path: $admin->profile_pic) : null;
                    $permissions = $user->getAllPermissions()->pluck('name');
                    $profile = [
                        'admin' => $admin,
                        'permissions' => $permissions,
                    ];

                }
            } elseif ($role === 'Customer') {
                $customer = customers::with('users:id,email')->where('user_id', $user->id)->first();
                if ($customer) {
                    $customer->profile_pic = $customer->profile_pic ? asset($customer->profile_pic) : null;
                    $profile = ['customer' => $customer];
                }
            }
            return response()->json([
                'success' => true,
                'role' => $role,
                'message' => 'Login successful',
                ...($profile ?? [])
            ]);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return response()->json(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        session()->flush();
        Auth::logout();

        return response()->json(['message' => 'Logged out successfully']);
    }
    public function getProfile()
    {
        if (!session()->has('user')) {
            return response()->json(['message' => 'Not logged in'], 401);
        }

        return response()->json([
            'user' => session('user'),
            'role' => session('role'),
            'profile' => session('profile')
        ]);
    }
    public function getAllProducts()
    {
        $products = product::where('is_active', 1)->get()->map(function ($product) {
            $product->image_url = $product->image ? asset($product->image) : asset('/images/temp.jpg');
            return $product;
        });

        return response()->json($products);
    }
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_address' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:product,id',
            'products.*.quantity' => 'required|integer|min:1',
            'order_type' => 'nullable|in:cash,online',
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            $itemsData = [];
            $stockErrors = [];

            foreach ($validated['products'] as $item) {
                $product = product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    $stockErrors[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'requested_quantity' => $item['quantity'],
                        'available_stock' => $product->stock,
                        'message' => "Stock not available for '{$product->name}'. Available stock is {$product->stock}.",
                        'image_url' => $product->image ? asset($product->image) : null,
                    ];
                } else {
                    $total += $product->price * $item['quantity'];

                    $itemsData[] = [

                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price_at_purchase' => $product->price,
                        'product_model' => $product,

                    ];
                }
            }
            if (!empty($stockErrors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot be placed due to stock issues',
                    'errors' => $stockErrors
                ], 400);

            }
            $paymentType = 'cash';
            $isPaid = 0;
            if (isset($validated['order_type']) && $validated['order_type'] === 'online') {
                $paymentType = 'online';
                $isPaid = 1;
            }
            $order = orders::create([
                'customer_id' => $validated['customer_id'],
                'delivery_address' => $validated['delivery_address'],
                'total_payment' => $total,
                'payment_type' => $paymentType,
                'is_paid' => $isPaid,
                'order_datetime' => Carbon::now(),
                'status' => 'Pending',
                'expected_delivery_date' => Carbon::now()->addDays(5),
            ]);
            foreach ($itemsData as $item) {
                order_items::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price_at_purchase'],
                ]);
                $item['product_model']->stock -= $item['quantity'];
                $item['product_model']->save();
            }
            try {
                $customer = customers::find($validated['customer_id']);

                $message = "🛒 New Order Received! {$customer->name} placed an order of Rs. {$total} (Order #{$order->id})";

                $redis = new Redis();
                $redis->publish('order-events', json_encode([
                    'event' => 'OrderPlaced',
                    'data' => [
                        'order_id' => $order->id,
                        'total_payment' => $total,
                        'status' => 'Pending',
                        'customer_id' => $validated['customer_id'],
                        'customer_name' => $customer->name,
                        'timestamp' => now()->toDateTimeString(),
                        'message' => $message
                    ]
                ]));

                logger("📡 OrderPlaced event published to Redis");

            } catch (Exception $e) {
                logger("❌ Failed to publish event to Redis: " . $e->getMessage());
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'total_payment' => $total
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Order failed due to internal error',
                'error' => $e->getMessage()
            ], 500);
        }

    }
    public function getCustomerOrders($customer_id)
    {
        try {

            if (!customers::find($customer_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            $orders = orders::where('customer_id', $customer_id)
                ->with(['orderItems.product'])
                ->orderByDesc('order_datetime')
                ->get();

            $data = [];

            foreach ($orders as $order) {
                $products = [];

                foreach ($order->orderItems as $item) {
                    $product = $item->product;

                    $products[] = [
                        'product_id' => $product->id ?? 'N/A',
                        'name' => $product->name ?? 'N/A',
                        'category' => $product->category ?? 'N/A',
                        'price_at_purchase' => $item->price_at_purchase ?? 'N/A',
                        'quantity' => $item->quantity ?? 'N/A',
                        'image_url' => isset($product->image) ? asset($product->image) : null
                    ];
                }
                $data[] = [
                    'order_id' => $order->id,
                    'order_datetime' => $order->order_datetime ?? 'N/A',
                    'status' => $order->status ?? 'N/A',
                    'total_payment' => $order->total_payment ?? 'N/A',
                    'delivered_on_time' => $order->delivered_on_time ?? 'N/A',
                    'delivery_address' => $order->delivery_address ?? 'N/A',
                    'expected_delivery_date' => $order->expected_delivery_date ?? 'N/A',
                    'actual_delivery_date' => $order->actual_delivery_date ?? 'N/A',
                    'products' => $products,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer orders fetched successfully',
                'orders' => $data
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
