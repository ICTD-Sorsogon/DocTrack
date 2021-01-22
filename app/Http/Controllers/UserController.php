<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\ChangePasswordPutRequest;
use App\Http\Requests\ChangeUsernamePutRequest;
use App\Http\Requests\ChangeFullnamePutRequest;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function getAuthUser(): User
    {
        return User::with('office', 'division', 'unit', 'sector')->find(Auth::id());
    }

    public function getAllUsers(): Collection
    {
        abort_if(!in_array(auth()->user()->role_id, [1,2]), 403);
        return User::where('role_id', 2)->with('office')->get();
    }

    public function getAllUserComplete(): Collection
    {
        abort_if(auth()->user()->role_id != 1, 403);
        return User::where('role_id', '<>', 1)
            ->with('office', 'division', 'sector', 'unit', 'role')
            ->get();
    }

    public function updateFullname(ChangeFullnamePutRequest $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        $user->first_name=$request->first_name;
        $user->middle_name=$request->middle_name;
        $user->last_name=$request->last_name;
        $user->suffix=$request->name_suffix;
        $user->save();
        $response = $user->wasChanged();
        return $response;
    }

    public function updateUsername(ChangeUsernamePutRequest $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        $user->username = $request->new_username;
        $user->save();
        if($user->wasChanged()) {
            return response()->json([
                'message' => 'Your new username has been set',
                'status' => 'success',
                'title' => 'Username Changed',
                'type' => 'success'
            ]);
        }
        return response()->json([
            'message' => 'No changes were made to your username',
            'status' => 'success',
            'title' => 'Username Not Changed',
            'type' => 'info'

        ]);
    }

    public function updatePassword(ChangePasswordPutRequest $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        if (Hash::check($request->old_password, Auth::user()->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'message' => 'Password was changed successfully',
                'status' => 'success',
                'title' => 'Password Change Success',
                'type' => 'success'

            ]);
        }
        return response()->json([
            'message' => 'The input for old password is incorrect',
            'status' => 'error',
            'title' => 'Password Change Failed',
            'type' => 'error'
        ]);
    }

    // public function updateUser(Request $request, string $userId)
    // {
    // // TODO: Add Log entry for each change
    // if ($request->form_type == 'account_details') {
    //     $validator = Validator::make($request->all(), [
    //         'first_name' => 'required|max:255',
    //         'middle_name' => 'required|max:255',
    //         'last_name' => 'required|max:255',
    //         'name_suffix' => 'nullable',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'Form validation error',
    //             'code' => 'FAILED'
    //         ]);
    //     }

    //     $user = User::where('id', $userId)->first();
    //     $user->first_name = ucfirst(trim($request->first_name));
    //     $user->middle_name = ucfirst(trim($request->middle_name));
    //     $user->last_name = ucfirst(trim($request->last_name));
    //     if ($request->name_suffix) {
    //         $user->suffix = ucfirst(trim($request->name_suffix));
    //     }
    //     $user->save();
    //     return response()->json([
    //         'message' => 'Account details successfully updated',
    //         'code' => 'SUCCESS',
    //     ]);
    //     } elseif ($request->form_type == 'account_username') {
    //         $validator = Validator::make($request->all(), [
    //             'new_username' => 'required|max:255',
    //             'confirm_username' => 'required|max:255',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'message' => 'Form validation error',
    //                 'code' => 'FAILED'
    //             ]);
    //         }

    //         $user = User::where('id', $userId)->first();
    //         if ($request->new_username == $request->confirm_username) {
    //             $user->username = $request->new_username;
    //             $user->save();
    //             return response()->json([
    //                 'message' => 'Username successfully updated',
    //                 'code' => 'SUCCESS'
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'message' => 'Username update failed',
    //                 'code' => 'FAILED',
    //             ]);
    //         }
    //     } else {
    //         $validator = Validator::make($request->all(), [
    //             'old_password' => 'required|max:255',
    //             'new_password' => 'required|max:255',
    //             'confirm_password' => 'required|max:255',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'message' => 'Form validation error',
    //                 'code' => 'FAILED'
    //             ]);
    //         }

    //         if ($request->confirm_password != $request->new_password) {
    //             return response()->json([
    //                 'message' => 'Password update failed, password confirmation and new password do not match',
    //                 'code' => 'FAILED'
    //             ]);
    //         }

    //         $user = User::where('id', $userId)->first();
    //         if (Hash::check($request->old_password, $user->password)) {
    //             $user->password = Hash::make($request->new_password);
    //             $user->save();
    //             return response()->json([
    //                 'message' => 'Password successfully updated',
    //                 'code' => 'SUCCESS'
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'message' => 'Password update failed',
    //                 'code' => 'FAILED'
    //             ]);
    //         }
    //     }
    // }
}
