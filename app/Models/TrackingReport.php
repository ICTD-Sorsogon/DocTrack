<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingReport extends Model
{
    use HasFactory;

    protected $guarded = ['updated_at'];

    protected $primaryKey = 'office_id';

    protected $casts = ['speeds' => 'collection'];

    protected $appends = ['Efficiency', 'average', 'fastest', 'slowest'];

    public function getAverageAttribute()
    {
        return $this->speeds ? CarbonInterval::seconds($this->speeds->average())->cascade()->forHumans(['short' => true, 'parts' => 1]) : 'NOT AVALABLE';
    }

    public function getFastestAttribute()
    {
        return $this->speeds ? CarbonInterval::seconds($this->speeds->min())->cascade()->forHumans(['short' => true, 'parts' => 1]) : 'NOT AVALABLE';
    }

    public function getSlowestAttribute()
    {
        return  $this->speeds ? CarbonInterval::seconds($this->speeds->max())->cascade()->forHumans(['short' => true, 'parts' => 1]) : 'NOT AVALABLE';
    }

    public function getEfficiencyAttribute()
    {
        extract($this->only('transactions', 'delayed'));
        return $transactions ? number_format(($transactions - $delayed) / $transactions * 100, 2) : 'NOT AVAILABLE';
    }
}
