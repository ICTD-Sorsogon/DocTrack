<?php

namespace App\Http\Controllers;

use App\Events\OfficeCreateEvent;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth as FacadesAuth;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function getOfficeList(): Collection
    {
        return Office::get();
    }

    public function addNewOffice(Request $request): Array
    {
        DB::beginTransaction();
        try {
            $office = new Office;
            $office->name = $request->name;
            $office->address = $request->address;
            $office->office_code = $request->office_code;
            $office->contact_number = $request->contact_number;
            $office->contact_email = $request->contact_email;
            $office->save();
        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();

        $user_id = Auth::user()->id;
        event(new OfficeCreateEvent($user_id, $request));

        return [$office];
    }

    public function updateExistingOffice(Request $request): Array
    {
        DB::beginTransaction();
        try {
            $office = Office::find($request->id);
            $office->name = $request->name;
            $office->address = $request->address;
            $office->office_code = $request->office_code;
            $office->contact_number = $request->contact_number;
            $office->contact_email = $request->contact_email;
            $office->save();
        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();
        return [$office];
    }

    public function deleteOffice(Request $request): Array
    {
        DB::beginTransaction();
        try {
            $office = Office::find($request->id);
            $office->delete();
        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();
        return [$office];
    }

    public function bulkUserTrackingList(Office $office)
    {
        $collection = [];
        foreach ($office->users as $users) {
            array_push($collection, $users->tracking_records);
        }
        return $collection;
    }

    public function getTrackingList()
    {
        $offices = Office::with('users.tracking_records')->get();
        foreach ($offices as $office) {
            $office['tracking_records'] = collect($this->bulkUserTrackingList($office))->collapse();
            unset($office->users);
        }
        return $offices;
    }

}
