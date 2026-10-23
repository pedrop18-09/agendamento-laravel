<?php

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Envia lembrete aos clientes com atendimento nas próximas 24 horas';

    public function handle(): int
    {
        $appointments = Appointment::with(['client', 'service', 'professional.user'])
            ->whereIn('status', [AppointmentStatus::Pending, AppointmentStatus::Confirmed])
            ->whereNull('reminder_sent_at')
            ->where('starts_at', '>', now())
            ->where('starts_at', '<=', now()->addHours(24))
            ->get();

        foreach ($appointments as $appointment) {
            Mail::to($appointment->client)->send(new AppointmentReminder($appointment));
            $appointment->update(['reminder_sent_at' => now()]);
        }

        $this->info("Lembretes enviados: {$appointments->count()}");

        return self::SUCCESS;
    }
}
