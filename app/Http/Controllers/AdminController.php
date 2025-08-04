<?php

namespace App\Http\Controllers;

use App\Models\customers;
use App\Models\orders;
use App\Models\product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\admins;
use App\Models\users;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Predis\Client as Redis;
class AdminController extends Controller
{
    public function index()
    {
        return view('Admin.Home');
    }
    public function updateAdminProfile(Request $request, $id)
    {
        $admin = admins::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin not found.',
            ], 404);
        }

        $user = users::find($admin->user_id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Associated user not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'address' => 'nullable|string|max:500',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|max:255',
        ], [
            'contact_no.regex' => 'The phone number must contain only numbers.',
            'profile_pic.max' => 'The profile picture must not be larger than 2MB.',
            'profile_pic.mimes' => 'The profile picture must be a JPEG, PNG, or JPG image.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Update admin fields
        $adminFields = ['name', 'designation', 'contact_no', 'address'];
        foreach ($adminFields as $field) {
            if (array_key_exists($field, $data)) {
                $admin->$field = $data[$field];
            }
        }

        // Handle profile picture
        if ($request->hasFile('profile_pic')) {
            // Delete old profile pic if exists
            if ($admin->profile_pic && file_exists(public_path($admin->profile_pic))) {
                unlink(public_path($admin->profile_pic));
            }

            $file = $request->file('profile_pic');
            $filename = 'admin_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('profile_pics'), $filename);
            $admin->profile_pic = 'profile_pics/' . $filename;
        }

        $admin->save();

        // Update user fields
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();

        // Prepare response with full URLs
        $admin->profile_pic = $admin->profile_pic ? asset($admin->profile_pic) : null;

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'admin' => $admin,
            'user' => $user,
        ]);
    }
    // public function updateAdminProfile(Request $request, $id)
    // {
    //     $admin = admins::find($id);

    //     if (!$admin) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Admin not found.',
    //         ], 404);
    //     }
    //     $user = users::find($admin->user_id);

    //     if (!$user) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Associated user not found.',
    //         ], 404);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'name' => 'nullable|string|max:255',
    //         'designation' => 'nullable|string|max:255',
    //         'contact_no' => 'nullable|string|max:20',
    //         'address' => 'nullable|string|max:500',
    //         'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg',
    //         'username' => 'nullable|string|max:255',
    //         'email' => 'nullable|email|unique:users,email|max:255',
    //         'password' => 'nullable|string|min:6',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $data = $validator->validated();
    //     foreach (['name', 'designation', 'contact_no', 'address'] as $field) {
    //         if (array_key_exists($field, $data)) {
    //             $admin->$field = $data[$field];
    //         }
    //     }

    //     if ($request->hasFile('profile_pic')) {
    //         $file = $request->file('profile_pic');
    //         $filename = 'admin_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
    //         $file->move(public_path('profile_pics'), $filename);
    //         $admin->profile_pic = 'profile_pics/' . $filename;
    //     }

    //     $admin->save();
    //     foreach (['username', 'email'] as $field) {
    //         if (array_key_exists($field, $data)) {
    //             $user->$field = $data[$field];
    //         }
    //     }
    //     if (isset($data['password'])) {
    //         $user->password = bcrypt($data['password']);
    //     }

    //     $user->save();
    //     $admin->profile_pic=asset($admin->profile_pic);
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Admin and associated user updated successfully.',
    //         'admin' => $admin,
    //         'user' => $user,
    //     ]);
    // }
    public function addProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'is_active' => 'required|boolean',
                'category' => 'nullable|string',
                'product_pic' => 'nullable|image|mimes:jpeg,png,jpg'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            $product = new product([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'category' => $request->input('category'),
                'stock' => $request->input('stock'),
                'is_active' => $isActive
            ]);

            $product->save();
            try {
                $message = "🆕 New Product Added: {$product->name} (ID: {$product->id})";

                $redis = new Redis();
                $redis->publish('product-events', json_encode([
                    'event' => 'ProductAdded',
                    'data' => [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'is_active' => $product->is_active,
                        'timestamp' => now()->toDateTimeString(),
                        'message' => $message
                    ]
                ]));

                logger("📡 ProductAdded event published to Redis");

            } catch (Exception $e) {
                logger("❌ Failed to publish ProductAdded event: " . $e->getMessage());
            }
            if ($request->hasFile('product_pic')) {
                $file = $request->file('product_pic');
                $filename = 'product_' . $product->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('product_images'), $filename);
                $product->image = 'product_images/' . $filename;
                $product->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Product added successfully',
                'product' => $product
            ], 201);

        } catch (Exception $e) {
            Log::error('Add product failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updateProduct(Request $request, $id)
    {
        $product = product::find($id);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        }

        $product->fill($request->only(['name', 'description', 'price', 'stock', 'is_active', 'category']));

        if ($request->hasFile('product_pic')) {
            $file = $request->file('product_pic');
            $filename = 'product_' . $product->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('product_images'), $filename);
            $product->image = '/' . 'product_images/' . $filename;
        }

        $product->save();
        try {
            $message = "🔄 Product Updated: {$product->name} (ID: {$product->id})";

            $redis = new Redis();
            $redis->publish('product-events', json_encode([
                'event' => 'ProductUpdated',
                'data' => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'is_active' => $product->is_active,
                    'timestamp' => now()->toDateTimeString(),
                    'message' => $message
                ]
            ]));

            logger("📡 ProductUpdated event published to Redis");

        } catch (Exception $e) {
            logger("❌ Failed to publish ProductUpdated event: " . $e->getMessage());
        }
        return response()->json(['status' => 'success', 'product' => $product], 200);
    }
    public function toggleProductStatus($id)
    {
        $product = product::find($id);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        }

        $product->is_active = !$product->is_active;
        $product->save();
        try {
            $status = $product->is_active ? 'activated' : 'deactivated';
            $message = "🔘 Product {$status}: {$product->name} (ID: {$product->id})";
            $redis = new Redis();
            $redis->publish('product-events', json_encode([
                'event' => 'ProductStatusChanged',
                'data' => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'is_active' => $product->is_active,
                    'timestamp' => now()->toDateTimeString(),
                    'message' => $message
                ]
            ]));
            logger("📡 ProductStatusChanged event published to Redis");
        } catch (Exception $e) {
            logger("❌ Failed to publish ProductStatusChanged event: " . $e->getMessage());
        }
        return response()->json([
            'status' => 'success',
            'message' => $product->is_active ? 'Product activated.' : 'Product deactivated.',
            'product' => $product
        ]);
    }
    public function getAllProducts()
    {
        $products = product::get()->map(function ($product) {
            $product->image_url = asset($product->image);
            $product->units_sold = $product->orderItems()->sum('quantity');
            return $product;
        });

        return response()->json($products);
    }
    public function restockProduct(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = product::find($id);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        }

        $product->stock += $request->quantity;
        $product->save();
        try {

            $message = "📦 Product Restocked: {$product->name} (ID: {$product->id}) - Added {$request->quantity} units";

            $redis = new Redis();
            $redis->publish('product-events', json_encode([
                'event' => 'ProductRestocked',
                'data' => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity_added' => $request->quantity,
                    'new_stock' => $product->stock,
                    'timestamp' => now()->toDateTimeString(),
                    'message' => $message
                ]
            ]));

            logger("📡 ProductRestocked event published to Redis");

        } catch (Exception $e) {
            logger("❌ Failed to publish ProductRestocked event: " . $e->getMessage());
        }
        return response()->json(['status' => 'success', 'message' => 'Product restocked.', 'product' => $product]);
    }
    public function getAllOrdersWithDetails()
    {
        try {

            $statusOrder = ['Pending', 'Dispatched', 'Completed', 'Canceled'];
            $orders = orders::with([
                'customer',
                'orderItems.product'
            ])
                ->get()
                ->sortBy(function ($order) use ($statusOrder) {
                    return array_search($order->status, $statusOrder);
                })
                ->values();
            $result = $orders->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'order_datetime' => $order->order_datetime,
                    'expected_delivery_date' => $order->expected_delivery_date,
                    'actual_delivery_date' => $order->actual_delivery_date,
                    'delivery_address' => $order->delivery_address,
                    'total_payment' => $order->total_payment,
                    'delivered_on_time' => $order->delivered_on_time,
                    'customer' => [
                        'customer_id' => $order->customer->id ?? null,
                        'name' => $order->customer->name ?? null,
                        'phone_no' => $order->customer->phone_no ?? null,
                        'address' => $order->customer->address ?? null,
                        'gender' => $order->customer->gender ?? null,
                        'dob' => $order->customer->dob ?? null,
                        'profile_pic' => $order->customer->profile_pic ? asset($order->customer->profile_pic) : null,
                    ],
                    'products' => $order->orderItems->map(function ($item) {
                        return [
                            'product_id' => $item->product->id ?? null,
                            'product_name' => $item->product->name ?? null,
                            'description' => $item->product->description ?? null,
                            'price_at_purchase' => $item->price_at_purchase,
                            'quantity' => $item->quantity,
                            'image' => $item->product->image ? asset($item->product->image) : null,
                        ];
                    })
                ];
            });
            return response()->json([
                'success' => true,
                'data' => $result
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function processOrder(Request $request, $orderId)
    {
        try {
            $request->validate([
                'status' => 'required|in:Pending,Dispatched,Canceled,Completed',
                'actual_delivery_date' => 'nullable|date'
            ]);

            $order = orders::find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.',
                ], 404);
            }

            $status = $request->status;
            $now = Carbon::now();

            switch ($status) {
                case 'Pending':
                    $order->expected_delivery_date = null;
                    $order->actual_delivery_date = null;
                    $order->delivered_on_time = null;
                    break;

                case 'Dispatched':
                    $order->expected_delivery_date = $now->copy()->addDay();
                    $order->actual_delivery_date = null;
                    $order->delivered_on_time = null;
                    break;

                case 'Canceled':
                    $order->expected_delivery_date = null;
                    $order->actual_delivery_date = null;
                    $order->delivered_on_time = null;
                    break;

                case 'Completed':
                    $actualDate = Carbon::parse($request->actual_delivery_date);
                    $order->actual_delivery_date = $actualDate;

                    if ($order->expected_delivery_date) {
                        $expected = Carbon::parse($order->expected_delivery_date)->startOfDay();
                        $actual = Carbon::parse($actualDate)->startOfDay();

                        $order->delivered_on_time = $actual->lessThanOrEqualTo($expected);
                    } else {
                        $order->delivered_on_time = false;
                    }
                    break;

            }

            $order->status = $status;
            $order->save();
            try {
                $message = "🔄 Order #{$order->id} status changed to: {$order->status}";

                $redis = new Redis();
                $redis->publish('order-events', json_encode([
                    'event' => 'OrderStatusChanged',
                    'data' => [
                        'order_id' => $order->id,
                        'customer_id' => $order->customer_id,
                        'customer_name' => $order->customer->name ?? 'Unknown',
                        'previous_status' => $request->previous_status ?? 'Unknown',
                        'new_status' => $order->status,
                        'timestamp' => now()->toDateTimeString(),
                        'message' => $message
                    ]
                ]));

                logger("📡 OrderStatusChanged event published to Redis");

            } catch (Exception $e) {
                logger("❌ Failed to publish OrderStatusChanged event: " . $e->getMessage());
            }
            return response()->json([
                'success' => true,
                'message' => 'Order processed successfully.',
                'data' => $order
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getAllAdmins()
    {
        try {
            $admins = admins::with(['user:id,username,email'])
                ->whereHas('user', function ($query) {
                    $query->where('username', '!=', 'admin')
                        ->where('password', '!=', 'admin');
                })
                ->get();
            $data = $admins->map(function ($admin) {
                $permissions = optional($admin->user)->getAllPermissions()->pluck('name');

                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'designation' => $admin->designation,
                    'contact_no' => $admin->contact_no,
                    'address' => $admin->address,
                    'profile_pic' => $admin->profile_pic ? asset($admin->profile_pic) : null,
                    'username' => optional($admin->user)->username,
                    'email' => optional($admin->user)->email,
                    'permissions' => $permissions,
                ];
            });

            return response()->json([
                'status' => 'success',
                'admins' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch admins',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function addAdmin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'name' => 'required|string|max:255',
                'designation' => 'nullable|string|max:255',
                'contact_no' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $data = $validator->validated();
            $customerRole = Role::where('name', 'Admin')->first();
            if (!$customerRole) {
                return response()->json(['error' => 'Customer role not found.'], 500);
            }
            $user = users::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $customerRole->id,
            ]);
            // Role::updateOrCreate(
            //     ['name' => $request->designation],
            // );


            $user->assignRole('Admin');
            if (
                strtolower($data['username']) === 'admin' &&
                $request->password === 'admin'
            ) {
                Permission::firstOrCreate(['name' => 'Add Admin']);
                $user->givePermissionTo('Add Admin');
            }
            $admin = new admins();
            $admin->user_id = $user->id;
            $admin->name = $data['name'];
            $admin->designation = $data['designation'] ?? null;
            $admin->contact_no = $data['contact_no'] ?? null;
            $admin->address = $data['address'] ?? null;
            if ($request->hasFile('profile_pic')) {
                $file = $request->file('profile_pic');
                $filename = 'admin_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('profile_pics'), $filename);
                $admin->profile_pic = 'profile_pics/' . $filename;
            }
            $admin->save();
            try {
                $message = "👤 New Admin Added: {$admin->name} ({$admin->designation})";
                $redis = new Redis();
                $redis->publish('admin-events', json_encode([
                    'event' => 'AdminAdded',
                    'data' => [
                        'admin_id' => $admin->id,
                        'name' => $admin->name,
                        'designation' => $admin->designation,
                        'username' => $user->username,
                        'timestamp' => now()->toDateTimeString(),
                        'message' => $message
                    ]
                ]));

                logger("📡 AdminAdded event published to Redis");

            } catch (Exception $e) {
                logger("❌ Failed to publish AdminAdded event: " . $e->getMessage());
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Admin added successfully',
                'admin' => $admin,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add admin',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateAdmin(Request $request, $id)
    {
        try {
            $admin = admins::find($id);
            if (!$admin) {
                return response()->json(['status' => 'error', 'message' => 'Admin not found.'], 404);
            }

            $user = $admin->user;
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Associated user not found.'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'contact_no' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            foreach (['name', 'designation', 'contact_no', 'address'] as $field) {
                if (array_key_exists($field, $data)) {
                    $admin->$field = $data[$field];
                }
            }

            if ($request->hasFile('profile_pic')) {
                $file = $request->file('profile_pic');
                $filename = 'admin_' . $admin->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('profile_pics'), $filename);
                $admin->profile_pic = 'profile_pics/' . $filename;
            }

            foreach (['username', 'email'] as $field) {
                if (array_key_exists($field, $data)) {
                    $user->$field = $data[$field];
                }
            }

            if (isset($data['password'])) {
                $user->password = $data['password'];
            }

            $user->save();
            $admin->save();
            try {
                $message = "🔄 Admin Updated: {$admin->name} (ID: {$admin->id})";
                $redis = new Redis();
                $redis->publish('admin-events', json_encode([
                    'event' => 'AdminUpdated',
                    'data' => [
                        'admin_id' => $admin->id,
                        'name' => $admin->name,
                        'designation' => $admin->designation,
                        'username' => $user->username,
                        'timestamp' => now()->toDateTimeString(),
                        'message' => $message
                    ]
                ]));

                logger("📡 AdminUpdated event published to Redis");

            } catch (Exception $e) {
                logger("❌ Failed to publish AdminUpdated event: " . $e->getMessage());
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Admin updated successfully',
                'admin' => $admin,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update admin',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function deleteAdmin($id)
    {
        try {
            $admin = admins::find($id);
            if (!$admin) {
                return response()->json(['status' => 'error', 'message' => 'Admin not found.'], 404);
            }

            $user = $admin->user;
            try {
                $message = "🗑️ Admin Deleted: {$admin->name} (ID: {$admin->id})";
                $redis = new Redis();
                $redis->publish('admin-events', json_encode([
                    'event' => 'AdminDeleted',
                    'data' => [
                        'admin_id' => $admin->id,
                        'name' => $admin->name,
                        'timestamp' => now()->toDateTimeString(),
                        'message' => $message
                    ]
                ]));

                logger("📡 AdminDeleted event published to Redis");
                $admin->delete();
                if ($user) {
                    $user->delete();
                }

            } catch (Exception $e) {
                logger("❌ Failed to publish AdminDeleted event: " . $e->getMessage());
            }


            return response()->json([
                'status' => 'success',
                'message' => 'Admin and associated user deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete admin',
                'error' => $e->getMessage(),
            ], 500);
        }
    }






    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'guard_name' => 'nullable|string',
        ]);

        try {
            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name ?? 'web',
            ]);
            try {

                $message = "🆕 New Permission Created: {$permission->name}";

                $redis = new Redis();
                $redis->publish('permission-events', json_encode([
                    'event' => 'PermissionCreated',
                    'data' => [
                        'permission_id' => $permission->id,
                        'permission_name' => $permission->name,
                        'guard_name' => $permission->guard_name,
                        'timestamp' => now()->toDateTimeString(),
                        'message' => $message
                    ]
                ]));

                logger("📡 PermissionCreated event published to Redis");

            } catch (Exception $e) {
                logger("❌ Failed to publish PermissionCreated event: " . $e->getMessage());
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Permission created successfully.',
                'permission' => $permission,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create permission.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updatePermission(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $id,
            'guard_name' => 'nullable|string',
        ]);

        try {
            $permission = Permission::findOrFail($id);

            $permission->update([
                'name' => $request->name,
                'guard_name' => $request->guard_name ?? $permission->guard_name,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Permission updated successfully.',
                'permission' => $permission,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update permission.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function deletePermission($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Permission deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete permission.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllPermissions()
    {
        try {
            $permissions = Permission::select('id', 'name', 'guard_name')->get();

            return response()->json([
                'status' => 'success',
                'permissions' => $permissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch permissions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function assignPermissionToAdmin(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        try {
            $admin = admins::with('user')->findOrFail($request->admin_id);
            $permission = Permission::findOrFail($request->permission_id);

            if (!$admin->user->hasPermissionTo($permission->name)) {
                $admin->user->givePermissionTo($permission->name);
            }
            try {
                $message = "🔑 Permission Assigned: {$permission->name} to {$admin->name}";

                $redis = new Redis();
                $redis->publish('permission-events', json_encode([
                    'event' => 'PermissionAssigned',
                    'data' => [
                        'admin_id' => $admin->id,
                        'admin_name' => $admin->name,
                        'permission_id' => $permission->id,
                        'permission_name' => $permission->name,
                        'timestamp' => now()->toDateTimeString(),
                        'message' => $message
                    ]
                ]));

                logger("📡 PermissionAssigned event published to Redis");

            } catch (Exception $e) {
                logger("❌ Failed to publish PermissionAssigned event: " . $e->getMessage());
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Permission assigned (or already exists).',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to assign permission.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function removePermissionFromAdmin(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        try {
            $admin = admins::with('user')->findOrFail($request->admin_id);
            $permission = Permission::findOrFail($request->permission_id);

            if ($admin->user->hasPermissionTo($permission->name)) {
                $admin->user->revokePermissionTo($permission->name);
            }
            try {
                $message = "🚫 Permission Removed: {$permission->name} from {$admin->name}";

                $redis = new Redis();
                $redis->publish('permission-events', json_encode([
                    'event' => 'PermissionRemoved',
                    'data' => [
                        'admin_id' => $admin->id,
                        'admin_name' => $admin->name,
                        'permission_id' => $permission->id,
                        'permission_name' => $permission->name,
                        'timestamp' => now()->toDateTimeString(),
                        'message' => $message
                    ]
                ]));

                logger("📡 PermissionRemoved event published to Redis");

            } catch (Exception $e) {
                logger("❌ Failed to publish PermissionRemoved event: " . $e->getMessage());
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Permission removed (if it existed).',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove permission.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    // In your Laravel API controller

public function getSalesAnalytics(Request $request)
{
    $period = $request->input('period', 'month');
    $year = $request->input('year', date('Y'));
    
    $query = orders::query()
        ->select(
            DB::raw('YEAR(order_datetime) as year'),
            DB::raw('MONTH(order_datetime) as month'),
            DB::raw('WEEK(order_datetime) as week'),
            DB::raw('DATE(order_datetime) as date'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total_payment) as total_revenue')
        )
        ->whereYear('order_datetime', $year);
    
    if ($period === 'day') {
        $query->groupBy('date');
    } elseif ($period === 'week') {
        $query->groupBy('year', 'week');
    } elseif ($period === 'month') {
        $query->groupBy('year', 'month');
    } else {
        $query->groupBy('year');
    }
    
    $results = $query->orderBy('year')
        ->when($period === 'month', fn($q) => $q->orderBy('month'))
        ->when($period === 'week', fn($q) => $q->orderBy('week'))
        ->when($period === 'day', fn($q) => $q->orderBy('date'))
        ->get();
    
    return response()->json([
        'success' => true,
        'data' => $results
    ]);
}

public function getTopCustomers()
{
    $customers = customers::withSum('orders', 'total_payment')
        ->withCount('orders')
        ->orderByDesc('orders_sum_total_payment')
        ->limit(5)
        ->get()
        ->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'total' => $customer->orders_sum_total_payment,
                'orders' => $customer->orders_count
            ];
        });
    
    return response()->json([
        'success' => true,
        'data' => $customers
    ]);
}

public function getTopProducts()
{
    $products = Product::withSum('orderItems', 'price_at_purchase')
        ->withSum('orderItems', 'quantity')
        ->orderByDesc('order_items_sum_price_at_purchase')
        ->limit(5)
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'revenue' => $product->order_items_sum_price_at_purchase,
                'quantity' => $product->order_items_sum_quantity
            ];
        });
    
    return response()->json([
        'success' => true,
        'data' => $products
    ]);
}
}

