<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\Trip;

Broadcast::channel('trip.{tripId}', function ($user, $tripId) {
    // Anyone can view seat availability, so we return true.
    // For more secure applications, you might want to check
    // if the user has permission to view the trip.
    return true;
});
