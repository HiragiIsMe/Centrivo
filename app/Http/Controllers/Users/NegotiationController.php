<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NegotiationController extends Controller
{
    public function initiate(Service $service)
    {
        $user = Auth::user();

        if ($user->id === $service->seller_id) {
            return redirect()->back()->with('error', 'Anda tidak bisa melakukan negosiasi pada layanan Anda sendiri.');
        }

        $activeRequest = ServiceRequest::where('service_id', $service->id)
            ->where('user_id', $user->id)
            ->where(function($q) {
                $q->where('status', 'negotiating')
                  ->orWhereHas('transaction', function($t) {
                      $t->where('transaction_status', '!=', 'completed');
                  });
            })
            ->first();

        if ($activeRequest) {
            return redirect()->route('negotiation.show', $activeRequest->id);
        }

        $serviceRequest = ServiceRequest::create([
            'service_id' => $service->id,
            'user_id' => $user->id,
            'seller_id' => $service->seller_id,
            'status' => 'negotiating',
        ]);

        return redirect()->route('negotiation.show', $serviceRequest->id);
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($user->id !== $serviceRequest->user_id && $user->id !== $serviceRequest->seller_id) {
            abort(403, 'Unauthorized access.');
        }

        $serviceRequest->load(['messages.sender.userProfile', 'service.images', 'buyer.userProfile', 'seller.sellerProfile']);

        $serviceRequest->messages()->where('sender_id', '!=', $user->id)->where('is_read', false)->update(['is_read' => true]);

        return view('market.negotiation', compact('serviceRequest'));
    }

    public function sendMessage(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($user->id !== $serviceRequest->user_id && $user->id !== $serviceRequest->seller_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'required_without:offered_price|string|max:1000',
            'offered_price' => 'nullable|numeric|min:0',
            'scheduled_date' => 'nullable|date',
        ]);

        $msg = Message::create([
            'request_id' => $serviceRequest->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'offered_price' => $request->offered_price,
            'scheduled_date' => $request->scheduled_date,
        ]);

        return response()->json(['success' => true, 'message_id' => $msg->id]);
    }

    public function fetchMessages(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($user->id !== $serviceRequest->user_id && $user->id !== $serviceRequest->seller_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $afterId = $request->query('after_id', 0);

        $messages = Message::where('request_id', $serviceRequest->id)
            ->where('id', '>', $afterId)
            ->with('sender.userProfile')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) use ($user, $serviceRequest) {
                $isMe = $msg->sender_id === $user->id;
                $isBuyer = $user->id === $serviceRequest->user_id;
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'offered_price' => $msg->offered_price,
                    'offered_price_formatted' => $msg->offered_price ? 'Rp ' . number_format($msg->offered_price, 0, ',', '.') : null,
                    'scheduled_date' => $msg->scheduled_date ? \Carbon\Carbon::parse($msg->scheduled_date)->format('d M Y, H:i') : null,
                    'time' => $msg->created_at->format('H:i'),
                    'is_me' => $isMe,
                    'is_buyer' => $isBuyer,
                    'show_checkout' => !$isMe && $isBuyer && $msg->offered_price && $serviceRequest->status === 'negotiating',
                    'checkout_url' => $msg->offered_price ? route('checkout.show', $msg->id) : null,
                ];
            });

        Message::where('request_id', $serviceRequest->id)
            ->where('id', '>', $afterId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['messages' => $messages]);
    }

    public function deleteMessage(Message $message)
    {
        $user = Auth::user();

        if ($user->id !== $message->sender_id) {
            return response()->json(['error' => 'Anda hanya bisa menghapus pesan milik Anda'], 403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    public function deleteConversation(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if ($user->id !== $serviceRequest->user_id && $user->id !== $serviceRequest->seller_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $serviceRequest->messages()->delete();
        $serviceRequest->delete();

        return response()->json(['success' => true]);
    }
}
