<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\HomeOfficeRequest;
use Illuminate\Http\Request;
use App\Helpers\GraphMailHelper;

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

        $existingActive = HomeOfficeRequest::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'active'])
            ->first();

        if ($existingActive) {
            return back()->withInput()->withErrors([
                'active_loan' => 'Ya tienes una solicitud de Home Office en curso (Estado: ' . ucfirst($existingActive->status) . '). Debes entregar el equipo actual antes de solicitar uno nuevo.'
            ]);
        }

        $ho = HomeOfficeRequest::create([
            'user_id' => auth()->id(),
            'request_type' => $validated['request_type'],
            'scheduled_start_date' => $validated['scheduled_start_date'],
            'scheduled_end_date' => $validated['scheduled_end_date'],
            'policy_accepted' => true,
            'status' => 'pending',
        ]);
        
        $ho->load('user');

        try {
            // Enviar correo al usuario
            $tituloUser = "Solicitud de Home Office Enviada";
            $mensajeUser = "Hola {$ho->user->name}, tu solicitud para llevarte equipo de Home Office del {$ho->scheduled_start_date} al {$ho->scheduled_end_date} ha sido registrada. Pronto el equipo de Sistemas la revisará para su aprobación.";
            $htmlUser = (new \App\Mail\HomeOfficeNotification($ho, $tituloUser, $mensajeUser))->render();
            GraphMailHelper::send($ho->user->email, $tituloUser, $htmlUser);

            // Enviar correo a Sistemas
            $correoSistemas = env('EMAIL_FROM', 'sistemas@ragon.com.mx');
            $tituloIT = "Nueva Solicitud de Home Office: {$ho->user->name}";
            $mensajeIT = "El usuario {$ho->user->name} ha solicitado equipo para Home Office del {$ho->scheduled_start_date} al {$ho->scheduled_end_date}. Favor de revisar la solicitud en el panel de administración.";
            $htmlIT = (new \App\Mail\HomeOfficeNotification($ho, $tituloIT, $mensajeIT))->render();
            GraphMailHelper::send($correoSistemas, $tituloIT, $htmlIT);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando correo de HO: ' . $e->getMessage());
        }

        return redirect()->route('cliente.home-office.index')
            ->with('success', 'Solicitud de Home Office enviada a Sistemas. En espera de aprobación.');
    }
}
