<?php
declare(strict_types=1);

namespace App\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AppVars {

    public static function getAppJSON(): string
    {
        $settings = [];
        $login_user = User::where('id', Auth::id());
        $settings['auth_user'] = $login_user->with(['role', 'office', 'division', 'unit', 'sector'])->first();
        $encoded = json_encode($settings);
        $encoded = addslashes($encoded);
        return $encoded;
    }

}
