<?php

namespace catchapp\Listeners\App\Listeners;

use catchapp\Events\App\Events\StorySeen;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateStoryStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  StorySeen  $event
     * @return void
     */
    public function handle(StorySeen $event)
    {
        //
    }
}
