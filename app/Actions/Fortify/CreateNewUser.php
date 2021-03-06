<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

use Illuminate\Notifications\Notifiable;
// use Illuminate\Support\Facades\Notification;
use App\Notifications\UserRegisteredNotification;
use App\Jobs\Excel\UserDetailsWriteJob;
use App\Events\NewUserRegisteredEvent;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    use Notifiable;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $receiver = User::where('role',42)->first(); //Admin

        // $receiver->notify((new UserRegisteredNotification($user))->delay([
        //     'mail' => now()->addMinutes(5)
        // ]));

        // Send Notification immediately
        // Notification::sendNow($receiver, new UserRegisteredNotification($user));

        // UserDetailsWriteJob::dispatchSync($user);

        event(new NewUserRegisteredEvent($user));

        return $user;
    }
}
