<?php

namespace App\Enums;

enum Role: string
{
    case Responsible = 'Responsible';
    case Driver = 'Driver';
    case Administrator = 'Administrator';
}
