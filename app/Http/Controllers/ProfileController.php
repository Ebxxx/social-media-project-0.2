<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return $request->user();
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|max:1024', // max 1MB
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::delete('public/profile_picture/' . $user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_picture', 'public');
            $validated['profile_picture'] = basename($path); // Store only the filename in the DB
        }

        $user->update($validated);

        // Include profile_picture in the response
        return response()->json([
            'name' => $user->name,
            'bio' => $user->bio,
            'profile_picture' => $validated['profile_picture'] ?? $user->profile_picture,
        ]);
    }

}
