<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\SellerProfile;
use App\Models\Location;
use App\Models\Category;
use App\Models\Service;
use App\Models\Withdrawal;
use App\Models\ServiceRequest;
use App\Models\Transaction;
use App\Models\AdPackage;
use App\Models\AdvertisementTransaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerTest extends TestCase
{
    use DatabaseTransactions;

    private User $seller;
    private User $user;
    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::create([
            'email' => 'admin_seller_test_' . uniqid() . '@centrivo.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $this->admin->id,
            'name' => 'Admin Seller Test',
        ]);

        // Create Seller
        $this->seller = User::create([
            'email' => 'seller_seller_test_' . uniqid() . '@centrivo.com',
            'password' => bcrypt('password123'),
            'role' => 'seller',
            'is_active' => true,
        ]);
        SellerProfile::create([
            'user_id' => $this->seller->id,
            'brand_name' => 'Seller Test Brand',
            'balance' => 100000.00,
            'verification_status' => 'verified',
        ]);

        // Create User
        $this->user = User::create([
            'email' => 'user_seller_test_' . uniqid() . '@centrivo.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $this->user->id,
            'name' => 'User Seller Test',
        ]);

        $this->category = Category::updateOrCreate(['name' => 'Tutor Privat']);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('sellers.dashboard'));
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_seller_routes(): void
    {
        $response = $this->actingAs($this->user)->get(route('sellers.dashboard'));
        $response->assertStatus(403);
    }

    public function test_seller_can_access_seller_dashboard(): void
    {
        $response = $this->actingAs($this->seller)->get(route('sellers.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('sellers-dashboard.dashboard');
    }

    public function test_seller_can_view_wallet(): void
    {
        $response = $this->actingAs($this->seller)->get(route('seller.wallet'));
        $response->assertStatus(200);
        $response->assertViewIs('sellers-dashboard.wallet');
    }

    public function test_seller_can_request_withdrawal_success(): void
    {
        $response = $this->actingAs($this->seller)->post(route('seller.wallet.withdraw'), [
            'amount' => 50000,
            'bank_name' => 'BCA',
            'account_number' => '987654321',
            'account_name' => 'Seller Test Account',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->seller->sellerProfile->refresh();
        $this->assertEquals(50000.00, $this->seller->sellerProfile->balance);
        $this->assertDatabaseHas('withdrawals', [
            'seller_id' => $this->seller->id,
            'amount' => 50000.00,
            'status' => 'pending',
        ]);
    }

    public function test_seller_cannot_request_withdrawal_insufficient_funds(): void
    {
        $response = $this->actingAs($this->seller)->post(route('seller.wallet.withdraw'), [
            'amount' => 150000,
            'bank_name' => 'BCA',
            'account_number' => '987654321',
            'account_name' => 'Seller Test Account',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->seller->sellerProfile->refresh();
        $this->assertEquals(100000.00, $this->seller->sellerProfile->balance);
    }

    public function test_seller_cannot_request_withdrawal_with_pending_request(): void
    {
        Withdrawal::create([
            'seller_id' => $this->seller->id,
            'amount' => 10000,
            'bank_name' => 'BCA',
            'account_number' => '123',
            'account_name' => 'Account',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->seller)->post(route('seller.wallet.withdraw'), [
            'amount' => 10000,
            'bank_name' => 'BCA',
            'account_number' => '987654321',
            'account_name' => 'Seller Test Account',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_seller_can_view_services(): void
    {
        $response = $this->actingAs($this->seller)->get(route('services.index'));
        $response->assertStatus(200);
        $response->assertViewIs('sellers-dashboard.services');
    }

    public function test_unverified_seller_cannot_create_service(): void
    {
        $this->seller->sellerProfile->update(['verification_status' => 'unverified']);

        $response = $this->actingAs($this->seller)->post(route('services.store'), [
            'service_name' => 'Jasa Tutor PHP',
            'description' => 'Belajar PHP Pemula',
            'start_price' => 50000,
            'whatsapp' => '08123456789',
            'category_id' => $this->category->id,
            'location_id' => 1,
        ]);
        $response->assertRedirect(route('sellers.dashboard'));
        $response->assertSessionHas('kyc_warning');
    }

    public function test_verified_seller_can_crud_service(): void
    {
        Storage::fake('public');

        $location = Location::create([
            'user_id' => $this->seller->id,
            'province' => 'Jawa Timur',
            'city' => 'Jember',
            'district' => 'Sumbersari',
            'address' => 'Mastrip',
            'latitude' => -8.1,
            'longitude' => 113.7,
        ]);

        // 1. Create & Store
        $response = $this->actingAs($this->seller)->post(route('services.store'), [
            'service_name' => 'Jasa Tutor PHP',
            'description' => 'Belajar PHP Pemula',
            'start_price' => 50000,
            'whatsapp' => '08123456789',
            'category_id' => $this->category->id,
            'location_id' => $location->id,
            'images' => [UploadedFile::fake()->image('service1.jpg')]
        ]);
        $response->assertRedirect();

        $service = Service::where('service_name', 'Jasa Tutor PHP')->first();
        $this->assertNotNull($service);
        $this->assertEquals('Belajar PHP Pemula', $service->description);

        // 3. Update & Toggle Status
        $response = $this->actingAs($this->seller)->put(route('services.update', $service), [
            'service_name' => 'Jasa Tutor PHP Update',
            'description' => 'Belajar PHP Lanjutan',
            'start_price' => 75000,
            'whatsapp' => '08123456789',
            'category_id' => $this->category->id,
            'location_id' => $location->id,
        ]);
        $response->assertRedirect();
        $service->refresh();
        $this->assertEquals('Jasa Tutor PHP Update', $service->service_name);

        $response = $this->actingAs($this->seller)->patch(route('services.toggle', $service));
        $response->assertStatus(200);
        $service->refresh();
        $this->assertEquals('inactive', $service->status);

        // 4. Delete
        $response = $this->actingAs($this->seller)->delete(route('services.destroy', $service));
        $response->assertRedirect();
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_seller_can_crud_locations(): void
    {
        // 1. Create Location
        $response = $this->actingAs($this->seller)->post(route('locations.store'), [
            'province' => 'Jawa Timur',
            'city' => 'Jember',
            'district' => 'Sumbersari',
            'postal_code' => '68121',
            'address' => 'Mastrip Timur',
            'latitude' => -8.1,
            'longitude' => 113.7,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('locations', ['address' => 'Mastrip Timur']);

        $location = Location::where('address', 'Mastrip Timur')->first();

        // 2. Show index
        $response = $this->actingAs($this->seller)->get(route('locations.index'));
        $response->assertStatus(200);

        // 3. Update
        $response = $this->actingAs($this->seller)->put(route('locations.update', $location), [
            'province' => 'Jawa Timur Updated',
            'city' => 'Jember',
            'district' => 'Sumbersari',
            'postal_code' => '68121',
            'address' => 'Mastrip Barat',
            'latitude' => -8.2,
            'longitude' => 113.8,
        ]);
        $response->assertRedirect();
        $location->refresh();
        $this->assertEquals('Mastrip Barat', $location->address);

        // 4. Destroy
        $response = $this->actingAs($this->seller)->delete(route('locations.destroy', $location));
        $response->assertRedirect();
        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }

    public function test_seller_can_view_kyc_and_submit(): void
    {
        Storage::fake('public');

        $this->seller->sellerProfile->update(['verification_status' => 'unverified']);

        $response = $this->actingAs($this->seller)->get(route('seller.kyc.show'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->seller)->post(route('seller.kyc.submit'), [
            'nik' => '1234567890123456',
            'ktp' => UploadedFile::fake()->image('ktp.jpg'),
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
            'bank_name' => 'BCA',
            'bank_account_number' => '123456789',
            'bank_account_name' => 'Brand Seller Ktp Name',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        
        $this->seller->sellerProfile->refresh();
        $this->assertEquals('1234567890123456', $this->seller->sellerProfile->nik);
        $this->assertEquals('pending', $this->seller->sellerProfile->verification_status);
    }

    public function test_seller_can_view_ads_and_checkout(): void
    {
        $package = AdPackage::create([
            'name' => 'Ultra Ad',
            'price' => 50000,
            'duration_days' => 5,
            'description' => 'Desc',
            'is_active' => true,
        ]);

        $location = Location::create([
            'user_id' => $this->seller->id,
            'province' => 'Jawa Timur',
            'city' => 'Jember',
            'district' => 'Sumbersari',
            'address' => 'Mastrip',
            'latitude' => -8.1,
            'longitude' => 113.7,
        ]);

        $service = Service::create([
            'seller_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'location_id' => $location->id,
            'service_name' => 'Jasa Tutor PHP',
            'description' => 'PHP',
            'start_price' => 50000,
            'whatsapp' => '08123456789',
            'status' => 'active',
            'is_banned' => false,
        ]);

        $response = $this->actingAs($this->seller)->get(route('seller.advertisements'));
        $response->assertStatus(200);
        $response->assertViewIs('sellers-dashboard.advertisements');

        $response = $this->actingAs($this->seller)->post(route('seller.advertisements.checkout'), [
            'service_id' => $service->id,
            'ad_package_id' => $package->id,
        ]);
        $response->assertRedirect();

        $adTransaction = AdvertisementTransaction::whereHas('advertisement', function($q) use ($service) {
            $q->where('service_id', $service->id);
        })->first();
        $this->assertNotNull($adTransaction);

        $response = $this->actingAs($this->seller)->get(route('seller.advertisements.pay', $adTransaction));
        $response->assertStatus(200);
        $response->assertViewIs('sellers-dashboard.payment-ad');
    }

    public function test_seller_can_view_reports_and_export(): void
    {
        // 1. View Report Index
        $response = $this->actingAs($this->seller)->get(route('seller.reports.index'));
        $response->assertStatus(200);
        $response->assertViewIs('sellers-dashboard.reports');

        // 2. Export Excel (Mocked Report Controller to avoid exit;)
        $mock = \Mockery::mock(\App\Http\Controllers\Sellers\ReportController::class)->makePartial();
        $mock->shouldReceive('exportExcel')->once()->andReturn(response('Excel Report Data', 200));
        $this->app->instance(\App\Http\Controllers\Sellers\ReportController::class, $mock);

        $response = $this->actingAs($this->seller)->get(route('seller.reports.export'));
        $response->assertStatus(200);
        $response->assertSee('Excel Report Data');
    }

    public function test_seller_can_report_user(): void
    {
        $location = Location::create([
            'user_id' => $this->seller->id,
            'province' => 'Jawa Timur',
            'city' => 'Jember',
            'district' => 'Sumbersari',
            'address' => 'Mastrip',
            'latitude' => -8.1,
            'longitude' => 113.7,
        ]);

        $service = Service::create([
            'seller_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'location_id' => $location->id,
            'service_name' => 'Jasa Tutor PHP',
            'description' => 'PHP',
            'start_price' => 50000,
            'whatsapp' => '08123456789',
        ]);

        $request = ServiceRequest::create([
            'user_id' => $this->user->id,
            'seller_id' => $this->seller->id,
            'service_id' => $service->id,
            'status' => 'agreed',
        ]);

        $transaction = Transaction::create([
            'request_id' => $request->id,
            'payment_status' => 'paid',
            'payment_method' => 'transfer',
            'transaction_status' => 'pending',
            'base_price' => 50000,
            'tax_amount' => 5500,
            'admin_fee' => 2000,
            'final_price' => 57500,
        ]);

        $response = $this->actingAs($this->seller)->post(route('seller.report.user'), [
            'transaction_id' => $transaction->id,
            'reported_user_id' => $this->user->id,
            'reason' => 'Buyer was abusive',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $this->seller->id,
            'reported_user_id' => $this->user->id,
            'reason' => 'Buyer was abusive',
        ]);
    }
}
