<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Location;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Message;
use App\Models\Transaction;
use App\Models\Report;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private User $seller;
    private User $admin;
    private Category $category;
    private Location $sellerLocation;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::create([
            'email' => 'admin_user_test_' . uniqid() . '@centrivo.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $this->admin->id,
            'name' => 'Admin Test',
        ]);

        // Create User
        $this->user = User::create([
            'email' => 'user_test_' . uniqid() . '@centrivo.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $this->user->id,
            'name' => 'User Buyer Test',
        ]);

        // Create Seller
        $this->seller = User::create([
            'email' => 'seller_test_' . uniqid() . '@centrivo.com',
            'password' => bcrypt('password123'),
            'role' => 'seller',
            'is_active' => true,
        ]);
        SellerProfile::create([
            'user_id' => $this->seller->id,
            'brand_name' => 'Brand Test',
            'balance' => 0,
            'verification_status' => 'verified',
        ]);

        $this->category = Category::updateOrCreate(['name' => 'Desain Grafis']);

        $this->sellerLocation = Location::create([
            'user_id' => $this->seller->id,
            'province' => 'Jawa Timur',
            'city' => 'Surabaya',
            'district' => 'Gubeng',
            'address' => 'Jl. Gubeng',
            'latitude' => -7.2,
            'longitude' => 112.7,
        ]);

        $this->service = Service::create([
            'seller_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'location_id' => $this->sellerLocation->id,
            'service_name' => 'Jasa Desain Logo',
            'description' => 'Desain logo profesional',
            'start_price' => 150000,
            'whatsapp' => '08123456789',
            'status' => 'active',
            'is_banned' => false,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('market'));
        $response->assertRedirect('/login');
    }

    public function test_seller_cannot_access_user_routes(): void
    {
        $response = $this->actingAs($this->seller)->get(route('market'));
        $response->assertStatus(403);
    }

    public function test_user_can_access_market_and_search(): void
    {
        // View Market
        $response = $this->actingAs($this->user)->get(route('market'));
        $response->assertStatus(200);

        // Search Market
        $response = $this->actingAs($this->user)->get(route('market.search', [
            'q' => 'Logo',
            'category' => $this->category->id,
        ]));
        $response->assertStatus(200);

        // Detail Market
        $response = $this->actingAs($this->user)->get(route('detail-market', $this->service));
        $response->assertStatus(200);
    }

    public function test_user_can_initiate_negotiation(): void
    {
        $response = $this->actingAs($this->user)->post(route('negotiation.initiate', $this->service), [
            'price_offer' => 100000,
            'message' => 'Halo, apakah bisa 100rb?',
        ]);

        $response->assertRedirect();
        
        $request = ServiceRequest::where('user_id', $this->user->id)->where('service_id', $this->service->id)->first();
        $this->assertNotNull($request);
        $this->assertEquals('negotiating', $request->status);
    }

    public function test_user_can_use_negotiation_chat(): void
    {
        $request = ServiceRequest::create([
            'user_id' => $this->user->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'status' => 'negotiating',
        ]);

        // Show negotiation view
        $response = $this->actingAs($this->user)->get(route('negotiation.show', $request));
        $response->assertStatus(200);

        // Send normal message
        $response = $this->actingAs($this->user)->post(route('negotiation.send', $request), [
            'message' => 'Baik, kita mulai kerjakan.',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'request_id' => $request->id,
            'message' => 'Baik, kita mulai kerjakan.',
        ]);

        // Fetch messages
        $response = $this->actingAs($this->user)->get(route('negotiation.fetch', $request));
        $response->assertStatus(200);
        $response->assertJsonStructure(['messages']);
        
        $message = Message::where('request_id', $request->id)->first();
        $response = $this->actingAs($this->user)->delete(route('negotiation.message.delete', $message));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('messages', ['id' => $message->id]);

        // Delete conversation
        $response = $this->actingAs($this->user)->delete(route('negotiation.destroy', $request));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('service_requests', ['id' => $request->id]);
    }

    public function test_user_can_checkout_and_pay(): void
    {
        $request = ServiceRequest::create([
            'user_id' => $this->user->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'status' => 'agreed',
        ]);

        $message = Message::create([
            'request_id' => $request->id,
            'sender_id' => $this->seller->id,
            'offered_price' => 120000,
        ]);

        // View Checkout
        $response = $this->actingAs($this->user)->get(route('checkout.show', $message));
        $response->assertStatus(200);

        // Process Checkout
        $response = $this->actingAs($this->user)->post(route('checkout.process', $message), [
            'service_type' => 'home_service'
        ]);
        $response->assertRedirect();

        $transaction = Transaction::where('request_id', $request->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('pending', $transaction->payment_status);
        $this->assertEquals(120000, $transaction->base_price);

        // View Payment page
        $response = $this->actingAs($this->user)->get(route('user.payment', $transaction));
        $response->assertStatus(200);
    }

    public function test_user_can_view_transactions_and_complete(): void
    {
        $request = ServiceRequest::create([
            'user_id' => $this->user->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'status' => 'agreed',
        ]);

        $transaction = Transaction::create([
            'request_id' => $request->id,
            'payment_status' => 'paid',
            'payment_method' => 'transfer',
            'transaction_status' => 'pending',
            'base_price' => 100000,
            'tax_amount' => 11000,
            'admin_fee' => 5000,
            'final_price' => 116000,
        ]);

        // View transactions
        $response = $this->actingAs($this->user)->get(route('user.transactions'));
        $response->assertStatus(200);

        // Complete transaction
        $response = $this->actingAs($this->user)->post(route('user.transactions.complete', $transaction), [
            'rating' => 5,
            'comment' => 'Bagus sekali',
        ]);
        $response->assertRedirect();
        
        $transaction->refresh();
        $this->assertEquals('completed', $transaction->transaction_status);
        
        $this->seller->sellerProfile->refresh();
        $this->assertEquals(100000, $this->seller->sellerProfile->balance); // Base price added to seller balance
    }

    public function test_user_can_manage_profile_and_settings(): void
    {
        Storage::fake('public');

        // Profile index
        $response = $this->actingAs($this->user)->get(route('user.profile'));
        $response->assertStatus(200);

        // Update profile
        $response = $this->actingAs($this->user)->post(route('user.profile.update'), [
            'name' => 'User Updated Name',
            'phone' => '08999999999',
            'profile_photo' => UploadedFile::fake()->image('avatar.jpg')
        ]);
        $response->assertRedirect();
        
        $this->user->userProfile->refresh();
        $this->assertEquals('User Updated Name', $this->user->userProfile->name);
        $this->assertNotNull($this->user->userProfile->profile_photo);

        // Settings index
        $response = $this->actingAs($this->user)->get(route('user.settings'));
        $response->assertStatus(200);

        // Update location
        $response = $this->actingAs($this->user)->post(route('user.profile.location.update'), [
            'latitude' => -8.0,
            'longitude' => 112.0,
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->user->userProfile->refresh();
        $this->assertEquals(-8.0, $this->user->userProfile->latitude);
        $this->assertEquals(112.0, $this->user->userProfile->longitude);
    }

    public function test_user_can_report_seller(): void
    {
        $request = ServiceRequest::create([
            'user_id' => $this->user->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'status' => 'agreed',
        ]);

        $transaction = Transaction::create([
            'request_id' => $request->id,
            'payment_status' => 'paid',
            'payment_method' => 'transfer',
            'transaction_status' => 'pending',
            'base_price' => 100000,
            'tax_amount' => 11000,
            'admin_fee' => 5000,
            'final_price' => 116000,
        ]);

        $response = $this->actingAs($this->user)->post(route('user.report.store'), [
            'reported_user_id' => $this->seller->id,
            'transaction_id' => $transaction->id,
            'reason' => 'Layanan tidak sesuai',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $this->user->id,
            'reported_user_id' => $this->seller->id,
            'reason' => 'Layanan tidak sesuai',
        ]);

        $response = $this->actingAs($this->user)->get(route('user.reports.index'));
        $response->assertStatus(200);
    }

    public function test_user_can_manage_chats(): void
    {
        $request = ServiceRequest::create([
            'user_id' => $this->user->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'status' => 'agreed',
        ]);

        Message::create([
            'request_id' => $request->id,
            'sender_id' => $this->user->id,
            'message' => 'Halo user!',
        ]);

        // Chat index
        $response = $this->actingAs($this->user)->get(route('user.chats'));
        $response->assertStatus(200);

        $message = Message::where('request_id', $request->id)->first();
        $response = $this->actingAs($this->user)->delete(route('user.chats.message.destroy', $message));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('messages', ['id' => $message->id]);

        // Delete chat conversation
        $response = $this->actingAs($this->user)->delete(route('user.chats.destroy', $request));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('service_requests', ['id' => $request->id]);
    }
}
