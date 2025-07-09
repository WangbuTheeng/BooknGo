<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'registration_number',
        'bus_number',
        'name',
        'type',
        'total_seats',
        'layout_config',
        'features',
        'layout_pattern',
        'rows_count',
        'seats_per_row',
        'back_row_seats',
        'has_driver_side_seat',
        'driver_side_seat_usable',
        'has_conductor_area',
        'bus_category',
        'layout_metadata',
        'layout_configured',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'features' => 'array',
        'layout_metadata' => 'array',
        'has_driver_side_seat' => 'boolean',
        'driver_side_seat_usable' => 'boolean',
        'has_conductor_area' => 'boolean',
        'layout_configured' => 'boolean',
    ];

    /**
     * Get the operator that owns this bus
     */
    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Get all seats in this bus
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Get all trips for this bus
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Get available seats for a specific trip
     */
    public function getAvailableSeats($tripId)
    {
        $bookedSeatIds = BookingSeat::whereHas('booking', function ($query) use ($tripId) {
            $query->where('trip_id', $tripId)
                  ->where('status', 'booked');
        })->pluck('seat_id');

        return $this->seats()->whereNotIn('id', $bookedSeatIds)->get();
    }

    /**
     *  Boot model to attach event listeners
     */
    protected static function booted()
    {
        static::created(function ($bus) {
            $bus->createSeats();
        });
    }

    /**
     * Create seats for the bus based on total_seats (legacy method)
     */
    public function createSeats()
    {
        // Only create basic seats if layout is not configured
        if (!$this->layout_configured) {
            $seats = [];
            for ($i = 1; $i <= $this->total_seats; $i++) {
                $seats[] = [
                    'bus_id' => $this->id,
                    'seat_number' => $i,
                    'position' => ($i % 4 === 1 || $i % 4 === 2) ? 'left' : 'right',
                    'seat_type' => 'passenger',
                    'row_number' => ceil($i / 4),
                    'column_number' => (($i - 1) % 4) + 1,
                    'side' => ($i % 4 === 1 || $i % 4 === 2) ? 'left' : 'right',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Seat::insert($seats);
        }
    }

    /**
     * Create seats based on layout configuration
     */
    public function createSeatsFromLayout($layoutData)
    {
        // Clear existing seats
        $this->seats()->delete();

        $seats = [];
        $seatNumber = 1;

        // Create seats based on layout pattern
        switch ($this->layout_pattern) {
            case '2x2':
                $seats = $this->create2x2Layout($layoutData, $seatNumber);
                break;
            case '2x1':
                $seats = $this->create2x1Layout($layoutData, $seatNumber);
                break;
            case '1x1':
                $seats = $this->create1x1Layout($layoutData, $seatNumber);
                break;
            case 'custom':
                $seats = $this->createCustomLayout($layoutData, $seatNumber);
                break;
        }

        if (!empty($seats)) {
            Seat::insert($seats);
            $this->update(['layout_configured' => true]);
        }

        return $seats;
    }

    /**
     * Create 2x2 layout (standard bus layout)
     */
    private function create2x2Layout($layoutData, &$seatNumber)
    {
        $seats = [];
        $rowCount = $this->rows_count ?? ceil($this->total_seats / 4);

        for ($row = 1; $row <= $rowCount; $row++) {
            // Left side seats (2 seats)
            for ($col = 1; $col <= 2; $col++) {
                if ($seatNumber <= $this->total_seats) {
                    $seats[] = $this->createSeatData($seatNumber, $row, $col, 'left', $layoutData);
                    $seatNumber++;
                }
            }

            // Right side seats (2 seats)
            for ($col = 3; $col <= 4; $col++) {
                if ($seatNumber <= $this->total_seats) {
                    $seats[] = $this->createSeatData($seatNumber, $row, $col, 'right', $layoutData);
                    $seatNumber++;
                }
            }
        }

        // Add back row seats if specified
        if ($this->back_row_seats && $this->back_row_seats > 0) {
            $backRow = $rowCount + 1;
            for ($i = 1; $i <= $this->back_row_seats; $i++) {
                if ($seatNumber <= $this->total_seats) {
                    $seats[] = $this->createSeatData($seatNumber, $backRow, $i, 'center', $layoutData);
                    $seatNumber++;
                }
            }
        }

        return $seats;
    }

    /**
     * Create 2x1 layout (deluxe/night bus layout)
     */
    private function create2x1Layout($layoutData, &$seatNumber)
    {
        $seats = [];
        $rowCount = $this->rows_count ?? ceil($this->total_seats / 3);

        for ($row = 1; $row <= $rowCount; $row++) {
            // Left side seats (2 seats)
            for ($col = 1; $col <= 2; $col++) {
                if ($seatNumber <= $this->total_seats) {
                    $seats[] = $this->createSeatData($seatNumber, $row, $col, 'left', $layoutData);
                    $seatNumber++;
                }
            }

            // Right side seat (1 seat)
            if ($seatNumber <= $this->total_seats) {
                $seats[] = $this->createSeatData($seatNumber, $row, 3, 'right', $layoutData);
                $seatNumber++;
            }
        }

        return $seats;
    }

    /**
     * Create 1x1 layout (VIP/sleeper layout)
     */
    private function create1x1Layout($layoutData, &$seatNumber)
    {
        $seats = [];
        $rowCount = $this->rows_count ?? ceil($this->total_seats / 2);

        for ($row = 1; $row <= $rowCount; $row++) {
            // Left side seat
            if ($seatNumber <= $this->total_seats) {
                $seats[] = $this->createSeatData($seatNumber, $row, 1, 'left', $layoutData);
                $seatNumber++;
            }

            // Right side seat
            if ($seatNumber <= $this->total_seats) {
                $seats[] = $this->createSeatData($seatNumber, $row, 2, 'right', $layoutData);
                $seatNumber++;
            }
        }

        return $seats;
    }

    /**
     * Create custom layout based on provided configuration
     */
    private function createCustomLayout($layoutData, &$seatNumber)
    {
        $seats = [];

        if (isset($layoutData['custom_seats']) && is_array($layoutData['custom_seats'])) {
            foreach ($layoutData['custom_seats'] as $seatConfig) {
                $seats[] = [
                    'bus_id' => $this->id,
                    'seat_number' => $seatConfig['seat_number'] ?? $seatNumber,
                    'seat_type' => $seatConfig['seat_type'] ?? 'passenger',
                    'row_number' => $seatConfig['row_number'] ?? null,
                    'column_number' => $seatConfig['column_number'] ?? null,
                    'side' => $seatConfig['side'] ?? null,
                    'position' => $seatConfig['position'] ?? null,
                    'is_available_for_booking' => $seatConfig['is_available_for_booking'] ?? true,
                    'price_multiplier' => $seatConfig['price_multiplier'] ?? 1.00,
                    'properties' => isset($seatConfig['properties']) ? json_encode($seatConfig['properties']) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $seatNumber++;
            }
        }

        return $seats;
    }

    /**
     * Create seat data array
     */
    private function createSeatData($seatNumber, $row, $col, $side, $layoutData)
    {
        $seatType = 'passenger';
        $isAvailable = true;
        $priceMultiplier = 1.00;

        // Check for special seat configurations
        if (isset($layoutData['special_seats'])) {
            $seatKey = "{$row}-{$col}";
            if (isset($layoutData['special_seats'][$seatKey])) {
                $specialConfig = $layoutData['special_seats'][$seatKey];
                $seatType = $specialConfig['type'] ?? 'passenger';
                $isAvailable = $specialConfig['available'] ?? true;
                $priceMultiplier = $specialConfig['price_multiplier'] ?? 1.00;
            }
        }

        return [
            'bus_id' => $this->id,
            'seat_number' => (string) $seatNumber,
            'seat_type' => $seatType,
            'row_number' => $row,
            'column_number' => $col,
            'side' => $side,
            'position' => $side,
            'is_available_for_booking' => $isAvailable,
            'price_multiplier' => $priceMultiplier,
            'properties' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Get layout configuration summary
     */
    public function getLayoutSummary()
    {
        return [
            'pattern' => $this->layout_pattern,
            'total_seats' => $this->total_seats,
            'rows_count' => $this->rows_count,
            'seats_per_row' => $this->seats_per_row,
            'back_row_seats' => $this->back_row_seats,
            'has_driver_side_seat' => $this->has_driver_side_seat,
            'driver_side_seat_usable' => $this->driver_side_seat_usable,
            'has_conductor_area' => $this->has_conductor_area,
            'bus_category' => $this->bus_category,
            'layout_configured' => $this->layout_configured,
        ];
    }
}
