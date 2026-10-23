<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Services\Index as ServicesIndex;
use App\Livewire\Professionals\Index as ProfessionalsIndex;
use App\Livewire\Availabilities\Index as AvailabilitiesIndex;
use App\Livewire\Booking\Create as BookingCreate;
use App\Livewire\Appointments\Mine as AppointmentsMine;
use App\Livewire\Appointments\Agenda as AppointmentsAgenda;
use App\Livewire\Timeoffs\Index as TimeoffsIndex;
use Illuminate\Support\Facades\Schedule;
use App\Livewire\Dashboard;

Route::view('/', 'welcome');

Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/services', ServicesIndex::class)
    ->middleware(['auth'])
    ->name('services.nidex');

Route::get('/services', ServicesIndex::class)
    ->middleware(['auth', 'admin'])
    ->name('services.index');


Route::get('/professionals', ProfessionalsIndex::class)
    ->middleware(['auth', 'admin'])
    ->name('professionals.index');


Route::get('/availabilities', AvailabilitiesIndex::class)
    ->middleware(['auth', 'admin'])
    ->name('availabilities.index');

Route::get('/agendar', BookingCreate::class)
    ->middleware(['auth'])
    ->name('booking.create');

Route::get('/meus-agendamentos', AppointmentsMine::class)
    ->middleware(['auth'])
    ->name('appointments.mine');


Route::get('/minha-agenda', AppointmentsAgenda::class)
    ->middleware(['auth'])
    ->name('appointments.agenda');


Route::get('/minhas-folgas', TimeoffsIndex::class)
    ->middleware(['auth'])
    ->name('timeoffs.index');


Schedule::command('appointments:send-reminders')->everyFifteenMinutes();
require __DIR__.'/auth.php';
