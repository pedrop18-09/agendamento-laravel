<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Livewire\Booking\Create;
use App\Mail\AppointmentBooked;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private Professional $professional;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->professional = Professional::create([
            'user_id' => User::factory()->create()->id,
        ]);

        $this->service = Service::create([
            'name' => 'Corte',
            'duration_minutes' => 30,
            'price' => 40,
        ]);

        $this->professional->services()->attach($this->service->id);

        $this->professional->availabilities()->create([
            'day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '18:00',
        ]);
    }

    private function monday(): string
    {
        return CarbonImmutable::now()->addWeeks(2)->next(CarbonImmutable::MONDAY)->toDateString();
    }

    private function attemptBooking(User $client, string $slot)
    {
        return Livewire::actingAs($client)
            ->test(Create::class)
            ->set('service_id', $this->service->id)
            ->set('professional_id', $this->professional->id)
            ->set('date', $this->monday())
            ->call('selectSlot', $slot)
            ->call('book');
    }

    public function test_cliente_agenda_um_horario_livre(): void
    {
        Mail::fake();
        $client = User::factory()->create();

        $this->attemptBooking($client, '10:00')->assertHasNoErrors();

        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'professional_id' => $this->professional->id,
            'status' => AppointmentStatus::Pending->value,
        ]);

        Mail::assertQueued(AppointmentBooked::class);
    }

    public function test_segundo_cliente_nao_pega_o_mesmo_horario(): void
    {
        Mail::fake();

        $this->attemptBooking(User::factory()->create(), '10:00')->assertHasNoErrors();
        $this->attemptBooking(User::factory()->create(), '10:00')->assertHasErrors('selectedSlot');

        $this->assertSame(1, Appointment::count());
    }

    public function test_horario_cancelado_pode_ser_reservado_de_novo(): void
    {
        Mail::fake();

        $this->attemptBooking(User::factory()->create(), '10:00');
        Appointment::first()->update(['status' => AppointmentStatus::Cancelled]);

        $this->attemptBooking(User::factory()->create(), '10:00')->assertHasNoErrors();

        $this->assertSame(2, Appointment::count());
    }

    public function test_profissional_que_nao_atende_o_servico_e_recusado(): void
    {
        $this->professional->services()->detach();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->attemptBooking(User::factory()->create(), '10:00');
    }
}
