<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;

    class User extends Authenticatable
    {
        /** @use HasFactory<\Database\Factories\UserFactory> */
        use HasFactory, Notifiable;

        /**
         * Viktigt: använd tenant-anslutningen.
         */
        protected $connection = 'tenant';

        /**
         * Mass-assignable fält.
         * @var list<string>
         */
        protected $fillable = [
            'name',
            'email',
            'password',
            'address',
            'city',
            'zipcode',
        ];

        /**
         * Dölj vid serialisering.
         * @var list<string>
         */
        protected $hidden = [
            'password',
            'remember_token',
        ];

        /**
         * Casts.
         * @return array<string,string>
         */
        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                // Hashar automatiskt lösenord när du sätter det
                'password' => 'hashed',
            ];
        }
    }
