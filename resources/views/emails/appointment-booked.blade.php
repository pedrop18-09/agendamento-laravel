<x-mail::message>
# Agendamento realizado

Olá, {{ $appointment->client->name }}!

Seu agendamento foi registrado:

- **Serviço:** {{ $appointment->service->name }}
- **Profissional:** {{ $appointment->professional->user->name }}
- **Data:** {{ $appointment->starts_at->format('d/m/Y') }}
- **Horário:** {{ $appointment->starts_at->format('H:i') }} às {{ $appointment->ends_at->format('H:i') }}

Se precisar cancelar, acesse "Meus agendamentos" no sistema.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
