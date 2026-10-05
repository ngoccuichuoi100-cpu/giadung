<?php

namespace App\Console\Commands;

use App\Mail\WelcomeNewUserMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email : Địa chỉ email người nhận}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi email thử nghiệm chào mừng thành viên mới để kiểm tra cấu hình SMTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetEmail = $this->argument('email');
        $this->info("Đang kiểm tra kết nối và gửi email thử nghiệm tới: {$targetEmail}...");

        $dummyUser = new User([
            'username' => 'khachhang_test',
            'name' => 'Khách Hàng Trải Nghiệm',
            'email' => $targetEmail,
            'phone' => '0912 345 678',
            'points' => 50,
            'referral_code' => 'GDPRO888',
        ]);

        try {
            Mail::to($targetEmail)->send(new WelcomeNewUserMail($dummyUser));
            $this->info("✓ THÀNH CÔNG! Email thật đã được gửi tới {$targetEmail}.");
            $this->info("Vui lòng mở hộp thư đến (inbox hoặc spam) của {$targetEmail} để kiểm tra thư.");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("✗ THẤT BẠI: Không thể gửi email qua SMTP!");
            $this->error("Chi tiết lỗi: " . $e->getMessage());
            $this->line("");
            $this->warn("Gợi ý khắc phục:");
            $this->line("1. Đảm bảo đã bật 'Xác minh 2 bước' cho tài khoản Google.");
            $this->line("2. Tạo 'Mật khẩu ứng dụng' (App Password - 16 ký tự) và điền vào MAIL_PASSWORD trong file .env.");
            $this->line("3. Đảm bảo MAIL_USERNAME và MAIL_FROM_ADDRESS là cùng một địa chỉ Gmail.");
            return Command::FAILURE;
        }
    }
}
