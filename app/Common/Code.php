<?php

namespace App\Common;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    //
    const HTTP_SUCCESS = 0;
    const HTTP_ERROR = 1;
    const HTTP_PROHIBIT = 2;
    const HTTP_UNKNOWN = 3;
}
