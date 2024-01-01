<?php

namespace App\Enums;

enum CommonError: string
{
    case ERR_INVALID_CREDS = 'ERR_INVALID_CREDS';
    case ERR_INVALID_REFRESH_TOKEN = 'ERR_INVALID_REFRESH_TOKEN';
    case ERR_BAD_REQUEST = 'ERR_BAD_REQUEST';
    case ERR_INVALID_ACCESS_TOKEN = 'ERR_INVALID_ACCESS_TOKEN';
    case ERR_FORBIDDEN_ACCESS = 'ERR_FORBIDDEN_ACCESS';
    case ERR_NOT_FOUND = 'ERR_NOT_FOUND';
    case ERR_INTERNAL_ERROR = 'ERR_INTERNAL_ERROR';
}