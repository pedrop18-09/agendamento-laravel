<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@exemplo.com'],
            ['name' => 'Admin', 'password' => 'senha1234', 'role' => UserRole::Admin]
        );

        // Cliente
        User::updateOrCreate(
            ['email' => 'cliente@exemplo.com'],
            ['name' => 'Cliente Demo', 'password' => 'senha1234', 'role' => UserRole::Client]
        );

        // Serviços
        $corte = Service::updateOrCreate(
            ['name' => 'Corte de cabelo'],
            ['duration_minutes' => 30, 'price' => 40]
        );
        $barba = Service::updateOrCreate(
            ['name' => 'Barba'],
            ['duration_minutes' => 20, 'price' => 25]
        );
        $coloracao = Service::updateOrCreate(
            ['name' => 'Coloração'],
            ['duration_minutes' => 60, 'price' => 120]
        );

        // Profissionais
        $profissionais = [
            ['email' => 'joao@exemplo.com', 'name' => 'João Barbeiro', 'title' => 'Barbeiro', 'services' => [$corte, $barba]],
            ['email' => 'maria@exemplo.com', 'name' => 'Maria Cabeleireira', 'title' => 'Cabeleireira', 'services' => [$corte, $coloracao]],
        ];

        foreach ($profissionais as $dados) {
            $user = User::updateOrCreate(
                ['email' => $dados['email']],
                ['name' => $dados['name'], 'password' => 'senha1234', 'role' => UserRole::Professional]
            );

            $professional = Professional::updateOrCreate(
                ['user_id' => $user->id],
                ['title' => $dados['title'], 'bio' => 'Profissional de demonstração', 'active' => true]
            );

            $professional->services()->sync(collect($dados['services'])->pluck('id'));

            // Segunda a sexta, 09-12 e 13-18
            $professional->availabilities()->delete();
            foreach ([1, 2, 3, 4, 5] as $dia) {
                $professional->availabilities()->create(['day_of_week' => $dia, 'start_time' => '09:00', 'end_time' => '12:00']);
                $professional->availabilities()->create(['day_of_week' => $dia, 'start_time' => '13:00', 'end_time' => '18:00']);
            }
        }
    }
}
