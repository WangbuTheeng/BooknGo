<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SeatLayoutController extends Controller
{
    /**
     * Show the seat layout configuration form
     */
    public function configure(Bus $bus)
    {
        $this->authorize('update', $bus);

        $bus->load('seats');

        return view('buses.layout.configure', compact('bus'));
    }

    /**
     * Save the seat layout configuration
     */
    public function store(Request $request, Bus $bus)
    {
        $this->authorize('update', $bus);

        $validator = Validator::make($request->all(), [
            'layout_pattern' => 'required|in:2x2,2x1,1x1,custom',
            'total_seats' => 'required|integer|min:1|max:100',
            'rows_count' => 'nullable|integer|min:1|max:20',
            'seats_per_row' => 'nullable|integer|min:1|max:6',
            'back_row_seats' => 'nullable|integer|min:0|max:6',
            'has_driver_side_seat' => 'boolean',
            'driver_side_seat_usable' => 'boolean',
            'has_conductor_area' => 'boolean',
            'bus_category' => 'required|in:standard,deluxe,sleeper,semi_sleeper,vip',
            'layout_metadata' => 'nullable|array',
            'special_seats' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Update bus layout configuration
            $bus->update([
                'layout_pattern' => $request->layout_pattern,
                'total_seats' => $request->total_seats,
                'rows_count' => $request->rows_count,
                'seats_per_row' => $request->seats_per_row,
                'back_row_seats' => $request->back_row_seats,
                'has_driver_side_seat' => $request->boolean('has_driver_side_seat'),
                'driver_side_seat_usable' => $request->boolean('driver_side_seat_usable'),
                'has_conductor_area' => $request->boolean('has_conductor_area'),
                'bus_category' => $request->bus_category,
                'layout_metadata' => $request->layout_metadata,
            ]);

            // Create seats based on layout configuration
            $layoutData = [
                'special_seats' => $request->special_seats ?? [],
                'custom_seats' => $request->custom_seats ?? [],
            ];

            $bus->createSeatsFromLayout($layoutData);

            DB::commit();

            return redirect()->route('buses.layout.preview', $bus)
                           ->with('success', 'Seat layout configured successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to configure seat layout: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Preview the configured seat layout
     */
    public function preview(Bus $bus)
    {
        $this->authorize('view', $bus);

        $bus->load(['seats' => function($query) {
            $query->orderBy('row_number')->orderBy('column_number');
        }]);

        $layoutSummary = $bus->getLayoutSummary();

        return view('buses.layout.preview', compact('bus', 'layoutSummary'));
    }

    /**
     * Get layout preview data (AJAX)
     */
    public function getPreview(Request $request, Bus $bus)
    {
        $this->authorize('view', $bus);

        $layoutPattern = $request->input('layout_pattern', '2x2');
        $totalSeats = $request->input('total_seats', 40);
        $rowsCount = $request->input('rows_count');
        $backRowSeats = $request->input('back_row_seats', 0);

        // Generate preview data without saving
        $previewData = $this->generateLayoutPreview($layoutPattern, $totalSeats, $rowsCount, $backRowSeats);

        return response()->json($previewData);
    }

    /**
     * Reset seat layout to default
     */
    public function reset(Bus $bus)
    {
        $this->authorize('update', $bus);

        try {
            DB::beginTransaction();

            // Clear existing seats
            $bus->seats()->delete();

            // Reset layout configuration
            $bus->update([
                'layout_pattern' => '2x2',
                'rows_count' => null,
                'seats_per_row' => null,
                'back_row_seats' => null,
                'has_driver_side_seat' => false,
                'driver_side_seat_usable' => true,
                'has_conductor_area' => false,
                'bus_category' => 'standard',
                'layout_metadata' => null,
                'layout_configured' => false,
            ]);

            // Create default seats
            $bus->createSeats();

            DB::commit();

            return redirect()->route('buses.seats.index', $bus)
                           ->with('success', 'Seat layout reset to default successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reset seat layout: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate layout preview data
     */
    private function generateLayoutPreview($pattern, $totalSeats, $rowsCount = null, $backRowSeats = 0)
    {
        $seats = [];
        $seatNumber = 1;

        switch ($pattern) {
            case '2x2':
                $rowsCount = $rowsCount ?? ceil($totalSeats / 4);
                $seatsPerRow = 4;
                break;
            case '2x1':
                $rowsCount = $rowsCount ?? ceil($totalSeats / 3);
                $seatsPerRow = 3;
                break;
            case '1x1':
                $rowsCount = $rowsCount ?? ceil($totalSeats / 2);
                $seatsPerRow = 2;
                break;
            default:
                $rowsCount = $rowsCount ?? ceil($totalSeats / 4);
                $seatsPerRow = 4;
        }

        // Generate main seats
        for ($row = 1; $row <= $rowsCount && $seatNumber <= $totalSeats; $row++) {
            for ($col = 1; $col <= $seatsPerRow && $seatNumber <= $totalSeats; $col++) {
                $side = $this->getSideForPattern($pattern, $col);
                $seats[] = [
                    'seat_number' => $seatNumber,
                    'row_number' => $row,
                    'column_number' => $col,
                    'side' => $side,
                    'seat_type' => 'passenger',
                ];
                $seatNumber++;
            }
        }

        // Add back row seats
        if ($backRowSeats > 0) {
            $backRow = $rowsCount + 1;
            for ($i = 1; $i <= $backRowSeats && $seatNumber <= $totalSeats; $i++) {
                $seats[] = [
                    'seat_number' => $seatNumber,
                    'row_number' => $backRow,
                    'column_number' => $i,
                    'side' => 'center',
                    'seat_type' => 'passenger',
                ];
                $seatNumber++;
            }
        }

        return [
            'seats' => $seats,
            'rows_count' => $rowsCount + ($backRowSeats > 0 ? 1 : 0),
            'pattern' => $pattern,
            'total_seats' => count($seats),
        ];
    }

    /**
     * Get seat side based on layout pattern and column
     */
    private function getSideForPattern($pattern, $column)
    {
        switch ($pattern) {
            case '2x2':
                return $column <= 2 ? 'left' : 'right';
            case '2x1':
                return $column <= 2 ? 'left' : 'right';
            case '1x1':
                return $column === 1 ? 'left' : 'right';
            default:
                return $column <= 2 ? 'left' : 'right';
        }
    }
}
