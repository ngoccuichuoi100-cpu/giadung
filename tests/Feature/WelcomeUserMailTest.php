<?php

namespace Tests\Feature;

use App\Mail\WelcomeNewUserMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WelcomeUserMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_mail_content_renders_correctly()
    {
        $user = new User([
            'username' => 'testuser99',
            'name' => 'Nguyễn Văn Test',
            'email' => 'testuser99@gmail.com',
            'phone' => '0987654321',
            'address' => 'Hà Nội',
            'points' => 50,
            'referral_code' => 'GD999999',
        ]);

        $mailable = new WelcomeNewUserMail($user);

        $mailable->assertHasSubject('GiaDụngPro - Khởi Nguồn Tiện Nghi, Vẹn Tròn Đẳng Cấp (Chào mừng thành viên mới)');
        $mailable->assertSeeInHtml('Nguyễn Văn Test');
        $mailable->assertSeeInHtml('testuser99@gmail.com');
        $mailable->assertSeeInHtml('CHÀO MỪNG BẠN TRỞ THÀNH THÀNH VIÊN GIADUNGPRO');
        $mailable->assertSeeInHtml('Đã kích hoạt sẵn sàng');
    }

    public function test_registration_sends_welcome_email()
    {
        Mail::fake();

        // Step 1: Register account
        $step1Response = $this->postJson('/register', [
            'username' => 'customernew' . time(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'policy' => true,
        ]);

        $step1Response->assertStatus(200);

        // Step 2: Complete profile
        $customerEmail = 'newcustomer_' . time() . '@gmail.com';
        $step2Response = $this->postJson('/register/profile', [
            'name' => 'Khách Hàng Mới',
            'age' => 25,
            'gender' => 'Nam',
            'phone' => '0912345678',
            'email' => $customerEmail,
            'address' => '123 Đường Cầu Giấy, Hà Nội',
        ]);

        $step2Response->assertStatus(200);

        // Verify email was sent to customer's email
        Mail::assertSent(WelcomeNewUserMail::class, function ($mail) use ($customerEmail) {
            return $mail->hasTo($customerEmail) &&
                   $mail->user->email === $customerEmail;
        });
    }

    public function test_single_step_registration_with_email_sends_welcome_mail()
    {
        Mail::fake();

        $customerEmail = 'onestep_' . time() . '@gmail.com';
        $response = $this->postJson('/register', [
            'username' => 'onestepuser' . time(),
            'email' => $customerEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'policy' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('redirect', route('register.welcome'));

        // Verify user created with real email
        $this->assertDatabaseHas('users', [
            'email' => $customerEmail,
        ]);

        // Verify welcome mail was sent directly
        Mail::assertSent(WelcomeNewUserMail::class, function ($mail) use ($customerEmail) {
            return $mail->hasTo($customerEmail) &&
                   $mail->user->email === $customerEmail;
        });
    }

    public function test_registration_without_username_sends_email_and_generates_username()
    {
        Mail::fake();

        $customerEmail = 'nousername_' . time() . '@gmail.com';
        $response = $this->postJson('/register', [
            'email' => $customerEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'policy' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('redirect', route('register.welcome'));

        // Verify user was created in database and username was auto-generated
        $user = User::where('email', $customerEmail)->first();
        $this->assertNotNull($user);
        $this->assertNotEmpty($user->username);
        $this->assertStringContainsString('nousername', $user->username);

        // Verify welcome mail was sent to this email
        Mail::assertSent(WelcomeNewUserMail::class, function ($mail) use ($customerEmail) {
            return $mail->hasTo($customerEmail);
        });
    }
}
