<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use App\Services\AvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private Professional $professional;
    private Service $service;
    private AvailabilityService $availability;

    protected function setUp(): void
    {
        parent::setUp();

        $this->availability = new AvailabilityService();

        $this->professional = Professional::create([
            'user_id' => User::factory()->create()->id,
        ]);

        $this->service = Service::create([
            'name' => 'Corte',
            'duration_minutes' => 30,
            'price' => 40,
        ]);

        // Segunda-feira: 09-12 e 13-18
        $this->professional->availabilities()->create([
            'day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '12:00',
        ]);
        $this->professional->availabilities()->create([
            'day_of_week' => 1, 'start_time' => '13:00', 'end_time' => '18:00',
        ]);
    }

    private function monday(): CarbonImmutable
    {
        // Sempre uma segunda-feira no futuro, independente do locale
        return CarbonImmutable::now()->addWeeks(2)->next(CarbonImmutable::MONDAY)->startOfDay();
    }

    private function book(CarbonImmutable $start, int $minutes = 30, AppointmentStatus $status = AppointmentStatus::Pending): Appointment
    {
        return Appointment::create([
            'client_id' => User::factory()->create()->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'starts_at' => $start,
            'ends_at' => $start->addMinutes($minutes),
            'status' => $status,
        ]);
    }

    public function test_gera_slots_respeitando_o_almoco(): void
    {
        $slots = $this->availability->slotsFor($this->professional, $this->service, $this->monday());

        $this->assertSame('09:00', $slots[0]);
        $this->assertContains('11:30', $slots);
        $this->assertNotContains('12:00', $slots);
        $this->assertNotContains('12:30', $slots);
        $this->assertContains('13:00', $slots);
        $this->assertContains('17:30', $slots);
        $this->assertNotContains('18:00', $slots);
    }

    public function test_dia_sem_disponibilidade_retorna_vazio(): void
    {
        $sunday = $this->monday()->subDay();

        $this->assertSame([], $this->availability->slotsFor($this->professional, $this->service, $sunday));
    }

    public function test_agendamento_remove_o_slot_ocupado(): void
    {
        $monday = $this->monday();
        $this->book($monday->setTime(9, 0));

        $slots = $this->availability->slotsFor($this->professional, $this->service, $monday);

        $this->assertNotContains('09:00', $slots);
        $this->assertContains('09:30', $slots);
    }

    public function test_agendamento_cancelado_libera_o_horario(): void
    {
        $monday = $this->monday();
        $this->book($monday->setTime(9, 0), 30, AppointmentStatus::Cancelled);

        $slots = $this->availability->slotsFor($this->professional, $this->service, $monday);

        $this->assertContains('09:00', $slots);
    }

    public function test_borda_agendamento_que_termina_as_10_nao_bloqueia_slot_das_10(): void
    {
        $monday = $this->monday();
        $this->book($monday->setTime(9, 30)); // 09:30 às 10:00

        $slots = $this->availability->slotsFor($this->professional, $this->service, $monday);

        $this->assertNotContains('09:30', $slots);
        $this->assertContains('10:00', $slots);
        $this->assertContains('09:00', $slots);
    }

    public function test_servico_longo_nao_cabe_em_buraco_pequeno(): void
    {
        $monday = $this->monday();
        $long = Service::create(['name' => 'Coloração', 'duration_minutes' => 60, 'price' => 120]);

        $this->book($monday->setTime(9, 30)); // ocupa 09:30-10:00

        $slots = $this->availability->slotsFor($this->professional, $long, $monday);

        // 09:00-10:00 colide com o agendamento das 09:30
        $this->assertNotContains('09:00', $slots);
        $this->assertContains('10:00', $slots);
    }

    public function test_horarios_passados_de_hoje_nao_aparecem(): void
    {
        $this->travelTo(CarbonImmutable::parse('next monday 10:15'));

        $slots = $this->availability->slotsFor($this->professional, $this->service, CarbonImmutable::now());

        $this->assertNotContains('09:00', $slots);
        $this->assertNotContains('10:00', $slots);
        $this->assertContains('10:30', $slots);
    }

    public function test_folga_remove_os_slots_do_periodo(): void
    {
        $monday = $this->monday();

        $this->professional->timeOffs()->create([
            'starts_at' => $monday->setTime(10, 0),
            'ends_at' => $monday->setTime(11, 0),
        ]);

        $slots = $this->availability->slotsFor($this->professional, $this->service, $monday);

        $this->assertContains('09:30', $slots);
        $this->assertNotContains('10:00', $slots);
        $this->assertNotContains('10:30', $slots);
        $this->assertContains('11:00', $slots); // borda: a folga termina às 11:00
    }
}
