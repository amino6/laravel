<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255','alpha_dash'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'tagline' => ['string', 'max:255'],
            'name' => ['string', 'max:255'],
            'about' => ['string', 'min:20'],
            'formatted_address' => ['string', 'max:255'],
            'latitude' => ['number', 'min:-90', 'max:90'],
            'longitude' => ['number', 'min:-180', 'max:180'],
        ])->validateWithBag('updateProfileInformation');

        if(isset($input['latitude']) && isset($input['longitude']))
            $location = new Point($input['latitude'],$input['longitude']);

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'username' => $input['username'],
                'tagline' => $input['tagline'] ?? $user->tagline,
                'about' => $input['about'] ?? $user->about,
                'location' => isset($location) ? $location : $user->location,
                'formatted_address' => $input['formatted_address'] ?? $user->formatted_address,
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
