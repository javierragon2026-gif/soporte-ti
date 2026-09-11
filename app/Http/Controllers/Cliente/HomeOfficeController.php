<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\HomeOfficeRequest;
use Illuminate\Http\Request;

class HomeOfficeController extends Controller
{
    public function index()
    {
        $requests = HomeOfficeRequest::with('device', 'checkoutAgent', 'checkinAgent')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
            
        return view('cliente.home-office.index', compact('requests'));
    }

    public function create()
    {
        return view('cliente.home-office.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_type' => 'required|in:shared_loan,assigned_gate_pass',
            'scheduled_start_date' => 'required|date',
            'scheduled_end_date' => 'required|date|after_or_equal:scheduled_start_date',
            'policy_accepted' => 'required|accepted',
        ]);

        HomeOfficeRequest::create([
            'user_id' => auth()->id(),
            'request_type' => $validated['request_type'],
            'scheduled_start_date' => $validated['scheduled_start_date'],
            'scheduled_end_date' => $validated['scheduled_end_date'],
            'policy_accepted' => true,
            'status' => 'pending',
        ]);

        return redirect()->route('cliente.home-office.index')
            ->with('success', 'Solicitud de Home Office enviada a Sistemas. En espera de aprobación.');
    }
}

