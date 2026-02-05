<?php

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

if (!function_exists('getSessionUser')) {

  function getSessionUser()
  {

    $sessionUser = request()->server('HTTP_SESSION_USER');

    return $sessionUser;
  }
}
