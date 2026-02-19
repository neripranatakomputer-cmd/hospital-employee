<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckSipExpiry extends Command
{
    protected $signature = 'sip:check-expiry';
    protected $description = 'Cek dan kirim notifikasi SIP yang akan kadaluarsa';

    public function handle()
    {
        $days = (int) config('app.sip_notify_days', 30);

        $expiring = Employee::expiringSip($days)->get();
        $expired  = Employee::expiredSip()->get();

        $this->info("SIP hampir kadaluarsa: {$expiring->count()}");
        $this->info("SIP sudah kadaluarsa: {$expired->count()}");

        foreach ($expiring as $emp) {
            $daysLeft = $emp->sip_days_left;
            $this->line("  ⚠ {$emp->nama_lengkap} - SIP berakhir {$emp->tat_sip->format('d/m/Y')} ({$daysLeft} hari lagi)");
        }

        foreach ($expired as $emp) {
            $this->line("  ✗ {$emp->nama_lengkap} - SIP KADALUARSA sejak {$emp->tat_sip->format('d/m/Y')}");
        }

        // Send email notifications (if mail configured)
        // You can customize this to send to HR email
        // Mail::to('hr@hospital.com')->send(new SipExpiryNotification($expiring, $expired));

        $this->info('Pengecekan SIP selesai.');
    }
}
