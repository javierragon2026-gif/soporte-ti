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

        // Evitar múltiples solicitudes activas/acaparamiento de laptops por el mismo usuario
        $existingActive = HomeOfficeRequest::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'active'])
            ->first();

        if ($existingActive) {
            return back()->withInput()->withErrors([
                'active_loan' => 'Ya tienes una solicitud de Home Office en curso (Estado: ' . ucfirst($existingActive->status) . '). Debes entregar el equipo actual antes de solicitar uno nuevo.'
            ]);
        }

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

