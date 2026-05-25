<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\SellerProfile;
use App\Models\Withdrawal;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\ServiceRequest;
use App\Models\Service;
use App\Models\Category;
use App\Models\AdPackage;
use App\Models\Billboard;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $userUser;
    private User $sellerUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::create([
            'email' => 'admin_test_route@centrivo.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $this->admin->id,
            'name' => 'Admin Controller Test',
        ]);

        // Create Regular User
        $this->userUser = User::create([
            'email' => 'user_test_route@centrivo.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $this->userUser->id,
            'name' => 'User Controller Test',
        ]);

        // Create Seller
        $this->sellerUser = User::create([
            'email' => 'seller_test_route@centrivo.com',
            'password' => bcrypt('password123'),
            'role' => 'seller',
            'is_active' => true,
        ]);
        SellerProfile::create([
            'user_id' => $this->sellerUser->id,
            'brand_name' => 'Seller Controller Test',
        ]);
    }

    /**
     * Test guest is redirected to login when accessing admin dashboard.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect('/login');
    }

    /**
     * Test regular user gets 403 when accessing admin dashboard.
     */
    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->userUser)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    /**
     * Test seller gets 403 when accessing admin dashboard.
     */
    public function test_seller_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->sellerUser)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    /**
     * Test admin can access admin dashboard.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /**
     * Test admin can view withdrawals.
     */
    public function test_admin_can_view_withdrawals(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.withdrawals'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.withdrawals');
    }

    /**
     * Test admin can approve withdrawal.
     */
    public function test_admin_can_approve_withdrawal(): void
    {
        $withdrawal = Withdrawal::create([
            'seller_id' => $this->sellerUser->id,
            'amount' => 50000,
            'status' => 'pending',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Seller Test Account',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.withdrawals.approve', $withdrawal));
        $response->assertRedirect();
        
        $withdrawal->refresh();
        $this->assertEquals('approved', $withdrawal->status);
    }

    /**
     * Test admin can reject withdrawal.
     */
    public function test_admin_can_reject_withdrawal(): void
    {
        $withdrawal = Withdrawal::create([
            'seller_id' => $this->sellerUser->id,
            'amount' => 50000,
            'status' => 'pending',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Seller Test Account',
        ]);

        $profile = $this->sellerUser->sellerProfile;
        $profile->update(['balance' => 10000]);

        $response = $this->actingAs($this->admin)->post(route('admin.withdrawals.reject', $withdrawal));
        $response->assertRedirect();

        $withdrawal->refresh();
        $profile->refresh();

        $this->assertEquals('rejected', $withdrawal->status);
        $this->assertEquals(60000, $profile->balance);
    }

    /**
     * Test admin can view users-management.
     */
    public function test_admin_can_view_users_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('users.management'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.users-management');
    }

    /**
     * Test admin can fetch user reports via JSON.
     */
    public function test_admin_can_fetch_user_reports(): void
    {
        $response = $this->actingAs($this->admin)->get(route('users.reports', $this->userUser));
        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'reports']);
    }

    /**
     * Test admin can ban a user.
     */
    public function test_admin_can_ban_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('users.ban', $this->userUser), [
            'ban_reason' => 'Spamming',
        ]);
        $response->assertRedirect();

        $this->userUser->refresh();
        $this->assertTrue((bool)$this->userUser->is_banned);
        $this->assertEquals('Spamming', $this->userUser->ban_reason);
    }

    /**
     * Test admin can unban a user.
     */
    public function test_admin_can_unban_user(): void
    {
        $this->userUser->update([
            'is_banned' => true,
            'ban_reason' => 'Spamming',
        ]);

        $response = $this->actingAs($this->admin)->post(route('users.unban', $this->userUser));
        $response->assertRedirect();

        $this->userUser->refresh();
        $this->assertFalse((bool)$this->userUser->is_banned);
        $this->assertNull($this->userUser->ban_reason);
    }

    /**
     * Test admin can view report center.
     */
    public function test_admin_can_view_report_center(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.report-center.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.report-center');
    }

    /**
     * Test admin can view specific report detail.
     */
    public function test_admin_can_view_report_detail(): void
    {
        $report = Report::create([
            'reporter_id' => $this->userUser->id,
            'reported_user_id' => $this->sellerUser->id,
            'reason' => 'Penipuan',
            'status' => 'pending',
            'report_code' => Report::generateCode(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.report-center.show', $report));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.report-center-detail');
    }

    /**
     * Test admin can update report status.
     */
    public function test_admin_can_update_report_status(): void
    {
        $report = Report::create([
            'reporter_id' => $this->userUser->id,
            'reported_user_id' => $this->sellerUser->id,
            'reason' => 'Penipuan',
            'status' => 'pending',
            'report_code' => Report::generateCode(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.report-center.status', $report), [
            'status' => 'reviewed',
            'admin_notes' => 'Sedang diperiksa',
        ]);

        $response->assertRedirect();
        $report->refresh();
        $this->assertEquals('reviewed', $report->status);
        $this->assertEquals('Sedang diperiksa', $report->admin_notes);
    }

    /**
     * Test admin can mark report resolved.
     */
    public function test_admin_can_mark_report_resolved(): void
    {
        $report = Report::create([
            'reporter_id' => $this->userUser->id,
            'reported_user_id' => $this->sellerUser->id,
            'reason' => 'Penipuan',
            'status' => 'pending',
            'report_code' => Report::generateCode(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.report-center.resolve', $report));
        $response->assertRedirect();
        
        $report->refresh();
        $this->assertEquals('resolved', $report->status);
    }

    /**
     * Test admin can resolve disputed transaction (resume or cancel).
     */
    public function test_admin_can_resolve_disputed_transaction(): void
    {
        $category = Category::create(['name' => 'Tech']);
        
        $location = \App\Models\Location::create([
            'user_id' => $this->sellerUser->id,
            'province' => 'Jawa Timur',
            'city' => 'Jember',
            'district' => 'Sumbersari',
            'address' => 'Jl. Mastrip No. 10',
            'latitude' => -8.1724,
            'longitude' => 113.7005,
        ]);

        $service = Service::create([
            'seller_id' => $this->sellerUser->id,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'service_name' => 'Web Dev',
            'description' => 'Description',
            'start_price' => 100000,
            'whatsapp' => '081234',
        ]);

        $request = ServiceRequest::create([
            'user_id' => $this->userUser->id,
            'seller_id' => $this->sellerUser->id,
            'service_id' => $service->id,
            'status' => 'agreed',
        ]);

        $transaction = Transaction::create([
            'request_id' => $request->id,
            'payment_status' => 'paid',
            'payment_method' => 'transfer',
            'transaction_status' => 'pending',
            'is_disputed' => true,
            'disputed_at' => now(),
            'disputed_by' => 'user_ban',
            'base_price' => 100000,
            'tax_amount' => 11000,
            'admin_fee' => 5000,
            'final_price' => 116000,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.disputed-transactions.resolve', $transaction), [
            'action' => 'resume',
        ]);

        $response->assertRedirect();
        $transaction->refresh();
        $this->assertFalse((bool)$transaction->is_disputed);
    }

    /**
     * Test admin can view services categories page.
     */
    public function test_admin_can_view_services_categories(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.services.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.servicencategories');
    }

    /**
     * Test admin can create, update, and delete category.
     */
    public function test_admin_crud_category(): void
    {
        // 1. Store
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Otomotif Unik',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Otomotif Unik']);

        $category = Category::where('name', 'Otomotif Unik')->first();

        // 2. Update
        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name' => 'Otomotif Klasik',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Otomotif Klasik']);

        // 3. Destroy
        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category));
        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /**
     * Test admin can view service transactions.
     */
    public function test_admin_can_view_service_transactions(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.service.transactions'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.service-transactions');
    }

    /**
     * Test admin can view advertisements packages.
     */
    public function test_admin_can_view_advertisements(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ads.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.advertisements');
    }

    /**
     * Test admin advertisement package management.
     */
    public function test_admin_advertisement_package_management(): void
    {
        // 1. Store Package
        $response = $this->actingAs($this->admin)->post(route('admin.ads.store'), [
            'name' => 'Premium Ad',
            'price' => 150000,
            'duration_days' => 7,
            'description' => 'Paket Iklan Premium',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('ad_packages', ['name' => 'Premium Ad']);

        $package = AdPackage::where('name', 'Premium Ad')->first();

        // 2. Toggle Status
        $response = $this->actingAs($this->admin)->post(route('admin.ads.toggle', $package));
        $response->assertRedirect();
        $package->refresh();
        $this->assertFalse((bool)$package->is_active);

        // 3. Destroy Package
        $response = $this->actingAs($this->admin)->delete(route('admin.ads.destroy', $package));
        $response->assertRedirect();
        $this->assertDatabaseMissing('ad_packages', ['id' => $package->id]);
    }

    public function test_admin_can_export_excel_report(): void
    {
        // To prevent PhpSpreadsheet saving to standard output and calling exit; which aborts PHPUnit,
        // we mock the controller action using a partial mock that allows route middleware fetching!
        $mock = \Mockery::mock(\App\Http\Controllers\Admin\ReportController::class)->makePartial();
        $mock->shouldReceive('exportExcel')->once()->andReturn(response('Excel Binary Data', 200));
        $this->app->instance(\App\Http\Controllers\Admin\ReportController::class, $mock);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.export'));
        $response->assertStatus(200);
        $response->assertSee('Excel Binary Data');
    }

    /**
     * Test admin can view settings index.
     */
    public function test_admin_can_view_settings(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.settings');
    }

    /**
     * Test admin can update platform settings.
     */
    public function test_admin_can_update_settings(): void
    {
        Setting::updateOrCreate(['key' => 'platform_name'], ['value' => 'Centrivo Old']);

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'settings' => [
                'platform_name' => 'Centrivo New',
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'key' => 'platform_name',
            'value' => 'Centrivo New',
        ]);
    }

    /**
     * Test admin billboard management.
     */
    public function test_admin_billboard_management(): void
    {
        Storage::fake('public');

        // 1. Store
        $response = $this->actingAs($this->admin)->post(route('admin.billboards.store'), [
            'title' => 'Big Sale',
            'subtitle' => 'Diskon Jasa',
            'gradient_from' => '#ff0000',
            'gradient_to' => '#0000ff',
            'image' => UploadedFile::fake()->image('sale.png'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('billboards', ['title' => 'Big Sale']);

        $billboard = Billboard::where('title', 'Big Sale')->first();
        $this->assertNotNull($billboard->image_path);
        Storage::disk('public')->assertExists($billboard->image_path);

        // 2. Toggle Status
        $response = $this->actingAs($this->admin)->post(route('admin.billboards.toggle', $billboard));
        $response->assertRedirect();
        $billboard->refresh();
        $this->assertFalse((bool)$billboard->is_active);

        // 3. Update
        $response = $this->actingAs($this->admin)->put(route('admin.billboards.update', $billboard), [
            'title' => 'Mega Sale',
            'gradient_from' => '#00ff00',
            'gradient_to' => '#0000ff',
        ]);
        $response->assertRedirect();
        $billboard->refresh();
        $this->assertEquals('Mega Sale', $billboard->title);

        // 4. Destroy
        $path = $billboard->image_path;
        $response = $this->actingAs($this->admin)->delete(route('admin.billboards.destroy', $billboard));
        $response->assertRedirect();
        $this->assertDatabaseMissing('billboards', ['id' => $billboard->id]);
        Storage::disk('public')->assertMissing($path);
    }

    /**
     * Test admin can view seller verifications.
     */
    public function test_admin_can_view_seller_verifications(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.seller-verifications.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.seller-verifications');
    }

    /**
     * Test admin can approve seller verification.
     */
    public function test_admin_can_approve_seller_verification(): void
    {
        $profile = $this->sellerUser->sellerProfile;
        $profile->update(['verification_status' => 'pending']);

        $response = $this->actingAs($this->admin)->post(route('admin.seller-verifications.approve', $profile));
        $response->assertRedirect();
        
        $profile->refresh();
        $this->assertEquals('verified', $profile->verification_status);
        $this->assertNotNull($profile->verified_at);
    }

    /**
     * Test admin can reject seller verification.
     */
    public function test_admin_can_reject_seller_verification(): void
    {
        $profile = $this->sellerUser->sellerProfile;
        $profile->update(['verification_status' => 'pending']);

        $response = $this->actingAs($this->admin)->post(route('admin.seller-verifications.reject', $profile), [
            'rejection_reason' => 'Identitas tidak jelas',
        ]);
        $response->assertRedirect();
        
        $profile->refresh();
        $this->assertEquals('rejected', $profile->verification_status);
        $this->assertEquals('Identitas tidak jelas', $profile->rejection_reason);
    }
}
