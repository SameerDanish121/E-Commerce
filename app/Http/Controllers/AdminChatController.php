<?php

// namespace App\Http\Controllers;

// use App\Models\chat_messages;
// use App\Models\customers;
// use Exception;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\File;
// use Predis\Client as Redis;
// use Illuminate\Support\Facades\Validator;

// class AdminChatController extends Controller
// {
//     public function getGroupedMessages()
//     {
//         $customers = chat_messages::
//             select('customer_id')
//             ->distinct()
//             ->pluck('customer_id');

//         $result = [];

//         foreach ($customers as $customerId) {
//             $customer = customers::with('users:id,email')
//                 ->where('id', $customerId)
//                 ->first();

//             if (!$customer)
//                 continue;

//             $customer->profile_pic = $customer->profile_pic ? asset($customer->profile_pic) : null;

//             $messages = chat_messages::where('customer_id', $customerId)
//                 ->orderBy('created_at', 'asc')
//                 ->get()
//                 ->map(function ($msg) {
//                     return [
//                         'id' => $msg->id,
//                         'message' => $msg->is_deleted ? 'This message has been deleted' : $msg->message,
//                         'media_type' => $msg->media_type,
//                         'media_url' => $msg->media_url ? asset('chat_media/' . $msg->media_url) : null,
//                         'reply_to' => $msg->reply_to,
//                         'is_opened' => $msg->is_opened,
//                         'is_deleted' => $msg->is_deleted,
//                         'sender_type' => $msg->sender_type,
//                         'customer_id' => $msg->customer_id,
//                         'created_at' => $msg->created_at,
//                     ];
//                 });

//             $result[] = [
//                 'customer' => [
//                     'id' => $customer->id,
//                     'name' => $customer->name,
//                     'email' => optional($customer->users)->email,
//                     'profile_pic' => $customer->profile_pic?asset($customer->profile_pic):null,
//                 ],
//                 'messages' => $messages,
//             ];
//         }

//         return response()->json($result);
//     }

//     public function sendMessage(Request $request)
//     {
//         try {
//             $validated = Validator::make($request->all(), [
//                 'customer_id' => 'required|exists:customers,id',
//                 'message' => 'nullable|string',
//                 'media' => 'nullable|file|max:20480',
//                 'media_type' => 'nullable|in:image,video,link,file',
//                 'reply_to' => 'nullable|exists:chat_messages,id',
//             ])->validate();

//             $fileName = null;
//             if ($request->hasFile('media')) {
//                 $file = $request->file('media');
//                 $fileName = uniqid() . '_' . $file->getClientOriginalName();
//                 $file->move(public_path('chat_media'), $fileName);
//             }

//             $chat = chat_messages::create([
//                 'customer_id' => $request->customer_id,
//                 'sender_type' => 'admin',
//                 'sender_id' => null,
//                 'message' => $request->message,
//                 'media_type' => $request->media_type ?? 'none',
//                 'media_url' => $fileName,
//                 'reply_to' => $request->reply_to,
//                 'is_opened' => false,
//                 'is_deleted' => false,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ]);

//             // Publish to Redis
//             $this->publishRedisEvent('AdminMessageSent', $chat);

//             return response()->json([
//                 'status' => 'sent',
//                 'data' => $chat
//             ]);
//         } catch (\Illuminate\Validation\ValidationException $e) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Validation failed',
//                 'errors' => $e->errors()
//             ], 422);
//         } catch (\Exception $e) {


//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Something went wrong while sending the message',
//             ], 500);
//         }
//     }
//     public function updateMessage(Request $request, $id)
//     {
//         $chat = chat_messages::where('sender_type', 'admin')->findOrFail($id);

//         $chat->update([
//             'message' => $request->has('message') ? $request->message : null,
//             'media_type' => $request->has('media_type') ? $request->media_type : 'none',
//             'media_url' => $request->hasFile('media') ? $this->replaceMedia($request, $chat) : null,
//             'reply_to' => $request->has('reply_to') ? $request->reply_to : null,
//             'updated_at' => now(),
//         ]);

//         $this->publishRedisEvent('AdminMessageUpdated', $chat);

//         return response()->json(['status' => 'updated', 'data' => $chat]);
//     }
//     public function deleteMessage($id)
//     {
//         $chat = chat_messages::where('sender_type', 'admin')->findOrFail($id);
//         $chat->update([
//             'message' => null,
//             'is_deleted' => true,
//             'updated_at' => now()
//         ]);

//         $this->publishRedisEvent('AdminMessageDeleted', $chat);

//         return response()->json(['status' => 'deleted']);
//     }
//     private function replaceMedia(Request $request, $chat)
//     {
//         if ($chat->media_url && File::exists(public_path('chat_media/' . $chat->media_url))) {
//             File::delete(public_path('chat_media/' . $chat->media_url));
//         }

//         $fileName = uniqid() . '_' . $request->file('media')->getClientOriginalName();
//         $request->file('media')->move(public_path('chat_media'), $fileName);
//         return $fileName;
//     }

//     private function publishRedisEvent($event, $chat)
//     {
//         try {
//             $message = "📨 $event: Message ID {$chat->id}";
//             $redis = new Redis();
//             $redis->publish('chat-events', json_encode([
//                 'event' => $event,
//                 'data' => [
//                     'message_id' => $chat->id,
//                     'customer_id' => $chat->customer_id,
//                     'sender_type' => 'admin',
//                     'message' => $chat->message,
//                     'media_type' => $chat->media_type,
//                     'media_url' => $chat->media_url ? asset('chat_media/' . $chat->media_url) : null,
//                     'reply_to' => $chat->reply_to,
//                     'is_opened' => $chat->is_opened,
//                     'is_deleted' => $chat->is_deleted,
//                     'timestamp' => now()->toDateTimeString(),
//                     'message_' => $message
//                 ]
//             ]));
//             logger("📡 $event published to Redis");
//         } catch (Exception $e) {
//             logger("❌ Redis publish failed: " . $e->getMessage());
//         }
//     }
// }


namespace App\Http\Controllers;

use App\Models\chat_messages;
use App\Models\customers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Predis\Client as Redis;
use Illuminate\Support\Facades\Validator;

class AdminChatController extends Controller
{
    public function getGroupedMessages()
    {
        $customers = chat_messages::
            select('customer_id')
            ->distinct()
            ->pluck('customer_id');

        $result = [];

        foreach ($customers as $customerId) {
            $customer = customers::with('users:id,email')
                ->where('id', $customerId)
                ->first();

            if (!$customer)
                continue;

            $customer->profile_pic = $customer->profile_pic ? asset($customer->profile_pic) : null;

            $messages = chat_messages::where('customer_id', $customerId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($msg) {
                    return [
                        'id' => $msg->id,
                        'message' => $msg->is_deleted ? 'This message has been deleted' : $msg->message,
                        'media_type' => $msg->media_type,
                        'media_url' => $msg->media_url ? asset('chat_media/' . $msg->media_url) : null,
                        'reply_to' => $msg->reply_to,
                        'is_opened' => $msg->is_opened,
                        'is_deleted' => $msg->is_deleted,
                        'sender_type' => $msg->sender_type,
                        'customer_id' => $msg->customer_id,
                        'created_at' => $msg->created_at,
                    ];
                });

            $result[] = [
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => optional($customer->users)->email,
                    'profile_pic' => $customer->profile_pic,
                ],
                'messages' => $messages,
            ];
        }

        // Sort by last message date (newest first)
        usort($result, function($a, $b) {
            $aLastMsg = end($a['messages'])['created_at'] ?? 0;
            $bLastMsg = end($b['messages'])['created_at'] ?? 0;
            return strtotime($bLastMsg) - strtotime($aLastMsg);
        });

        return response()->json($result);
    }

    public function sendMessage(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'message' => 'nullable|string',
                'media' => 'nullable|file|max:20480',
                'media_type' => 'nullable|in:image,video,link,file',
                'reply_to' => 'nullable|exists:chat_messages,id',
            ])->validate();

            $fileName = null;
            if ($request->hasFile('media')) {
                $file = $request->file('media');
                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('chat_media'), $fileName);
            }

            $chat = chat_messages::create([
                'customer_id' => $request->customer_id,
                'sender_type' => 'admin',
                'sender_id' => null,
                'message' => $request->message,
                'media_type' => $request->media_type ?? 'none',
                'media_url' => $fileName,
                'reply_to' => $request->reply_to,
                'is_opened' => false,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->publishRedisEvent('AdminMessageSent', $chat);

            return response()->json([
                'status' => 'sent',
                'data' => $chat
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while sending the message',
            ], 500);
        }
    }

    public function updateMessage(Request $request, $id)
    {
        $chat = chat_messages::where('sender_type', 'admin')->findOrFail($id);

        $fileName = $chat->media_url;
        if ($request->hasFile('media')) {
            if ($fileName && File::exists(public_path('chat_media/' . $fileName))) {
                File::delete(public_path('chat_media/' . $fileName));
            }
            $file = $request->file('media');
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('chat_media'), $fileName);
        }

        $chat->update([
            'message' => $request->has('message') ? $request->message : $chat->message,
            'media_type' => $request->has('media_type') ? $request->media_type : $chat->media_type,
            'media_url' => $fileName,
            'reply_to' => $request->has('reply_to') ? $request->reply_to : $chat->reply_to,
            'updated_at' => now(),
        ]);

        $this->publishRedisEvent('AdminMessageUpdated', $chat);

        return response()->json(['status' => 'updated', 'data' => $chat]);
    }

    public function deleteMessage($id)
    {
        $chat = chat_messages::where('sender_type', 'admin')->findOrFail($id);
        $chat->update([
            'message' => null,
            'is_deleted' => true,
            'updated_at' => now()
        ]);

        $this->publishRedisEvent('AdminMessageDeleted', $chat);

        return response()->json(['status' => 'deleted']);
    }

    public function markMessagesAsRead($customerId)
    {
        chat_messages::where('customer_id', $customerId)
            ->where('sender_type', 'customer')
            ->where('is_opened', false)
            ->update(['is_opened' => true, 'updated_at' => now()]);

        return response()->json(['status' => 'success']);
    }

    private function publishRedisEvent($event, $chat)
    {
        try {
            $message = "📨 $event: Message ID {$chat->id}";
            $redis = new Redis();
            $redis->publish('chat-events', json_encode([
                'event' => $event,
                'data' => [
                    'message_id' => $chat->id,
                    'customer_id' => $chat->customer_id,
                    'sender_type' => 'admin',
                    'message' => $chat->message,
                    'media_type' => $chat->media_type,
                    'media_url' => $chat->media_url ? asset('chat_media/' . $chat->media_url) : null,
                    'reply_to' => $chat->reply_to,
                    'is_opened' => $chat->is_opened,
                    'is_deleted' => $chat->is_deleted,
                    'timestamp' => now()->toDateTimeString(),
                    'message_' => $message
                ]
            ]));
            logger("📡 $event published to Redis");
        } catch (Exception $e) {
            logger("❌ Redis publish failed: " . $e->getMessage());
        }
    }
}