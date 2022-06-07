<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\BoardNode;

class NodeTransform implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

		public $node;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BoardNode $node)
    {
			$this->node = $node;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
				return new Channel('nodes.transform.' . $this->node->type);
    }
}
