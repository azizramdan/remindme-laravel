<?php

namespace App\Enums;

enum TokenName: string
{
    case ACCESS_TOKEN = 'access-token';
    case REFRESH_TOKEN = 'refresh-token';
}