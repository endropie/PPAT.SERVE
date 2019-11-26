<?php
namespace App\Models;

trait WithUserBy
{
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function user_by()
	{
        return $this->belongsTo('App\Models\Auth\User', 'created_by');
    }

    public static function bootWithUserBy()
	{
		static::creating(function ($model)
        {
            if ($user = auth()->user()) {
                $model->created_by = $user->id ?? null;
            }
            return $model;
        });

	}
}
