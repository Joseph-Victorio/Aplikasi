<?php

namespace App\Helpers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ApiResponse
{
   private static function send($data, $http_status = 200)
   {
      return response()->json($data, $http_status);
   }

   public static function success($value = null, $msg = null)
   {
      $response = [];
      $response['success'] = true;
      $response['message'] = $msg ?? 'Request Success';
      if (!is_null($value)) {
         if (is_array($value)) {
            $response['results'] = $value;
         } elseif (is_object($value)) {
            $response['results'] = $value;
         } else {
            $response['results'] = $value;
         }
      }

      return self::send($response);
   }

   public static function failed($error = null)
   {
      if (env('APP_ENV', 'local') != 'production') {
         Log::debug($error);
      }
      $response = [];
      $response['success'] = false;
      $response['results'] = null;
      $response['message'] = null;
      $response['errors'] = [];

      $http_status = 400;

      if ($error instanceof ValidationException) {
         $http_status = 422;
         $response['message'] = 'Data yang input tidak valid';
         $response['errors'] = $error->errors();
      } elseif ($error instanceof QueryException) {
         $errors = [];
         if (env('APP_ENV', 'local') != 'production') {
            $errors['code'][] = $error->getCode();
            $errors['sql'][] = $error->getSql();
            $errors['bindings'][] = $error->getBindings();
         }
         $response['message'] = $error->getMessage();
         $response['errors'] = $errors;
      } elseif ($error instanceof ModelNotFoundException) {
         $errors = [];
         $http_status = 404;
         $response['message'] = 'Data not found';
      } elseif ($error instanceof Exception) {
         if (env('APP_DEBUG') == true) {
            $response['message'] = $error->getMessage();
            $response['errors'] = json_decode(json_encode($error->getTrace()));
         } else {
            $response['message'] = $error->getMessage();
         }
      } else {
         if (is_object($error) || is_array($error)) {
            $response['message'] = json_encode($error);
         } else {
            $response['message'] = $error;
         }
      }

      return self::send($response, $http_status);
   }
}
