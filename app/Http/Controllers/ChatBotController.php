<?php
// namespace App\Http\Controllers;
// use App\Models\chat_messages;
// use App\Models\customers;
// use Exception;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\File;
// use Predis\Client as Redis;
// use Illuminate\Support\Facades\Validator;
// class ChatBotController extends Controller
// {
//     public function getAllMessages($customerId)
//     {
//         $messages = chat_messages::where('customer_id', $customerId)
//             ->orderBy('created_at', 'asc')
//             ->get()
//             ->map(function ($msg) {
//                 return [
//                     'id' => $msg->id,
//                     'message' => $msg->is_deleted ? 'This message has been deleted' : $msg->message,
//                     'media_type' => $msg->media_type,
//                     'media_url' => $msg->media_url ? asset('chat_media/' . $msg->media_url) : null,
//                     'is_opened' => $msg->is_opened,
//                     'is_deleted' => $msg->is_deleted,
//                     'reply_to' => $msg->reply_to,
//                     'sender_type' => $msg->sender_type,
//                     'sender_id' => $msg->sender_id,
//                     'customer_id' => $msg->customer_id,
//                     'created_at' => $msg->created_at,
//                 ];
//             });

//         return response()->json($messages);
//     }
//     public function sendMessage(Request $request)
//     {
//         try {
//             $validated = Validator::make($request->all(), [
//                 'customer_id' => 'required|exists:customers,id',
//                 'sender_type' => 'required|in:customer',
//                 'sender_id' => 'required|integer',
//                 'message' => 'nullable|string',
//                 'media' => 'nullable|file|max:20480',
//                 'media_type' => 'nullable|in:image,video,link,file',
//                 'reply_to' => 'nullable|exists:chat_messages,id',
//             ])->validate();

//             $fileName = null;
//             if ($request->hasFile('media')) {
//                 $fileName = uniqid() . '_' . $request->file('media')->getClientOriginalName();
//                 $request->file('media')->move(public_path('chat_media'), $fileName);
//             }

//             $chat = chat_messages::create([
//                 'customer_id' => $request->customer_id,
//                 'sender_type' => 'customer',
//                 'sender_id' => $request->sender_id,
//                 'message' => $request->message,
//                 'media_type' => $request->media_type ?? 'none',
//                 'media_url' => $fileName,
//                 'reply_to' => $request->reply_to,
//                 'is_opened' => false,
//                 'is_deleted' => false,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ]);

//             $this->publishRedisEvent('ChatMessageSent', $chat);

//             return response()->json(['status' => 'sent', 'data' => $chat], 201);
//         } catch (\Illuminate\Validation\ValidationException $e) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Validation failed',
//                 'errors' => $e->errors()
//             ], 422);
//         } catch (Exception $e) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Something went wrong',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }
//     public function updateMessage(Request $request, $id)
//     {
//         $chat = chat_messages::findOrFail($id);

//         $chat->update([
//             'message' => $request->has('message') ? $request->message : null,
//             'media_type' => $request->has('media_type') ? $request->media_type : 'none',
//             'media_url' => $request->hasFile('media') ? $this->replaceMedia($request, $chat) : null,
//             'reply_to' => $request->has('reply_to') ? $request->reply_to : null,
//             'updated_at' => now()
//         ]);

//         $this->publishRedisEvent('ChatMessageUpdated', $chat);

//         return response()->json(['status' => 'updated', 'data' => $chat]);
//     }
//     public function deleteMessage($id)
//     {
//         try {
//             $chat = chat_messages::findOrFail($id);

//             $chat->update([
//                 'message' => null,
//                 'is_deleted' => true,
//                 'updated_at' => now(),
//             ]);

//             $this->publishRedisEvent('ChatMessageDeleted', $chat);

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Message deleted successfully',
//             ]);
//         } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Message not found',
//             ], 404);
//         } catch (Exception $e) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'An error occurred while deleting the message',
//                 'error' => $e->getMessage(),
//             ], 500);
//         }
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
//              $redis = new Redis();
//                $redis->publish('chat-events', json_encode([
//                 'event' => $event,
//                 'data' => [
//                     'message_id' => $chat->id,
//                     'customer_id' => $chat->customer_id,
//                     'sender_id' => $chat->sender_id,
//                     'sender_type' => $chat->sender_type,
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

//             logger("📡 $event event published to Redis");
//         } catch (Exception $e) {
//             logger("❌ Failed to publish $event event: " . $e->getMessage());
//         }
//     }
// }

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\chat_messages;
use Illuminate\Support\Facades\Log;
use Predis\Client as Redis;

class ChatBotController extends Controller
{
    public function getAllMessages($customerId)
    {
        $messages = chat_messages::where('customer_id', $customerId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->is_deleted ? 'This message has been deleted' : $msg->message,
                    'media_type' => $msg->media_type,
                    'media_url' => $msg->media_url ? asset('chat_media/' . $msg->media_url) : null,
                    'is_opened' => $msg->is_opened,
                    'is_deleted' => $msg->is_deleted,
                    'reply_to' => $msg->reply_to,
                    'sender_type' => $msg->sender_type,
                    'sender_id' => $msg->sender_id,
                    'customer_id' => $msg->customer_id,
                    'created_at' => $msg->created_at,
                ];
            });

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'sender_type' => 'required|in:customer',
                'sender_id' => 'required|integer',
                'message' => 'nullable|string',
                'media' => 'nullable|file|max:20480',
                'media_type' => 'nullable|in:image,video,link,file',
                'reply_to' => 'nullable|exists:chat_messages,id',
            ])->validate();

            $fileName = null;
            if ($request->hasFile('media')) {
                $fileName = uniqid() . '_' . $request->file('media')->getClientOriginalName();
                $request->file('media')->move(public_path('chat_media'), $fileName);
            }

            $chat = chat_messages::create([
                'customer_id' => $request->customer_id,
                'sender_type' => 'customer',
                'sender_id' => $request->sender_id,
                'message' => $request->message,
                'media_type' => $request->media_type ?? 'none',
                'media_url' => $fileName,
                'reply_to' => $request->reply_to,
                'is_opened' => false,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->publishRedisEvent('ChatMessageSent', $chat);

            return response()->json(['status' => 'sent', 'data' => $chat], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateMessage(Request $request, $id)
    {
        try {
            $chat = chat_messages::findOrFail($id);

            $chat->update([
                'message' => $request->has('message') ? $request->message : null,
                'media_type' => $request->has('media_type') ? $request->media_type : 'none',
                'media_url' => $request->hasFile('media') ? $this->replaceMedia($request, $chat) : null,
                'reply_to' => $request->has('reply_to') ? $request->reply_to : null,
                'updated_at' => now()
            ]);

            $this->publishRedisEvent('ChatMessageUpdated', $chat);

            return response()->json(['status' => 'updated', 'data' => $chat]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteMessage($id)
    {
        try {
            $chat = chat_messages::findOrFail($id);

            $chat->update([
                'message' => null,
                'is_deleted' => true,
                'updated_at' => now(),
            ]);

            $this->publishRedisEvent('ChatMessageDeleted', $chat);

            return response()->json([
                'status' => 'success',
                'message' => 'Message deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the message',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function replaceMedia(Request $request, $chat)
    {
        if ($chat->media_url && File::exists(public_path('chat_media/' . $chat->media_url))) {
            File::delete(public_path('chat_media/' . $chat->media_url));
        }

        $fileName = uniqid() . '_' . $request->file('media')->getClientOriginalName();
        $request->file('media')->move(public_path('chat_media'), $fileName);
        return $fileName;
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
                    'sender_id' => $chat->sender_id,
                    'sender_type' => $chat->sender_type,
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

            Log::info("📡 $event event published to Redis");
        } catch (\Exception $e) {
            Log::error("❌ Failed to publish $event event: " . $e->getMessage());
        }
    }
}