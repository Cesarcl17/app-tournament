<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailConfiguration extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:test {email? : Email address to send test to}';

    /**
     * The console command description.
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📧 Email Configuration Test');
        $this->line('');

        // Show current configuration
        $mailer = config('mail.default');
        $this->table(['Setting', 'Value'], [
            ['MAIL_MAILER', $mailer],
            ['MAIL_HOST', config('mail.mailers.smtp.host') ?? 'N/A'],
            ['MAIL_PORT', config('mail.mailers.smtp.port') ?? 'N/A'],
            ['MAIL_FROM_ADDRESS', config('mail.from.address') ?? 'N/A'],
            ['MAIL_FROM_NAME', config('mail.from.name') ?? 'N/A'],
        ]);

        $this->line('');

        if ($mailer === 'log') {
            $this->warn('⚠️  Using LOG mailer - emails will be written to storage/logs/laravel.log');
            $this->info('This is perfect for development, but you should configure SMTP for production.');
            $this->line('');
        }

        $email = $this->argument('email');

        if (!$email) {
            if (!$this->confirm('Do you want to send a test email?')) {
                $this->info('Configuration check complete. No test email sent.');
                return 0;
            }

            $email = $this->ask('Enter email address to send test to');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address: ' . $email);
            return 1;
        }

        $this->info("Sending test email to: {$email}");

        try {
            Mail::raw(
                "This is a test email from App Tournament.\n\nIf you received this, your email configuration is working correctly!\n\nSent at: " . Carbon::now()->format('Y-m-d H:i:s'),
                function ($message) use ($email) {
                    $message->to($email)
                            ->subject('App Tournament - Test Email');
                }
            );

            $this->info('✅ Test email sent successfully!');

            if ($mailer === 'log') {
                $this->line('');
                $this->info('Check storage/logs/laravel.log to see the email content.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            $this->line('');
            $this->warn('Common issues:');
            $this->line('  - Invalid SMTP credentials');
            $this->line('  - Firewall blocking port');
            $this->line('  - Gmail requires "App Password" (not regular password)');
            $this->line('');
            $this->info('Check docs/EMAIL_CONFIGURATION.md for setup instructions.');

            return 1;
        }
    }
}
