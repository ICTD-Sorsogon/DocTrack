<?php

namespace App\Http\Controllers;

use App\Events\AccountEvent;
use App\Events\AccountFullnameUpdateEvent;
use App\Events\AccountPasswordUpdateEvent;
use App\Events\AccountUsernameUpdateEvent;
use App\Events\UserCreateEvent;
use App\Events\UserDeleteEvent;
use App\Events\UserEvent;
use App\Events\UserUpdateEvent;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\ChangePasswordPutRequest;
use App\Http\Requests\ChangeUsernamePutRequest;
use App\Http\Requests\ChangeFullnamePutRequest;
use Illuminate\Support\Facades\Validator;
use stdClass;

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
        return User::with('office')->get();
    }

    public function getAllUserComplete(): Collection
    {
        abort_if(auth()->user()->role_id != 1, 403);
        return User::where('role_id', '<>', 1)
            ->with('office', 'division', 'sector', 'unit', 'role')
            ->get();
    }

    public function addNewUser(Request $request): Array
    {
        DB::beginTransaction();
        try {
            $user = new User;
            $user->role_id = $request->role_id;
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name;
            $user->last_name = $request->last_name;
            $user->suffix = $request->suffix;
            $user->gender = $request->gender;
            $user->birthday = $request->birthday;
            $user->id_number = $request->id_number;
            $user->office_id = $request->office_id;
            $user->is_active = $request->is_active;
            $user->username = $request->username;
            $user->password = Hash::make($request->password);
            $user->save();
        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();

        $collection = collect($request->except('password'));

        $user_id = Auth::user()->id;
        event(new UserEvent($user_id, json_encode($collection), null, 'create'));

        return [$user];
    }

    public function updateExistingUser(Request $request): Array
    {

        $old_values = User::select('role_id','first_name','middle_name','last_name','suffix', 'gender', 'birthday', 'id_number', 'office_id', 'is_active', 'username')->where('id', $request->id)->get();

        DB::beginTransaction();
        try {
            $user = User::find($request->id);
            $user->role_id = $request->role_id;
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name;
            $user->last_name = $request->last_name;
            $user->suffix = $request->suffix;
            $user->gender = $request->gender;
            $user->birthday = $request->birthday;
            $user->id_number = $request->id_number;
            $user->office_id = $request->office_id;
            $user->is_active = $request->is_active;
            $user->username = $request->username;
            $user->password = Hash::make($request->password);
            $user->save();
        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }

        // $collection = collect($request->except('password', 'office', 'id', 'full_name', 'deleted_at', 'created_at', 'updated_at', 'division_id', 'unit_id', 'sector_id'));

        $request_object = '{
            "role_id":"' . $request->role_id . '",
            "first_name":"' . $request->first_name . '",
            "middle_name":"' . $request->middle_name . '",
            "last_name":"' . $request->last_name . '",
            "suffix":"' . $request->suffix . '",
            "gender":"' . $request->gender . '",
            "birthday":"' . $request->birthday . '",
            "id_number":"' . $request->id_number . '",
            "office_id":"' . $request->office_id . '",
            "is_active":"' . $request->is_active . '",
            "username":"' . $request->username . '"}';

        $user_id = Auth::user()->id;
        event(new UserEvent($user_id,json_decode($request_object),json_decode($old_values[0]), 'update'));

        DB::commit();
        return [$user];
    }

    public function deleteExistingUser (Request $request): Array
    {
        DB::beginTransaction();
        try {
            $user = User::find($request->id);
            $user->delete();
        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }

        $user_id = Auth::user()->id;
        event(new UserEvent($user_id,$user, null, 'delete'));
        DB::commit();
        return [$user];
    }

    public function updateFullname(ChangeFullnamePutRequest $request)
    {
        $old_values = User::select('first_name','middle_name','last_name','suffix')->where('id', $request->id)->get();

        $user = User::findOrFail(Auth::user()->id);
        $user->first_name=$request->first_name;
        $user->middle_name=$request->middle_name;
        $user->last_name=$request->last_name;
        $user->suffix=$request->name_suffix;
        $user->save();
        $response = $user->wasChanged();

        $request_object = '{
            "first_name":"' . $request->first_name . '",
            "middle_name":"' . $request->middle_name . '",
            "last_name":"' . $request->last_name . '",
            "suffix":"' . $request->name_suffix . '"}';

        $user_id = Auth::user()->id;
        event(new AccountEvent($user_id,json_decode($request_object), $old_values[0], 'fullname'));

        return $response;
    }

    public function updateUsername(ChangeUsernamePutRequest $request)
    {
        $old_values = Auth::user()->username;
        
        $user = User::findOrFail(Auth::user()->id);
        $user->username = $request->new_username;
        $user->save();

        $request_object = '{
            "username":"' . $request->new_username . '"}';

        $user_id = Auth::user()->id;
        event(new AccountEvent($user_id,json_decode($request_object),$old_values, 'username'));

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

            $user_id = Auth::user()->id;
            event(new AccountEvent($user_id, 'Password Updated',null, 'password'));

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
