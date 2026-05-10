@extends('sellers-dashboard.main')

@section('sellers_content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .swiper { width: 100%; height: 250px; border-radius: 1.5rem; background: #eee; }
    .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }
    .existing-image-box { position: relative; width: 100px; height: 100px; border-radius: 0.5rem; overflow: hidden; }
    .existing-image-box img { width: 100%; height: 100%; object-fit: cover; }
    .existing-image-box button { position: absolute; top: 4px; right: 4px; background: rgba(255,0,0,0.8); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;}
    
    .select2-container--default .select2-selection--single {
        height: auto;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 1rem;
        background-color: #f9fafb;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 50%;
        transform: translateY(-50%);
        right: 1.5rem;
    }
</style>

<div class="p-8 bg-slate-50 min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h3 class="text-3xl font-black text-slate-800">My Services</h3>
        <div class="flex gap-4 w-full md:w-auto">
            <form action="{{ route('services.index') }}" method="GET" class="flex-1 md:w-64 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search service..." class="w-full pl-10 pr-4 py-4 rounded-2xl border-none bg-white shadow-sm outline-none focus:ring-2 focus:ring-color1">
                <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
            @if(Auth::user()->sellerProfile?->canCreateService())
                <button onclick="openModal('create')" class="bg-color1 text-white px-8 py-4 rounded-2xl font-bold whitespace-nowrap">+ Add New Service</button>
            @else
                <a href="{{ route('seller.kyc.show') }}" class="bg-slate-200 text-slate-500 px-8 py-4 rounded-2xl font-bold whitespace-nowrap flex items-center gap-2">
                    🔒 Verifikasi Dulu
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        @foreach($services as $service)
        <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm relative overflow-hidden">
            <div class="h-40 bg-gray-100 rounded-2xl mb-4 overflow-hidden relative">
                @if($service->images->first())
                    <img src="{{ asset('storage/'.$service->images->first()->image_path) }}" 
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 font-bold">NO IMAGE</div>
                @endif
            </div>
            <div class="mb-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-lg font-black text-slate-800">{{ $service->service_name }}</h3>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $service->status == 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} uppercase">{{ $service->status }}</span>
                </div>
                <p class="text-gray-500 text-sm mb-3 line-clamp-3 break-words">{{ Str::limit($service->description, 100) }}</p>
                <p class="text-color1 font-bold text-lg">Rp.{{ number_format($service->start_price, 0, ',', '.') }}</p>
            </div>
            
            <div class="flex gap-2 pt-4 border-t border-gray-50">
                <button onclick="openModal('show', {{ $service->id }})" class="flex-1 py-2 bg-gray-50 rounded-xl font-bold text-sm text-slate-600">View</button>
                @if(Auth::user()->sellerProfile?->canCreateService())
                    <button onclick="openModal('edit', {{ $service->id }})" class="flex-1 py-2 bg-blue-50 rounded-xl font-bold text-sm text-blue-600">Edit</button>
                    <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="flex-1 flex" onsubmit="return confirm('Are you sure you want to delete this service?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2 bg-red-50 rounded-xl font-bold text-sm text-red-600">Delete</button>
                    </form>
                @else
                    <a href="{{ route('seller.kyc.show') }}" class="flex-1 py-2 bg-slate-100 rounded-xl font-bold text-sm text-slate-400 text-center">🔒 Edit</a>
                    <div class="flex-1 py-2 bg-slate-100 rounded-xl font-bold text-sm text-slate-400 text-center">🔒 Delete</div>
                @endif
            </div>
            
            <div class="mt-4 flex items-center justify-between bg-slate-50 p-3 rounded-2xl">
                <span class="text-xs font-bold text-slate-500 uppercase">Status: {{ $service->status }}</span>
                @if(Auth::user()->sellerProfile?->canCreateService())
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" onchange="toggleStatus({{ $service->id }})" 
                            {{ $service->status == 'active' ? 'checked' : '' }} 
                            class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-color1"></div>
                    </label>
                @else
                    <span class="text-xs text-slate-400 font-bold">🔒 Terkunci</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<div id="serviceModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[9999] flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-3xl rounded-[40px] p-8 md:p-12 shadow-2xl overflow-y-auto max-h-[95vh]">
        <h3 class="text-3xl font-black text-slate-800 mb-8 text-center">Manage Service</h3>
        
        <form id="serviceForm" action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Service Name</label>
                    <input type="text" name="service_name" value="{{ old('service_name') }}" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-color1">
                    @error('service_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Category</label>
                    <select name="category_id" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Location</label>
                    <select name="location_id" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none select2-location" style="width: 100%;">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->postal_code }} - {{ $loc->province }}, {{ $loc->city }}, {{ $loc->district }}</option>
                        @endforeach
                    </select>
                    @error('location_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">WhatsApp Number</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="08123456789" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none">
                    @error('whatsapp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Start Price (Rp)</label>
                    <input type="number" name="start_price" value="{{ old('start_price') }}" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none">
                    @error('start_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Service Images</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full px-6 py-4 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl">
                    <p class="text-xs text-gray-400 mt-1">
                        Maksimal 5 gambar (jpg/png, max 2MB per gambar)
                    </p>
                    @error('images')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('images.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700">Description</label>
                <textarea name="description" rows="3" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div id="imagePreviewContainer" class="hidden">
                <label class="text-sm font-bold text-slate-700">Existing Images</label>
                <div id="existingImages" class="flex flex-wrap gap-4 mt-2"></div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 py-4 font-bold text-slate-400">Cancel</button>
                <button type="submit" class="flex-1 bg-color1 text-white py-4 rounded-3xl font-bold hover:shadow-lg transition-all">Save Service</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-location').select2({
            dropdownParent: $('#serviceModal'),
            placeholder: "Select Location"
        });
    });

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            openModal('create');
        });
    @endif

    const rawServices = @json($services);
    const servicesData = rawServices.data || rawServices;

    document.querySelector('input[name="images[]"]').addEventListener('change', function(e) {
        if (this.files.length > 5) {
            alert('Maksimal upload 5 gambar!');
            this.value = '';
        }
    });

    function openModal(type, serviceId = null) {
        document.getElementById('serviceModal').classList.remove('hidden');
        const form = document.getElementById('serviceForm');
        const methodInput = document.getElementById('formMethod');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const existingImagesDiv = document.getElementById('existingImages');
        const modalTitle = document.querySelector('#serviceModal h3');
        
        form.reset();
        existingImagesDiv.innerHTML = '';
        imagePreviewContainer.classList.add('hidden');

        if (type === 'create') {
            modalTitle.textContent = 'Add New Service';
            form.action = "{{ route('services.store') }}";
            methodInput.value = "POST";
            document.querySelectorAll('#serviceForm input:not([type="hidden"]), #serviceForm select, #serviceForm textarea').forEach(el => el.disabled = false);
            document.querySelector('#serviceForm button[type="submit"]').classList.remove('hidden');
        } else if (type === 'edit' || type === 'show') {
            modalTitle.textContent = type === 'edit' ? 'Edit Service' : 'Service Details';
            
            const service = servicesData.find(s => s.id === serviceId);
            if(service) {
                form.action = `/services/${service.id}`;
                methodInput.value = "PUT";
                
                form.querySelector('input[name="service_name"]').value = service.service_name;
                form.querySelector('select[name="category_id"]').value = service.category_id;
                form.querySelector('select[name="location_id"]').value = service.location_id;
                form.querySelector('input[name="whatsapp"]').value = service.whatsapp;
                form.querySelector('input[name="start_price"]').value = service.start_price;
                form.querySelector('textarea[name="description"]').value = service.description;

                if (service.images && service.images.length > 0) {
                    imagePreviewContainer.classList.remove('hidden');
                    service.images.forEach(img => {
                        const imgBox = document.createElement('div');
                        imgBox.className = 'existing-image-box';
                        imgBox.innerHTML = `
                            <img src="/storage/${img.image_path}" alt="Service Image">
                            ${type === 'edit' ? `<button type="button" onclick="deleteImage(${img.id}, this)">X</button>` : ''}
                        `;
                        existingImagesDiv.appendChild(imgBox);
                    });
                }

                const isShow = type === 'show';
                document.querySelectorAll('#serviceForm input:not([type="hidden"]), #serviceForm select, #serviceForm textarea').forEach(el => el.disabled = isShow);
                
                const submitBtn = document.querySelector('#serviceForm button[type="submit"]');
                if(isShow) {
                    submitBtn.classList.add('hidden');
                } else {
                    submitBtn.classList.remove('hidden');
                }
            }
        }
    }

    function closeModal() {
        document.getElementById('serviceModal').classList.add('hidden');
    }

    function toggleStatus(id) {
        fetch(`/services/${id}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            // update badge status
            const card = [...document.querySelectorAll('[onclick*="'+id+'"]')]
                .map(btn => btn.closest('.bg-white'))
                .find(el => el);

            if (!card) return;

            const badge = card.querySelector('span.px-3');
            const statusText = card.querySelector('.text-xs.font-bold');

            if (data.status === 'active') {
                badge.textContent = 'active';
                badge.classList.remove('bg-red-100','text-red-600');
                badge.classList.add('bg-green-100','text-green-600');

                statusText.textContent = 'Status: active';
            } else {
                badge.textContent = 'inactive';
                badge.classList.remove('bg-green-100','text-green-600');
                badge.classList.add('bg-red-100','text-red-600');

                statusText.textContent = 'Status: inactive';
            }
        });
    }

    function deleteImage(imageId, btnElement) {
        if(!confirm('Are you sure you want to delete this image?')) return;
        
        fetch(`/service-images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                btnElement.closest('.existing-image-box').remove();
                const service = servicesData.find(s => s.images.some(img => img.id === imageId));
                if (service) {
                    service.images = service.images.filter(img => img.id !== imageId);
                }
            } else {
                alert('Failed to delete image.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred.');
        });
    }
</script>
@endsection