<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\HomeOfficeRequest;
use Illuminate\Http\Request;

class HomeOfficeController extends Controller
{
    public function index()
    {
        $requests = HomeOfficeRequest::with('user', 'device')
            ->latest()
            ->paginate(20);
            
        return view('admin.home-office.index', compact('requests'));
    }

    public function show(HomeOfficeRequest $homeOffice)
    {
        $homeOffice->load('user', 'device', 'checkoutAgent', 'checkinAgent');
        $availableDevices = Device::where('status', 'available')->where('type', 'shared')->get();
        return view('admin.home-office.show', compact('homeOffice', 'availableDevices'));
    }

    public function approve(Request $request, HomeOfficeRequest $homeOffice)
    {
        if ($homeOffice->request_type === 'shared_loan') {
            $request->validate(['device_id' => 'required|exists:devices,id']);
            $device = Device::find($request->device_id);
            $device->update(['status' => 'loaned']);
            
            $homeOffice->update([
                'device_id' => $device->id,
                'status' => 'active',
                'checkout_at' => now(),
                'checkout_by' => auth()->id(),
                'accessories' => ['Cargador'],
                'checkout_notes' => 'Entrega rápida de laptop con cargador.',
            ]);
        } else {
            $homeOffice->update([
                'status' => 'active',
                'checkout_at' => now(),
                'checkout_by' => auth()->id(),
                'checkout_notes' => 'Pase de salida rápido autorizado.',
            ]);
        }

        return back()->with('success', 'Equipo asignado y salida registrada en un solo paso.');
    }

    public function checkout(Request $request, HomeOfficeRequest $homeOffice)
    {
        $request->validate([
            'checkout_notes' => 'nullable|string',
            'accessories' => 'nullable|array'
        ]);

        $homeOffice->update([
            'status' => 'active',
            'checkout_at' => now(),
            'checkout_by' => auth()->id(),
            'checkout_notes' => $request->checkout_notes,
            'accessories' => $request->accessories
        ]);

        return back()->with('success', 'Salida / Préstamo registrado correctamente.');
    }

    public function checkin(Request $request, HomeOfficeRequest $homeOffice)
    {
        $request->validate([
            'checkin_notes' => 'nullable|string'
        ]);

        $homeOffice->update([
            'status' => 'returned',
            'checkin_at' => now(),
            'checkin_by' => auth()->id(),
            'checkin_notes' => $request->checkin_notes
        ]);

        if ($homeOffice->device) {
            $homeOffice->device->update(['status' => 'available']);
        }

        return back()->with('success', 'Ingreso / Devolución registrada correctamente.');
    }

    public function edit(HomeOfficeRequest $homeOffice)
    {
        $homeOffice->load('user', 'device');
        $availableDevices = Device::where('status', 'available')->where('type', 'shared')->get();
        // Incluir también el equipo actual si existe para que pueda mantenerse
        if ($homeOffice->device_id) {
            $availableDevices->push($homeOffice->device);
        }
        return view('admin.home-office.edit', compact('homeOffice', 'availableDevices'));
    }

    public function update(Request $request, HomeOfficeRequest $homeOffice)
    {
        $validated = $request->validate([
            'device_id' => 'nullable|exists:devices,id',
            'status' => 'required|in:pending,approved,active,returned,cancelled',
            'scheduled_start_date' => 'required|date',
            'scheduled_end_date' => 'required|date'
        ]);

        // Si cambia el dispositivo y estaba "loaned", liberar el viejo y ocupar el nuevo
        if ($homeOffice->device_id != $request->device_id) {
            if ($homeOffice->device) {
                $homeOffice->device->update(['status' => 'available']);
            }
            if ($request->device_id) {
                Device::find($request->device_id)->update(['status' => 'loaned']);
            }
        }

        $homeOffice->update($validated);
        return redirect()->route('admin.home-office.show', $homeOffice)->with('success', 'Solicitud actualizada correctamente.');
    }

    public function destroy(HomeOfficeRequest $homeOffice)
    {
        if ($homeOffice->device) {
            $homeOffice->device->update(['status' => 'available']);
        }
        $homeOffice->delete();
        return redirect()->route('admin.home-office.index')->with('success', 'Solicitud y registro histórico eliminado.');
    }
}

