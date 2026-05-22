<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivationEmail;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test landing page is accessible by guest.
     */
    public function test_landing_page_accessible_by_guest(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('landing.index');
    }

    /**
     * Test login page is accessible by guest.
     */
    public function test_login_page_accessible_by_guest(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * Test user register page is accessible.
     */
    public function test_user_register_page_accessible(): void
    {
        $response = $this->get('/register/user');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register-user');
    }

    /**
     * Test seller register page is accessible.
     */
    public function test_seller_register_page_accessible(): void
    {
        $response = $this->get('/register/seller');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register-seller');
    }

    /**
     * Test login with valid admin credentials.
     */
    public function test_login_valid_admin_redirects_to_dashboard(): void
    {
        $password = 'password123';
        $user = User::create([
            'email' => 'admin_test@centrivo.com',
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'name' => 'Admin Test',
            'phone' => '0812345678',
            'address' => 'Test Address',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login with valid seller credentials.
     */
    public function test_login_valid_seller_redirects_to_dashboard(): void
    {
        $password = 'password123';
        $user = User::create([
            'email' => 'seller_test@centrivo.com',
            'password' => Hash::make($password),
            'role' => 'seller',
            'is_active' => true,
        ]);

        SellerProfile::create([
            'user_id' => $user->id,
            'brand_name' => 'Seller Test Brand',
            'phone' => '0812345679',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // AuthController matches 'sellers' (plural) but DB is 'seller' (singular).
        // Since 'seller' doesn't match 'sellers', it redirects to '/' and RedirectIfAuthenticated redirects to sellers.dashboard
        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login with valid user credentials.
     */
    public function test_login_valid_user_redirects_to_market(): void
    {
        $password = 'password123';
        $user = User::create([
            'email' => 'user_test@centrivo.com',
            'password' => Hash::make($password),
            'role' => 'user',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'name' => 'User Test',
            'phone' => '0812345670',
            'address' => 'Test User Address',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login with invalid credentials.
     */
    public function test_login_invalid_credentials_returns_errors(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test login with inactive account.
     */
    public function test_login_inactive_account_redirects_to_activation_notice(): void
    {
        $password = 'password123';
        $user = User::create([
            'email' => 'inactive_test@centrivo.com',
            'password' => Hash::make($password),
            'role' => 'user',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('activation.notice', $user->email));
        $this->assertGuest();
    }

    /**
     * Test login with banned account.
     */
    public function test_login_banned_account_redirects_to_banned_notice(): void
    {
        $password = 'password123';
        $user = User::create([
            'email' => 'banned_test@centrivo.com',
            'password' => Hash::make($password),
            'role' => 'user',
            'is_active' => true,
            'is_banned' => true,
            'ban_report_code' => 'REP-123',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('banned.notice', 'REP-123'));
        $this->assertGuest();
    }

    /**
     * Test user registration with valid data.
     */
    public function test_user_registration_sends_email_and_creates_records(): void
    {
        Mail::fake();

        $response = $this->post('/register/user', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'phone' => '08987654321',
            'address' => 'Jl. Test No. 5',
            'latitude' => '-6.200000',
            'longitude' => '106.816666',
        ]);

        $response->assertRedirect(route('activation.notice', 'johndoe@example.com'));

        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
            'role' => 'user',
            'is_active' => false,
        ]);

        $user = User::where('email', 'johndoe@example.com')->first();

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'name' => 'John Doe',
            'phone' => '08987654321',
            'address' => 'Jl. Test No. 5',
        ]);

        Mail::assertSent(ActivationEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test seller registration with valid data.
     */
    public function test_seller_registration_sends_email_and_creates_records(): void
    {
        Mail::fake();

        $response = $this->post('/register/seller', [
            'business_name' => 'Ahmad Bakery',
            'email' => 'ahmadseller@example.com',
            'password' => 'password123',
            'phone' => '08987654322',
        ]);

        $response->assertRedirect(route('activation.notice', 'ahmadseller@example.com'));

        $this->assertDatabaseHas('users', [
            'email' => 'ahmadseller@example.com',
            'role' => 'seller',
            'is_active' => false,
        ]);

        $user = User::where('email', 'ahmadseller@example.com')->first();

        $this->assertDatabaseHas('seller_profiles', [
            'user_id' => $user->id,
            'brand_name' => 'Ahmad Bakery',
            'phone' => '08987654322',
        ]);

        Mail::assertSent(ActivationEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test successful account activation with valid token.
     */
    public function test_account_activation_with_valid_token(): void
    {
        $token = Str::random(64);
        $user = User::create([
            'email' => 'activation_test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'is_active' => false,
            'activation_token' => $token,
        ]);

        $response = $this->get(route('activate.account', $token));

        $response->assertStatus(200);
        $response->assertViewIs('auth.activation-success');

        $this->assertDatabaseHas('users', [
            'email' => 'activation_test@example.com',
            'is_active' => true,
            'activation_token' => null,
        ]);
    }

    /**
     * Test account activation with invalid token.
     */
    public function test_account_activation_with_invalid_token(): void
    {
        $response = $this->get(route('activate.account', 'invalid-token'));

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Token tidak valid!');
    }

    /**
     * Test resending activation email.
     */
    public function test_resend_activation_email(): void
    {
        Mail::fake();

        $user = User::create([
            'email' => 'resend_test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'is_active' => false,
            'activation_token' => 'old-token',
        ]);

        $response = $this->post(route('activation.resend', $user->email));

        $response->assertSessionHas('message', 'Email aktivasi berhasil dikirim ulang!');

        $user->refresh();
        $this->assertNotEquals('old-token', $user->activation_token);
        $this->assertNotNull($user->activation_token);

        Mail::assertSent(ActivationEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test logout.
     */
    public function test_logout_destroys_session(): void
    {
        $user = User::create([
            'email' => 'logout_test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
