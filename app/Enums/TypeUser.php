<?php

namespace App\Enums;

enum TypeUser: string
{
    case ADMIN = 'admin';
    case ADMIN2 = 'admin2';
    case ADMIN3 = 'admin3';

    public static function toArray(): array
  {
      return array_column(TypeUser::cases(), 'value');
  }

}



