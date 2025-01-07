<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'name' => $user->name,
            'bio' => $user->bio,
            'profile_picture' => $user->profile_picture,
            'profile_picture_url' => $user->profile_picture_url
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|max:1024',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists('profile_picture/' . $user->profile_picture)) {
                Storage::disk('public')->delete('profile_picture/' . $user->profile_picture);
            }

            // Store the new image
            $path = $request->file('profile_picture')->store('profile_picture', 'public');
            $validated['profile_picture'] = basename($path);
        }

        $user->update($validated);

        return response()->json([
            'name' => $user->name,
            'bio' => $user->bio,
            'profile_picture' => $user->profile_picture,
            'profile_picture_url' => $user->profile_picture_url
        ]);
    }
}
