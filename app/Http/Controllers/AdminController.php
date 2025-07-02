<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Operator;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Bus;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_operators' => User::where('role', 'operator')->count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Payment::where('payment_status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('payment_status', 'pending')->count(),
            'active_trips' => Trip::where('departure_datetime', '>', now())->count(),
            'total_buses' => Bus::count(),
            'recent_bookings' => Booking::with(['user', 'trip.route.fromCity', 'trip.route.toCity'])
                                      ->latest()
                                      ->take(10)
                                      ->get(),
        ];

        // Monthly revenue chart data
        $monthlyRevenue = Payment::where('payment_status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('admin.dashboard', compact('stats', 'monthlyRevenue'));
    }

    /**
     * Users Management
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->withCount(['bookings', 'payments'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user->load(['bookings.trip.route.fromCity', 'bookings.trip.route.toCity', 'payments']);
        
        $userStats = [
            'total_bookings' => $user->bookings->count(),
            'total_spent' => $user->payments->where('payment_status', 'completed')->sum('amount'),
            'cancelled_bookings' => $user->bookings->where('status', 'cancelled')->count(),
            'member_since' => $user->created_at->diffForHumans(),
        ];

        return view('admin.users.show', compact('user', 'userStats'));
    }

    /**
     * Edit user
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,operator,user',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'role', 'status']));

        return redirect()->route('admin.users.show', $user)
                        ->with('success', 'User updated successfully.');
    }

    /**
     * Create new user
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:30|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,operator,admin',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User created successfully.');
    }

    /**
     * Operators Management
     */
    public function operators(Request $request)
    {
        $query = User::where('role', 'operator');

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $operators = $query->with(['operator.buses'])
                          ->withCount(['bookings'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);

        return view('admin.operators.index', compact('operators'));
    }

    /**
     * Create new operator
     */
    public function createOperator()
    {
        return view('admin.operators.create');
    }

    /**
     * Store new operator
     */
    public function storeOperator(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'license_number' => 'required|string|max:100|unique:operators,license_number',
            'address' => 'required|string|max:500',
            'contact_person' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'operator',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Create operator profile
            Operator::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'license_number' => $request->license_number,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
                'status' => 'active',
            ]);
        });

        return redirect()->route('admin.operators.index')
                        ->with('success', 'Operator created successfully.');
    }

    /**
     * Show operator details
     */
    public function showOperator(User $operator)
    {
        if ($operator->role !== 'operator') {
            abort(404);
        }

        $operator->load(['operator.buses.trips', 'bookings.trip']);
        
        $operatorStats = [
            'total_buses' => $operator->operator->buses->count(),
            'total_trips' => $operator->operator->buses->sum(function($bus) {
                return $bus->trips->count();
            }),
            'total_bookings' => $operator->bookings->count(),
            'total_revenue' => $operator->bookings->sum('total_amount'),
            'active_trips' => $operator->operator->buses->sum(function($bus) {
                return $bus->trips->where('departure_datetime', '>', now())->count();
            }),
        ];

        return view('admin.operators.show', compact('operator', 'operatorStats'));
    }

    /**
     * Edit operator
     */
    public function editOperator(User $operator)
    {
        if ($operator->role !== 'operator') {
            abort(404);
        }

        $operator->load('operator');
        return view('admin.operators.edit', compact('operator'));
    }

    /**
     * Update operator
     */
    public function updateOperator(Request $request, User $operator)
    {
        if ($operator->role !== 'operator') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $operator->id,
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'company_name' => 'required|string|max:255',
            'license_number' => 'required|string|max:100|unique:operators,license_number,' . $operator->operator->id,
            'address' => 'required|string|max:500',
            'contact_person' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $operator) {
            // Update user
            $operator->update($request->only(['name', 'email', 'phone', 'status']));

            // Update operator profile
            $operator->operator->update($request->only([
                'company_name', 'license_number', 'address', 'contact_person'
            ]));
        });

        return redirect()->route('admin.operators.show', $operator)
                        ->with('success', 'Operator updated successfully.');
    }

    /**
     * Bookings Management
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'trip.route.fromCity', 'trip.route.toCity', 'trip.bus']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by booking reference or passenger name
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('booking_reference', 'like', '%' . $request->search . '%')
                  ->orWhere('passenger_name', 'like', '%' . $request->search . '%');
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Payments Management
     */
    public function payments(Request $request)
    {
        $query = Payment::with(['booking.user', 'booking.trip.route.fromCity', 'booking.trip.route.toCity']);

        // Filter by payment status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        $paymentStats = [
            'total_amount' => Payment::where('payment_status', 'completed')->sum('amount'),
            'pending_amount' => Payment::where('payment_status', 'pending')->sum('amount'),
            'failed_amount' => Payment::where('payment_status', 'failed')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'paymentStats'));
    }
}
