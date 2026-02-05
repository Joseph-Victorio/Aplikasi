<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSession {
  
  public static function bootHasSession()
  {
    $sessionId = getSessionUser();

    if($sessionId) {

      static::addGlobalScope('session_id', function(Builder $builder) use($sessionId){
        return $builder->where('session_id', $sessionId);
      });
      
    }

  }

}