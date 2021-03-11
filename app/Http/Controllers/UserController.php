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
use Illuminate\Support\Facades\Storage;
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
        return User::with('office')->find(Auth::id());
    }

    public function getAllUsers(): Collection
    {
        abort_if(!in_array(auth()->user()->role_id, [1,2,3]), 403);

        if(auth()->user()->isAdmin()){
            return User::with('office')->get();
        }

        return User::with('office')->get(['id', 'first_name', 'last_name', 'middle_name', 'suffix']);
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

    public function uploadSelfie(Request $request)
    {
        if($request->hasFile('file')) {
            $filename = $request->file->getClientOriginalName();
            if(auth()->user()->avatar){
                Storage::delete('/public/' . auth()->user()->avatar);
            }
            $path = $request->file->storeAs('images', $filename, 'public');
            Auth::user()->update(['avatar' => $path]);
        }
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
}
