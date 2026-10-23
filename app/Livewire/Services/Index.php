<?php

namespace App\Livewire\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\Validate;


#[Layout('layouts.app')]
class Index extends Component
{
    #[Validate('required|string|max:100', as: 'nome')]
    public string $name = '';

    #[Validate('required|integer|min:5|max:480', as: 'duração')]
    public int $duration_minutes = 30;

    #[Validate('required|numeric|min:0', as: 'preço')]
    public string $price = '';

    public ?int $deletingId = null;

    public ?int $editingId = null;

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'duration_minutes' => $this->duration_minutes,
            'price' => $this->price,
        ];

        if ($this->editingId){
            Service::findOrFail($this->editingId)->update($data);
        }

        else{
            Service::create($data);
        }

        $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.services.index', [
            'services' => Service::orderBy('name')->get()
        ]);
    }

    public function askDelete(int $id): void
    {
        $this->deletingId = $id;

        $this->dispatch('open-modal', 'confirm-delete');
    }

    public function confirmDelete(): void
    {
        if ($this->deletingId){
            Service::findOrFail($this->deletingId)->delete();
        }

        $this->deletingId = null;

        $this->dispatch('close-modal', 'confirm-delete');
    }

    public function edit(int $id): void
    {
        $service = Service::findOrFail($id);

        $this->editingId = $service->id;
        $this->name = $service->name;
        $this->duration_minutes = $service->duration_minutes;
        $this->price = (string) $service->price;
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'name', 'duration_minutes', 'price']);

        $this->resetValidation();
    }
}
