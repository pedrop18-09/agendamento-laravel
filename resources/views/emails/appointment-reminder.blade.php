<x-mail::message>
# Lembrete de agendamento

Olá, {{ $appointment->client->name }}!

Você tem um atendimento nas próximas 24 horas:

- **Serviço:** {{ $appointment->service->name }}
- **Profissional:** {{ $appointment->professional->user->name }}
- **Data:** {{ $appointment->starts_at->format('d/m/Y') }}
- **Horário:** {{ $appointment->starts_at->format('H:i') }}

Se não puder comparecer, cancele em "Meus agendamentos" (até 2 horas antes).

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
