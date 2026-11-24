<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * The preferred language defaults to the locale the visitor is browsing in.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input['locale'] ??= App::getLocale();

        Validator::make($input, [
            ...$this->profileRules(),
            'locale' => $this->localeRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'firstname' => $input['firstname'],
            'lastname' => $input['lastname'],
            'email' => $input['email'],
            'password' => $input['password'],
            'locale' => $input['locale'],
        ]);
    }
}
