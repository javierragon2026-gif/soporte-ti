<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::latest()->paginate(15);
        return view('admin.devices.index', compact('devices'));
    }

    public function create()
    {
        return view('admin.devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:shared,assigned',
            'serial_number' => 'nullable|string|max:255',
            'status' => 'required|in:available,loaned,maintenance,retired',
            'notes' => 'nullable|string'
        ]);

        Device::create($validated);
        return redirect()->route('admin.devices.index')->with('success', 'Equipo registrado.');
    }

    public function edit(Device $inventario_equipo)
    {
        return view('admin.devices.edit', ['device' => $inventario_equipo]);
    }

    public function update(Request $request, Device $inventario_equipo)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:shared,assigned',
            'serial_number' => 'nullable|string|max:255',
            'status' => 'required|in:available,loaned,maintenance,retired',
            'notes' => 'nullable|string'
        ]);

        $inventario_equipo->update($validated);
        return redirect()->route('admin.devices.index')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Device $inventario_equipo)
    {
        $inventario_equipo->delete();
        return redirect()->route('admin.devices.index')->with('success', 'Equipo eliminado del inventario.');
    }
}

