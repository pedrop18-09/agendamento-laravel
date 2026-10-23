<?php

namespace App\Livewire\Timeoffs;

use App\Models\TimeOff;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    #[Validate('required|date', as: 'início')]
    public string $starts_at = '';

    #[Validate('required|date|after:starts_at', as: 'fim')]
    public string $ends_at = '';

    #[Validate('nullable|string|max:255', as: 'motivo')]
    public string $reason = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->professional, 403);
    }

    public function save(): void
    {
        $professional = auth()->user()->professional;
        abort_unless($professional, 403);

        $this->validate();

        TimeOff::create([
            'professional_id' => $professional->id,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'reason' => $this->reason ?: null,
        ]);

        $this->reset(['starts_at', 'ends_at', 'reason']);
    }

    public function delete(int $id): void
    {
        $professional = auth()->user()->professional;
        abort_unless($professional, 403);

        TimeOff::where('professional_id', $professional->id)->findOrFail($id)->delete();
    }

    public function render()
    {
        $professional = auth()->user()->professional;
        abort_unless($professional, 403);

        return view('livewire.timeoffs.index', [
            'timeOffs' => TimeOff::where('professional_id', $professional->id)
                ->orderByDesc('starts_at')
                ->get(),
        ]);
    }
}
