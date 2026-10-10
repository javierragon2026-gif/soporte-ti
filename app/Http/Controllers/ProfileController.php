<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $user->update([
            'name'   => $request->get('name'),
            'locale' => $request->get('locale'),
            'email'  => $request->get('email', $user->email),
        ]);

        $user->settings()->updateOrCreate([], $request->only('tickets_signature') + $this->notificationSettings($request));

        return back();
    }

    public function password(Request $request)
    {
        $user = auth()->user();

        $messages = [
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'old.old_password' => 'La contraseña actual no es correcta.'
        ];

        // Validar contraseña vieja (si la pide)
        if ($request->has('old')) {
            $this->validate($request, [
                'old'      => 'old_password:'.$user->password,
                'password' => 'required|confirmed|min:6',
            ], $messages);
        } else {
            $this->validate($request, [
                'password' => 'required|confirmed|min:6',
            ], $messages);
        }

        $user->update(['password' => bcrypt($request->get('password'))]);

        return back();
    }

    public function forcePasswordForm()
    {
        return view('profile.force-password');
    }

    public function forcePasswordUpdate(Request $request)
    {
        $messages = [
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];

        $this->validate($request, [
            'password' => 'required|confirmed|min:6',
        ], $messages);

        $user = auth()->user();
        $user->update(['password' => bcrypt($request->get('password'))]);

        return redirect()->route('dashboard')->with('success', '¡Contraseña actualizada correctamente!');
    }

    private function notificationSettings(Request $request)
    {
        return [
            'new_ticket_notification'           => $request->filled('new_ticket_notification'),
            'ticket_assigned_notification'      => $request->filled('ticket_assigned_notification'),
            'ticket_updated_notification'       => $request->filled('ticket_updated_notification'),
            'new_lead_notification'             => $request->filled('new_lead_notification'),
            'lead_assigned_notification'        => $request->filled('lead_assigned_notification'),
            'daily_tasks_notification'          => $request->filled('daily_tasks_notification'),
            'mention_notification'              => $request->filled('mention_notification'),
            'escalated_ticket_notification'     => $request->filled('escalated_ticket_notification'),
        ];
    }
}
