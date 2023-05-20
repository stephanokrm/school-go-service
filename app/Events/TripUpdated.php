<?php

namespace App\Events;

use App\Models\Trip;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 *
 */
class TripUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string|null
     */
    private ?string $origin = null;

    /**
     * @param Trip $trip
     */
    public function __construct(private readonly Trip $trip)
    {
    }

    /**
     * @param string $origin
     */
    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    /**
     * @return string|null
     */
    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    /**
     * @return Trip
     */
    public function getTrip(): Trip
    {
        return $this->trip;
    }
}
