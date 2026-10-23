<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Professional = 'professional';
    case Client = 'client';
}
