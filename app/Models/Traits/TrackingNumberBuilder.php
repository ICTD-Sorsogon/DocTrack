<?php

namespace App\Models\Traits;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

trait TrackingNumberBuilder
{
    public function buildTrackingNumber($model)
    {
		$tracking = ($model->is_external ? 'E' : 'I') .
			'-'.
            auth()->user()->office->office_code.
            '-'.
            date('YmdH').
            '-'.
            substr(str_shuffle("0123456789"), 0, 5).
            '-'.
            $model->attachment_page_count;
        return $tracking;
    }
}