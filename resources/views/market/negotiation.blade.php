<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Negosiasi - Centrivo</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'color1': '#628ECB',
                        'color2': '#8AAEE0',
                        'color3': '#B1C9EF',
                        'color4': '#D5DEEF',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .chat-container { height: calc(100vh - 220px); }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .msg-animate { animation: fadeInUp 0.3s ease-out; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 h-screen flex flex-col overflow-hidden">

    <!-- Header -->
    <nav class="bg-white/80 backdrop-blur-md z-50 border-b border-gray-100 flex-shrink-0">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ redirect()->back()->getTargetUrl() }}" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="text-xl">←</span>
                </a>
                <div class="flex items-center gap-3">
                    @php
                        $isBuyer = Auth::id() === $serviceRequest->user_id;
                        $partnerName = $isBuyer 
                            ? ($serviceRequest->seller->sellerProfile->brand_name ?? 'Penyedia Jasa') 
                            : ($serviceRequest->buyer->userProfile->name ?? 'Pelanggan');
                    @endphp
                    <div class="w-10 h-10 bg-color1 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                        {{ substr($partnerName, 0, 2) }}
                    </div>
                    <div>
                        <h1 class="font-black text-slate-800 leading-tight">{{ $partnerName }}</h1>
                        <p class="text-[11px] font-bold text-color1">Negosiasi Layanan</p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-600 line-clamp-1 max-w-[200px]">{{ $serviceRequest->service->service_name }}</p>
                    <p class="text-[11px] text-slate-400 font-medium">Harga Awal: Rp {{ number_format($serviceRequest->service->start_price, 0, ',', '.') }}</p>
                </div>
                <button onclick="deleteConversation()" class="p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all" title="Hapus Percakapan">
                    🗑️
                </button>
            </div>
        </div>
    </nav>

    <!-- Chat Area -->
    <main class="flex-grow overflow-y-auto chat-container p-6 flex flex-col" id="chatBox">
        <div class="max-w-4xl mx-auto w-full flex-grow flex flex-col gap-4 justify-end" id="messagesContainer">
            
            <div class="text-center my-6">
                <span class="bg-gray-200 text-gray-500 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-widest">
                    Ruang Negosiasi Dibuka
                </span>
            </div>

            <!-- Service Info Card -->
            <div class="flex justify-center mb-6">
                <div class="bg-white border border-gray-100 rounded-[24px] p-4 shadow-sm max-w-sm w-full flex gap-4 items-center">
                    <div class="w-20 h-20 rounded-[16px] bg-slate-100 overflow-hidden flex-shrink-0">
                        @if($serviceRequest->service->images->count() > 0)
                            <img src="{{ asset('storage/' . $serviceRequest->service->images->first()->image_path) }}" class="w-full h-full object-cover" alt="Service Image">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-400 font-bold">No Image</div>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-color1 uppercase tracking-widest mb-1">Layanan yang Dinegosiasikan</p>
                        <h3 class="font-bold text-slate-800 text-sm line-clamp-2 leading-tight mb-1">{{ $serviceRequest->service->service_name }}</h3>
                        <p class="text-xs font-black text-slate-600">Mulai dari Rp {{ number_format($serviceRequest->service->start_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Initial messages rendered server-side --}}
            @foreach($serviceRequest->messages as $msg)
                @php $isMe = $msg->sender_id === Auth::id(); @endphp
                <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}" data-msg-id="{{ $msg->id }}">
                    <div class="max-w-[80%] sm:max-w-[60%] {{ $isMe ? 'bg-color1 text-white rounded-tl-2xl rounded-tr-2xl rounded-bl-2xl' : 'bg-white border border-gray-100 text-slate-700 rounded-tr-2xl rounded-tl-2xl rounded-br-2xl' }} p-4 shadow-sm relative group">

                        @if($isMe)
                        <button onclick="deleteMessage({{ $msg->id }}, this)" class="absolute -top-2 {{ $isMe ? '-left-2' : '-right-2' }} w-6 h-6 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-red-600" title="Hapus">
                            ✕
                        </button>
                        @endif

                        @if($msg->message)
                            <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $msg->message }}</p>
                        @endif

                        @if($msg->offered_price)
                            <div class="mt-2 pt-2 {{ $isMe ? 'border-white/20' : 'border-gray-100' }} border-t">
                                <p class="text-[10px] uppercase font-bold tracking-widest opacity-70 mb-1">Penawaran Harga:</p>
                                <p class="text-lg font-black">Rp {{ number_format($msg->offered_price, 0, ',', '.') }}</p>
                                
                                @if($msg->scheduled_date)
                                    <p class="text-xs font-bold mt-1 opacity-90 flex items-center gap-1">
                                        🗓️ {{ \Carbon\Carbon::parse($msg->scheduled_date)->format('d M Y, H:i') }}
                                    </p>
                                @endif
                                
                                @if(!$isMe && Auth::id() === $serviceRequest->user_id)
                                    @if($msg->is_checkout)
                                        <div class="mt-3 flex gap-2">
                                            <span class="bg-gray-100 text-slate-500 text-xs font-bold px-4 py-2 rounded-xl inline-block border border-gray-200">Sudah di-Checkout</span>
                                        </div>
                                    @elseif($serviceRequest->status === 'negotiating' || $serviceRequest->status === 'open')
                                        <div class="mt-3 flex gap-2">
                                            <a href="{{ route('checkout.show', $msg->id) }}" class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition-colors shadow-sm inline-block">Terima & Checkout</a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif

                        <span class="text-[9px] font-bold opacity-50 mt-2 block {{ $isMe ? 'text-right' : 'text-left' }}">
                            {{ $msg->created_at->format('H:i') }}
                        </span>
                    </div>
                </div>
            @endforeach

        </div>
    </main>

    <!-- Input Area -->
    <footer class="bg-white border-t border-gray-100 p-4 flex-shrink-0">
        <div class="max-w-4xl mx-auto relative">
            <form id="chatForm" class="flex items-end gap-3">
                <div class="flex-grow bg-gray-50 border border-gray-200 rounded-[24px] p-2 flex flex-col focus-within:ring-2 focus-within:ring-color1/20 focus-within:border-color1 transition-all">
                    
                    @if(Auth::id() === $serviceRequest->seller_id)
                    <!-- Form Penawaran Harga & Jadwal (Khusus Seller) -->
                    <div class="flex items-center gap-2 px-3 pb-2 border-b border-gray-200 mb-2 flex-wrap">
                        <div class="flex items-center gap-2 flex-1 min-w-[150px]">
                            <span class="text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" id="offeredPrice" placeholder="Harga Penawaran..." class="bg-transparent text-sm font-bold text-slate-800 outline-none w-full placeholder:font-normal">
                        </div>
                        <div class="w-px h-4 bg-gray-300 hidden sm:block"></div>
                        <div class="flex items-center gap-2 flex-1 min-w-[150px]">
                            <span class="text-xs font-bold text-slate-400">📅</span>
                            <input type="datetime-local" id="scheduledDate" class="bg-transparent text-sm font-bold text-slate-800 outline-none w-full placeholder:font-normal text-slate-500 cursor-pointer">
                        </div>
                    </div>
                    @endif

                    <textarea id="messageInput" rows="1" placeholder="Ketik pesan Anda..." class="w-full bg-transparent resize-none outline-none px-3 py-2 text-sm text-slate-700 no-scrollbar" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px';"></textarea>
                </div>

                <button type="submit" id="sendBtn" class="w-12 h-12 bg-color1 hover:bg-color2 text-white rounded-full flex items-center justify-center transition-all shadow-lg shadow-color1/30 flex-shrink-0 transform hover:scale-105 active:scale-95">
                    <span class="text-xl transform rotate-45 -ml-1 -mb-1">✈</span>
                </button>
            </form>
        </div>
    </footer>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const SEND_URL = "{{ route('negotiation.send', $serviceRequest->id) }}";
        const FETCH_URL = "{{ route('negotiation.fetch', $serviceRequest->id) }}";
        const IS_SELLER = {{ Auth::id() === $serviceRequest->seller_id ? 'true' : 'false' }};
        const IS_BUYER = {{ Auth::id() === $serviceRequest->user_id ? 'true' : 'false' }};
        const STATUS = "{{ $serviceRequest->status }}";

        const chatBox = document.getElementById('chatBox');
        const messagesContainer = document.getElementById('messagesContainer');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');

        // Track last message id for polling
        let lastMsgId = 0;
        document.querySelectorAll('[data-msg-id]').forEach(el => {
            const id = parseInt(el.dataset.msgId);
            if (id > lastMsgId) lastMsgId = id;
        });

        // Scroll to bottom
        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        scrollToBottom();

        // Build a message bubble HTML from data
        function buildBubbleHtml(msg) {
            const align = msg.is_me ? 'items-end' : 'items-start';
            const bubbleClass = msg.is_me
                ? 'bg-[#628ECB] text-white rounded-tl-2xl rounded-tr-2xl rounded-bl-2xl'
                : 'bg-white border border-gray-100 text-slate-700 rounded-tr-2xl rounded-tl-2xl rounded-br-2xl';
            const borderColor = msg.is_me ? 'border-white/20' : 'border-gray-100';
            const timeAlign = msg.is_me ? 'text-right' : 'text-left';

            let html = `<div class="flex flex-col ${align} msg-animate" data-msg-id="${msg.id}">`;
            html += `<div class="max-w-[80%] sm:max-w-[60%] ${bubbleClass} p-4 shadow-sm relative group">`;

            if (msg.is_me) {
                html += `<button onclick="deleteMessage(${msg.id}, this)" class="absolute -top-2 -left-2 w-6 h-6 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-red-600" title="Hapus">✕</button>`;
            }

            if (msg.message) {
                html += `<p class="text-sm leading-relaxed whitespace-pre-wrap">${escapeHtml(msg.message)}</p>`;
            }

            if (msg.offered_price) {
                html += `<div class="mt-2 pt-2 ${borderColor} border-t">`;
                html += `<p class="text-[10px] uppercase font-bold tracking-widest opacity-70 mb-1">Penawaran Harga:</p>`;
                html += `<p class="text-lg font-black">${msg.offered_price_formatted}</p>`;

                if (msg.scheduled_date) {
                    html += `<p class="text-xs font-bold mt-1 opacity-90 flex items-center gap-1">🗓️ ${msg.scheduled_date}</p>`;
                }

                if (msg.show_checkout && msg.checkout_url) {
                    html += `<div class="mt-3 flex gap-2">`;
                    html += `<a href="${msg.checkout_url}" class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition-colors shadow-sm inline-block">Terima & Checkout</a>`;
                    html += `</div>`;
                }

                html += `</div>`;
            }

            html += `<span class="text-[9px] font-bold opacity-50 mt-2 block ${timeAlign}">${msg.time}</span>`;
            html += `</div></div>`;

            return html;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // AJAX: Send message
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const message = messageInput.value.trim();
            const offeredPrice = document.getElementById('offeredPrice')?.value || null;
            const scheduledDate = document.getElementById('scheduledDate')?.value || null;

            if (!message && !offeredPrice) return;

            // Disable button
            sendBtn.disabled = true;
            sendBtn.classList.add('opacity-50');

            const body = { message, offered_price: offeredPrice, scheduled_date: scheduledDate };

            try {
                const res = await fetch(SEND_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(body),
                });

                if (res.ok) {
                    // Clear inputs
                    messageInput.value = '';
                    messageInput.style.height = '';
                    if (document.getElementById('offeredPrice')) document.getElementById('offeredPrice').value = '';
                    if (document.getElementById('scheduledDate')) document.getElementById('scheduledDate').value = '';

                    // Immediately poll for the new message
                    await pollMessages();
                }
            } catch (err) {
                console.error('Send failed:', err);
            } finally {
                sendBtn.disabled = false;
                sendBtn.classList.remove('opacity-50');
            }
        });

        // AJAX: Poll for new messages
        async function pollMessages() {
            try {
                const res = await fetch(`${FETCH_URL}?after_id=${lastMsgId}`, {
                    headers: { 'Accept': 'application/json' },
                });

                if (!res.ok) return;

                const data = await res.json();

                if (data.messages && data.messages.length > 0) {
                    const wasAtBottom = (chatBox.scrollTop + chatBox.clientHeight) >= (chatBox.scrollHeight - 60);

                    data.messages.forEach(msg => {
                        // Prevent duplicates
                        if (document.querySelector(`[data-msg-id="${msg.id}"]`)) return;

                        messagesContainer.insertAdjacentHTML('beforeend', buildBubbleHtml(msg));
                        lastMsgId = msg.id;
                    });

                    if (wasAtBottom) scrollToBottom();
                }
            } catch (err) {
                console.error('Poll failed:', err);
            }
        }

        // Poll every 2 seconds
        setInterval(pollMessages, 2000);

        // Allow Enter to send (Shift+Enter for newline)
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });

        // Delete a single message
        async function deleteMessage(msgId, btnEl) {
            if (!confirm('Hapus pesan ini?')) return;
            try {
                const res = await fetch(`/negotiation/message/${msgId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });
                if (res.ok) {
                    const bubble = document.querySelector(`[data-msg-id="${msgId}"]`);
                    if (bubble) {
                        bubble.style.opacity = '0';
                        bubble.style.transform = 'scale(0.8)';
                        bubble.style.transition = 'all 0.3s ease';
                        setTimeout(() => bubble.remove(), 300);
                    }
                }
            } catch (err) { console.error(err); }
        }

        // Delete entire conversation
        async function deleteConversation() {
            if (!confirm('Hapus seluruh percakapan ini? Tindakan ini tidak bisa dibatalkan.')) return;
            try {
                const res = await fetch(`/negotiation/{{ $serviceRequest->id }}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });
                if (res.ok) {
                    window.location.href = "{{ route('user.chats') }}";
                }
            } catch (err) { console.error(err); }
        }
    </script>
</body>
</html>
