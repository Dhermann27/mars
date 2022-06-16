<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ExposeRoomselection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $year_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($year_id)
    {
        $this->year_id = $year_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::statement('CALL expose_roomselection(' . $this->year_id . ');');
    }
}
