<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\customers;
use App\Models\users;


class CustomerController extends Controller
{
    public function index()
    {
        return view('customer.home');
    }

    public function updateCustomerProfile(Request $request, $id)
    {
        $customer = customers::find($id);

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found.',
            ], 404);
        }


        $user = users::find($customer->user_id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Associated user not found.',
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone_no' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        foreach (['name', 'address', 'phone_no', 'dob', 'gender'] as $field) {
            if (array_key_exists($field, $data)) {
                $customer->$field = $data[$field];
            }
        }
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = 'customer_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('profile_pics'), $filename);
            $customer->profile_pic = 'profile_pics/' . $filename;
        }

        $customer->save();
        foreach (['username', 'email'] as $field) {
            if (array_key_exists($field, $data)) {
                $user->$field = $data[$field];
            }
        }

        if (isset($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();
        $imageUrl = $customer->profile_pic ? asset($customer->profile_pic) : null;
        return response()->json([
            'status' => 'success',
            'message' => 'Customer and associated user updated successfully.',
            'customer' => $customer,
            'user' => $user,
            'profile_pic_url' => $imageUrl,
        ]);
    }
    public function allCustomer()
    {
        try {
            $customers = customers::with(['users:id,username,email', 'orders'])->get();

            $data = $customers->map(function ($customer) {
                $totalOrders = $customer->orders->count();
                $totalAmount = $customer->orders->sum('total_payment');

                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'address' => $customer->address,
                    'phone_no' => $customer->phone_no,
                    'dob' => $customer->dob,
                    'gender' => $customer->gender,
                    'profile_pic' => $customer->profile_pic ? asset($customer->profile_pic) : asset('images/temp.jpg'),
                    'username' => optional($customer->users)->username,
                    'email' => optional($customer->users)->email,
                    'total_orders' => $totalOrders,
                    'total_amount_spent' => $totalAmount,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'All customers fetched successfully.',
                'customers' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while fetching customers.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
