<?php

namespace catchapp\Events\App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
class StorySeen implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $story_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($story_id)
    {
        $this->story_id = $story_id +1;
        //
    }
    public function broadcastAs()
    {
        return $this->story_id;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('storyStatus');
    }


}
