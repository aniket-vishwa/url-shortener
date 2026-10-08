<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function create()
    {
        return view('invitations.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            $data = $request->validate([
                'company_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
            ]);

            $company = Company::create(['name' => $data['company_name']]);
            $role = 'Admin';
        } else {
            $data = $request->validate([
                'email' => 'required|email|unique:users,email',
                'role' => 'required|in:Admin,Member',
            ]);

            $company = $user->company;
            $role = $data['role'];
        }

        $invitation = Invitation::create([
            'email' => $data['email'],
            'role' => $role,
            'company_id' => $company->id,
            'invited_by' => $user->id,
            'token' => Str::random(40),
        ]);

        $link = route('invitations.accept', $invitation->token);

        return back()->with('invite_link', $link);
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        return view('invitations.accept', compact('invitation'));
    }

    public function complete(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            'password' => $data['password'],
            'company_id' => $invitation->company_id,
            'role' => $invitation->role,
            'email_verified_at' => now(),
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}