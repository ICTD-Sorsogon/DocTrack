<?php

namespace App\Http\Controllers;

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
        return [$office];
    }

    public function updateExistingOffice(Request $request): Array
    {
        DB::beginTransaction();
        try {
            /*dd('sample:',
              $request->id,
              $request->name,
              $request->address,
              $request->office_code,
              $request->contact_number,
              $request->contact_email
            );*/
            $office = Office::find($request->id);
            //dd('sample:', $office);
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
        //dd('record_id', $request->id);
        DB::beginTransaction();
        try {
            //Office::find($request->id)-delete();
            $office = Office::find($request->id);
            $office->delete();

            /*dd('sample:',
              $request->id,
              $request->name,
              $request->address,
              $request->office_code,
              $request->contact_number,
              $request->contact_email
            );*/
            //$office = Office::find($request->id);
            //dd('sample:', $office);
            /*$office->name = $request->name;
            $office->address = $request->address;
            $office->office_code = $request->office_code;
            $office->contact_number = $request->contact_number;
            $office->contact_email = $request->contact_email;
            $office->save();*/
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

}
