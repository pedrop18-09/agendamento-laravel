<?php

namespace App\Livewire\Availabilities;

use App\Models\Availability;
use App\Models\Professional;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    public ?int $professional_id = null;

    #[Validate('required|integer|between:0,6', as: 'dia da semana')]
    public ?int $day_of_week = 1;

    #[Validate('required|date_format:H:i', as: 'início')]
    public string $start_time = '09:00';

    #[Validate('required|date_format:H:i|after:start_time', as: 'fim')]
    public string $end_time = '18:00';

    public function save(): void
    {
        $this->validate([
            'professional_id' => 'required|exists:professionals,id',
        ], [], ['professional_id' => 'profissional']);
        $this->validate();

        $overlaps = Availability::where('professional_id', $this->professional_id)
            ->where('day_of_week', $this->day_of_week)
            ->where('start_time', '<', $this->end_time)
            ->where('end_time', '>', $this->start_time)
            ->exists();

        if ($overlaps) {
            $this->addError('start_time', 'Esse horário se sobrepõe a outro já cadastrado neste dia.');
            return;
        }

        Availability::create([
            'professional_id' => $this->professional_id,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);

        $this->resetValidation();
    }

    public function delete(int $id): void
    {
        Availability::where('professional_id', $this->professional_id)
            ->findOrFail($id)
            ->delete();
    }

    public function render()
    {
        return view('livewire.availabilities.index', [
            'professionals' => Professional::with('user')->get(),
            'availabilities' => $this->professional_id
                ? Availability::where('professional_id', $this->professional_id)
                    ->orderBy('day_of_week')
                    ->orderBy('start_time')
                    ->get()
                : collect(),
            'days' => [
                0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta',
                4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado',
            ],
        ]);
    }
}
