# Sistema de Agendamento

Sistema web de agendamento de horários para barbearias, salões e clínicas, feito com **Laravel** e **Livewire**. O cliente escolhe serviço, profissional, data e horário; o sistema calcula automaticamente os horários livres, evita conflitos e envia e-mails de confirmação e lembrete.

## Tecnologias

- PHP 8.4 e Laravel
- Livewire (componentes de página, sem API separada)
- Laravel Breeze (autenticação)
- Tailwind CSS e Alpine.js
- SQLite (padrão), compatível com MySQL e PostgreSQL
- Filas (`database`) e scheduler para e-mails
- PHPUnit para testes automatizados

## Funcionalidades

**Cliente**
- Agendamento em etapas: serviço, profissional, data e horário
- Horários livres calculados em tempo real
- "Meus agendamentos" com cancelamento (mínimo de 2 horas de antecedência)
- E-mail de confirmação ao agendar e lembrete 24 horas antes

**Profissional**
- Agenda diária com navegação entre dias
- Confirmar, concluir, marcar não comparecimento ou cancelar atendimentos
- Cadastro de folgas e bloqueios de horário

**Administrador**
- CRUD de serviços e profissionais
- Vínculo entre profissionais e os serviços que atendem
- Disponibilidade semanal por profissional (com suporte a intervalo de almoço)
- Painel com totais

## Destaques técnicos

- **Motor de horários** (`AvailabilityService`): gera os slots a partir da disponibilidade, remove conflitos com agendamentos, folgas e horários passados. Trata o caso de borda em que um atendimento que termina às 10:00 não bloqueia outro que começa às 10:00.
- **Concorrência**: a reserva roda em transação com `lockForUpdate()` e revalida o horário antes de gravar, impedindo que dois clientes peguem o mesmo horário.
- **Autorização em camadas**: middleware para rotas de admin, policy para cancelamento de agendamentos e filtros por dono em todas as consultas.
- **Máquina de estados**: as transições de status permitidas ficam no enum `AppointmentStatus`.
- **Filas e scheduler**: e-mails enviados em segundo plano, com marcação (`reminder_sent_at`) para não duplicar lembretes.

## Como rodar

Requisitos: PHP, Composer e Node.js.

```bash
git clone https://github.com/SEU-USUARIO/agendamento.git
cd agendamento

composer install
cp .env.example .env
php artisan key:generate
```

No Windows (Prompt de Comando), use `copy .env.example .env` no lugar do `cp`.

Configure o fuso horário no `.env`:

```
APP_TIMEZONE=America/Sao_Paulo
```

Depois:

```bash
php artisan migrate --seed
npm install
npm run dev
```

Em outro terminal:

```bash
php artisan serve
```

Acesse `http://localhost:8000`.

### E-mails e lembretes (opcional)

Em desenvolvimento, os e-mails são gravados em `storage/logs/laravel.log` (`MAIL_MAILER=log`). Para processá-los, mantenha um worker rodando:

```bash
php artisan queue:work
```

Para o envio automático dos lembretes:

```bash
php artisan schedule:work
```

Em produção, o scheduler roda por uma entrada no cron do servidor (`php artisan schedule:run` a cada minuto).

## Contas de demonstração

Criadas pelo seeder. Senha de todas: `senha1234`

| Perfil | E-mail |
|---|---|
| Administrador | admin@exemplo.com |
| Profissional | joao@exemplo.com |
| Profissional | maria@exemplo.com |
| Cliente | cliente@exemplo.com |

Os profissionais trabalham de segunda a sexta, das 09:00 às 12:00 e das 13:00 às 18:00.

## Testes

```bash
php artisan test
```

Cobrem o motor de horários (almoço, conflitos, cancelamentos, folgas, bordas e horários passados) e o fluxo de agendamento (incluindo a tentativa de dois clientes no mesmo horário).

## Estrutura principal

```
app/
  Enums/            UserRole, AppointmentStatus
  Livewire/         telas (serviços, profissionais, agenda, agendamento...)
  Mail/             AppointmentBooked, AppointmentReminder
  Models/           User, Professional, Service, Availability, TimeOff, Appointment
  Policies/         AppointmentPolicy
  Services/         AvailabilityService
  Console/Commands/ SendAppointmentReminders
database/seeders/   DemoSeeder
tests/Feature/      AvailabilityServiceTest, BookingTest
```

## Possíveis evoluções

- Tela de gestão de todos os agendamentos para o admin
- Passo fixo de horários (ex.: de 30 em 30 minutos), independente da duração do serviço
- Aviso de conflitos ao cadastrar folgas sobre agendamentos existentes
- Integração com WhatsApp, Google Calendar ou pagamento de sinal via Pix
- Deploy

## Licença

Projeto de estudo, uso livre.
