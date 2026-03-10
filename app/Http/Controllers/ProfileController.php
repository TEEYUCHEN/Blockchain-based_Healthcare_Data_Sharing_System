<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function view()
    {
        $user = Auth::user();

        $profileUrl = null;

        if ($user->profile_pic) {
            $profileUrl = Storage::disk('s3')->temporaryUrl(
                $user->profile_pic,
                now()->addMinutes(60)
            );
        }

        return view('profile.view', compact('user', 'profileUrl'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_pic' => 'nullable|image|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if ($request->hasFile('profile_pic')) {

            $path = 'profile_pic/' . $user->id . '/avatar.jpg';

            Storage::disk('s3')->put($path, file_get_contents($request->file('profile_pic')));

            $user->profile_pic = $path;
        }

        $user->save();

        return redirect()->route('profile.view')->with('success', 'Profile updated successfully.');
    }
}