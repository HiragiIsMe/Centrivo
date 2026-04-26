<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $conversations = ServiceRequest::with([
                'service.images',
                'seller.sellerProfile',
                'buyer.userProfile',
                'messages' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])
            ->where('user_id', $userId)
            ->latest('updated_at')
            ->get();

        return view('market.chats', compact('conversations'));
    }

    public function destroyConversation(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($user->id !== $serviceRequest->user_id && $user->id !== $serviceRequest->seller_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $serviceRequest->messages()->delete();
        $serviceRequest->delete();

        return response()->json(['success' => true]);
    }

    public function destroyMessage(Message $message)
    {
        $user = Auth::user();

        if ($user->id !== $message->sender_id) {
            return response()->json(['error' => 'Anda hanya bisa menghapus pesan milik Anda'], 403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }
}
