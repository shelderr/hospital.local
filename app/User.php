<?php

namespace App;

use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;


class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_doctor','slug'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Пользователь может иметь много статей
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blog() {
        return $this->hasMany('App\Blog');
    }
    /**
     * Пользователь может иметь много заказов
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders() {
        return $this->hasMany('App\Order');
    }
    /**
     * Пользователь может иметь много нот
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products() {
        return $this->hasMany('App\Product');
    }

    /**
     * Пользователь может много раз зарегистрироваться к врачам
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function registrations() {
        return $this->HasMany('App\Registration');
    }

    public function doctors(){
        return $this->hasOne('App\Doctors');
    }

}
