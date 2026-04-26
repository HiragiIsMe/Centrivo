<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Unauthorized</title>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen font-sans">
    
    <div class="text-center p-8 bg-white rounded-[40px] shadow-xl border border-gray-100 max-w-md w-full mx-4">
        <div class="text-6xl mb-6">🚫</div>
        <h1 class="text-4xl font-black text-slate-800 mb-2">Access Denied</h1>
        <p class="text-slate-500 mb-8 font-medium">
            You don't have permission to access this page. Please return to your authorized dashboard.
        </p>
        
        <div class="flex flex-col gap-3">
            <a href="javascript:history.back()" 
            class="bg-color1 text-white py-4 rounded-2xl font-bold hover:opacity-90 transition-all">
                Go Back
            </a>

            @auth
                @php
                    $homeRoute = match(auth()->user()->role) {
                        'admin'   => route('admin.dashboard'),
                        'sellers' => route('sellers.dashboard'),
                        'users'   => route('market'),
                        default   => '/',
                    };
                @endphp
                <a href="{{ $homeRoute }}" class="text-slate-400 font-bold hover:text-slate-600 transition-colors">
                    Back to Home
                </a>
            @else
                <a href="/" class="text-slate-400 font-bold hover:text-slate-600 transition-colors">
                    Back to Home
                </a>
            @endauth
        </div>
    </div>

</body>
</html>