<?php

namespace App\Http\Controllers;

use App\Events\OfficeEvent;
use App\Models\Document;
use Auth;
use DB;

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

        $request_obj = '{
            "id":"' . $office->id . '",
            "name":"' . $request->name . '",
            "address":"' . $request->address . '",
            "office_code":"' . $request->office_code . '",
            "contact_number":"' . $request->contact_number . '",
            "contact_email":"' . $request->contact_email . '"}';

        $user_id = Auth::user()->id;
        event(new OfficeEvent($user_id, json_decode($request_obj),null, 'create'));

        return [$office];
    }

    public function importNewOffice(Request $request): Array
    {
        /*dd($request['office_list']);
        foreach($request['office_data'] as $office_data){
            //dd($office_data['tab']);
            foreach($office_data['content'] as $office){
                //dd($office['Office_Name']);
            }
        }*/


        DB::beginTransaction();
        try {
            foreach($request['office_data'] as $office_data){

                $user_id = Auth::user()->id;
                event(new OfficeEvent($user_id, $office_data['tab'], null, 'import'));

                foreach($office_data['content'] as $offices){
                    $office = new Office;
                    $office->name = $offices["Office_Name"];
                    $office->address = $offices["Address"];
                    $office->office_code = $offices["Office_Code"];
                    $office->contact_number = $offices["Contact_Number"];
                    $office->contact_email = $offices["Email_Address"];
                    $office->save();

                }
            }
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
        $old_values = Office::select('id','name','address','office_code','contact_number','contact_email')->where('id', $request->id)->get();

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

        $request_obj = '{
            "id":"' . $request->id . '",
            "name":"' . $request->name . '",
            "address":"' . $request->address . '",
            "office_code":"' . $request->office_code . '",
            "contact_number":"' . $request->contact_number . '",
            "contact_email":"' . $request->contact_email . '"}';

        $user_id = Auth::user()->id;
        event(new OfficeEvent($user_id, json_decode($old_values[0]), json_decode($request_obj), 'update'));

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


        $user_id = Auth::user()->id;

        event(new OfficeEvent($user_id,$office,null,'delete'));

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
        $stat = [];
        $document =  Document::with('tracking_records')
            ->get()
            ->groupBy('originating_office')
            ->filter(function($val,$key){return is_numeric($key);});

        foreach (Office::all() as $office) {
           $stat[$office->name]['count'] = @optional($document[$office->id])->count() ?? 0;
           $stat[$office->name]['document'] = $document[$office->id] ?? null;
        }

        return $stat;
        // $offices = Office::with('users.tracking_records')->get();
        // foreach ($offices as $office) {
        //     $office['transaction_count'] = collect($this->bulkUserTrackingList($office))->collapse()->count();
        //     $office['documents'] = collect($this->bulkUserTrackingList($office))->collapse()->groupBy('document_id');
        //     unset($office->users);
        // }
        // return $offices;
    }

}
