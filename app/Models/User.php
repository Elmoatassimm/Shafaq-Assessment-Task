<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{

    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }




    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {
        return [];
    }

    public function payment()
    {
        return $this->hasOne(\App\Models\Payment::class);
    }

    /**
     * Check if the user has a successful payment.
     *
     * @return bool
     */
    public function isPaid()
    {
        $payment = $this->payment()->first(); // Get the actual payment record

        if (!$payment || $payment->status === 'failed' || $payment->status === 'pending') {
            return false;
        }

        if ($payment->status === 'success') return true;
    }
}
