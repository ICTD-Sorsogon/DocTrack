<?php

namespace App\Http\Controllers;

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
