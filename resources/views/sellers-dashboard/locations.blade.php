@extends('sellers-dashboard.main')

@section('sellers_content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="p-8 bg-slate-50 min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h3 class="text-3xl font-black text-slate-800">My Locations</h3>
        <button onclick="openModal('create')" class="bg-color1 text-white px-8 py-4 rounded-2xl font-bold">+ Add Location</button>
    </div>

    <div class="mb-6 bg-white p-2 rounded-2xl shadow-sm border border-gray-100 max-w-md">
        <form action="{{ route('locations.index') }}" method="GET" class="flex items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by city, address..." class="w-full px-4 py-2 outline-none bg-transparent">
            <button type="submit" class="p-2 bg-color1 text-white rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </button>
        </form>
    </div>

    <div class="bg-white rounded-[40px] border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50">
                <tr>
                    <th class="px-8 py-5 text-slate-500 font-bold">CITY / DISTRICT</th>
                    <th class="px-8 py-5 text-slate-500 font-bold">ADDRESS</th>
                    <th class="px-8 py-5 text-slate-500 font-bold text-center">ACTION</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($locations as $location)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-8 py-6 font-bold text-slate-800">
                        {{ $location->city }}, {{ $location->district }}
                        <div class="text-xs text-slate-400 font-normal">{{ $location->province }} - {{ $location->postal_code }}</div>
                    </td>
                    <td class="px-8 py-6 text-slate-600 text-sm">{{ $location->address }}</td>
                    <td class="px-8 py-6 text-center">
                        <div class="flex justify-center gap-2">
                            <button type="button" onclick='openModal("edit", @json($location))' class="p-2 text-color1 hover:bg-orange-50 rounded-xl">✏️</button>
                            <form action="{{ route('locations.destroy', $location->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-xl">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-8 py-6 text-center text-gray-500">No locations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="locModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[9999] flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-[40px] p-8 shadow-2xl">
        <h3 id="modalTitle" class="text-2xl font-black mb-6 text-center">Location Form</h3>
        <form id="locForm" action="{{ route('locations.store') }}" method="POST" class="space-y-4">
            @csrf
            <div id="methodContainer"></div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <input type="text" name="province" id="province" value="{{ old('province') }}" required placeholder="Province" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none outline-none">
                    @error('province')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <input type="text" name="city" id="city" value="{{ old('city') }}" required placeholder="City" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none outline-none">
                    @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <input type="text" name="district" id="district" value="{{ old('district') }}" required placeholder="District" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none outline-none">
                    @error('district')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" placeholder="Postal Code" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none outline-none">
                    @error('postal_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <textarea name="address" id="address" required placeholder="Detail Address" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none outline-none">{{ old('address') }}</textarea>
                @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div id="mapLoc" style="height: 200px;" class="rounded-2xl border-2 border-gray-100"></div>
            
            <input type="hidden" name="latitude" id="lat">
            <input type="hidden" name="longitude" id="lng">

            <div class="flex gap-4">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 font-bold text-slate-400">Cancel</button>
                <button type="submit" id="btnSubmit" class="flex-1 bg-color1 text-white py-3 rounded-2xl font-bold">Save Location</button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            openModal('create');
        });
    @endif
    let map, marker;
    function openModal(type, data = null) {
        const form = document.getElementById('locForm');
        const methodContainer = document.getElementById('methodContainer');
        const modalTitle = document.getElementById('modalTitle');
        const btnSubmit = document.getElementById('btnSubmit');

        document.getElementById('locModal').classList.remove('hidden');

        let initialLat = -8.1724;
        let initialLng = 113.7005;

        if (type === 'edit' && data) {
            modalTitle.innerText = 'Edit Location';
            btnSubmit.innerText = 'Update Location';
            form.action = `/locations/${data.id}`;
            methodContainer.innerHTML = '@method("PUT")';

            document.getElementById('province').value = data.province;
            document.getElementById('city').value = data.city;
            document.getElementById('district').value = data.district;
            document.getElementById('postal_code').value = data.postal_code || '';
            document.getElementById('address').value = data.address;
            document.getElementById('lat').value = data.latitude;
            document.getElementById('lng').value = data.longitude;

            initialLat = data.latitude;
            initialLng = data.longitude;
        } else {
            modalTitle.innerText = 'Add Location';
            btnSubmit.innerText = 'Save Location';
            form.action = `{{ route('locations.store') }}`;
            methodContainer.innerHTML = '';
            form.reset();
        }

        setTimeout(() => {
            if (!map) {
                map = L.map('mapLoc').setView([initialLat, initialLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                marker = L.marker([initialLat, initialLng], {draggable: true}).addTo(map);
                marker.on('dragend', (e) => {
                    document.getElementById('lat').value = e.target.getLatLng().lat;
                    document.getElementById('lng').value = e.target.getLatLng().lng;
                });
            } else {
                map.setView([initialLat, initialLng], 13);
                marker.setLatLng([initialLat, initialLng]);
            }
            map.invalidateSize();
        }, 300);
    }
    function closeModal() { document.getElementById('locModal').classList.add('hidden'); }
</script>
@endsection