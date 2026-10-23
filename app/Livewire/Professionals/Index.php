<?php

namespace App\Livewire\Professionals;

use App\Enums\UserRole;
use App\Models\Service;
use App\Models\Professional;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    public ?int $user_id = null;

    #[Validate('nullable|string|max:100', as: 'cargo')]
    public string $title = '';

    #[Validate('nullable|string|max:500', as: 'bio')]
    public string $bio = '';

    public bool $active = true;

    public ?int $editingId = null;
    public ?int $deletingId = null;
    public string $filter = 'all';
    public array $service_ids = [];

    public function save(): void
    {
        if ($this->editingId) {
            $this->validate();

            $professional = Professional::findOrFail($this->editingId);
            $professional->update([
                'title' => $this->title ?: null,
                'bio' => $this->bio ?: null,
                'active' => $this->active,
            ]);
        } else {
            $this->validate([
                'user_id' => 'required|exists:users,id|unique:professionals,user_id',
            ], [], ['user_id' => 'usuário']);
            $this->validate();

            $professional = Professional::create([
                'user_id' => $this->user_id,
                'title' => $this->title ?: null,
                'bio' => $this->bio ?: null,
                'active' => $this->active,
            ]);

            User::whereKey($this->user_id)->update(['role' => UserRole::Professional]);
        }

        $professional->services()->sync(
            Service::whereIn('id', $this->service_ids)->pluck('id')
        );

        $this->cancelEdit();
    }

    public function edit(int $id): void
    {
        $professional = Professional::findOrFail($id);

        $this->editingId = $professional->id;
        $this->title = $professional->title ?? '';
        $this->bio = $professional->bio ?? '';
        $this->active = $professional->active;
        $this->service_ids = $professional->services->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'user_id', 'title', 'bio', 'active', 'service_ids']);
        $this->resetValidation();
    }

    public function askDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->dispatch('open-modal', 'confirm-delete');
    }

    public function confirmDelete(): void
    {
        if ($this->deletingId) {
            $professional = Professional::findOrFail($this->deletingId);

            // Volta o usuário para cliente antes de remover o perfil
            User::whereKey($professional->user_id)->update(['role' => UserRole::Client]);
            $professional->delete();
        }

        $this->deletingId = null;
        $this->dispatch('close-modal', 'confirm-delete');
    }

    public function render()
    {
        $professionals = Professional::with(['user', 'services'])
            ->when($this->filter === 'active', fn ($q) => $q->where('active', true))
            ->when($this->filter === 'inactive', fn ($q) => $q->where('active', false))
            ->get();

        return view('livewire.professionals.index', [
            'professionals' => $professionals,
            'services' => Service::where('active', true)->orderBy('name')->get(),
            'availableUsers' => User::where('role', UserRole::Client)
                ->whereDoesntHave('professional')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
