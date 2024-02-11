<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;


use App\Models\ApiRequest;

class CleanUpOldCachedData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $type, $cacheExpiry;
    
    public function __construct($type, $cacheExpiry)
    {
        $this->type = $type;
        $this->cacheExpiry = $cacheExpiry;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $date = Carbon::now()->sub($this->cacheExpiry);
        
        ApiRequest::where('created_at', '<', $date)->where('request_type', $this->type)->delete();
    }
}
