<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Centrivo</title>
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
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('market') }}" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                    <span class="text-xl">←</span>
                </a>
                <h1 class="text-2xl font-black text-slate-800 tracking-tighter">Profil Saya</h1>
            </div>
            <a href="{{ route('user.settings') }}" class="text-sm font-bold text-color1 hover:underline">Pengaturan</a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 py-10">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-600 rounded-2xl font-bold text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
            <div class="h-32 bg-gradient-to-r from-color1 to-color2"></div>
            <div class="px-8 pb-8">
                <div class="relative -mt-16 mb-6">
                    <div class="w-32 h-32 rounded-[32px] bg-white p-1 shadow-xl relative group">
                        @if($profile->profile_photo)
                            <img src="{{ asset('storage/' . $profile->profile_photo) }}" class="w-full h-full object-cover rounded-[28px]" alt="Profile Photo">
                        @else
                            <div class="w-full h-full bg-slate-100 rounded-[28px] flex items-center justify-center text-4xl font-black text-color1">
                                {{ substr($profile->name ?? $user->email, 0, 1) }}
                            </div>
                        @endif
                        <label for="profile_photo" class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-[28px] opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <span class="text-white text-xs font-bold">Ubah Foto</span>
                        </label>
                    </div>
                </div>

                <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="file" name="profile_photo" id="profile_photo" class="hidden" onchange="this.form.submit()">

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $profile->name) }}" class="w-full px-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-color1/20 focus:border-color1 transition-all font-bold text-slate-700">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Nomor Telepon / WA</label>
                            <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" class="w-full px-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-color1/20 focus:border-color1 transition-all font-bold text-slate-700">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Alamat Domisili</label>
                            <textarea name="address" rows="3" class="w-full px-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-color1/20 focus:border-color1 transition-all font-bold text-slate-700 resize-none">{{ old('address', $profile->address) }}</textarea>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full md:w-auto px-10 py-4 bg-color1 hover:bg-color2 text-white font-bold rounded-2xl shadow-lg shadow-color1/20 transition-all transform hover:-translate-y-1">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-[32px] p-8 shadow-sm border border-gray-100">
            <h2 class="text-lg font-black mb-6">Informasi Akun</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-4 border-b border-gray-50">
                    <span class="text-sm font-medium text-slate-500">Email</span>
                    <span class="text-sm font-bold text-slate-800">{{ $user->email }}</span>
                </div>
                <div class="flex items-center justify-between py-4 border-b border-gray-50">
                    <span class="text-sm font-medium text-slate-500">Role</span>
                    <span class="text-sm font-bold text-color1 uppercase tracking-widest text-xs">{{ $user->role }}</span>
                </div>
                <div class="flex items-center justify-between py-4">
                    <span class="text-sm font-medium text-slate-500">Terdaftar Sejak</span>
                    <span class="text-sm font-bold text-slate-800">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
