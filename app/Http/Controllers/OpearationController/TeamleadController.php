<?php

namespace App\Http\Controllers\OpearationController;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TeamleadController extends Controller
{

       public function indexTeamlead(){
       $teamleads = User::where('designation', 'team_lead')
                    ->where('created_by', auth()->id())
                    ->withTrashed() // if you want to see both active and inactive
                    ->get();
$totalTeamleads = $teamleads->count();
$activeTeamleads = $teamleads->whereNull('deleted_at')->count();

       return view('Opearation.teamleads.index', compact('teamleads', 'totalTeamleads', 'activeTeamleads'));

    }
   public function storeTeamlead(Request $request)
{
  try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'designation' => 'required|in:team_lead',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->designation = 'team_lead';
        $user->address = $validated['address'] ?? null;
        $user->password = bcrypt('defaultpassword');
        $user->created_by = auth()->id();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('profile_photos', 'public');
            $user->profile_photo = $photo;
        }

        $user->save();

        return response()->json([
            'message' => 'Team Lead added successfully!',
            'teamlead' => $user // Return the created team lead
        ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to add Team Lead: ' . $e->getMessage()
        ], 500);
    };
}



public function activate($id)
{
    $user = User::withTrashed()->findOrFail($id);

    if ($user->trashed()) {
        $user->restore(); // Bring back soft-deleted user
    }

    return redirect()->back()->with('success', 'Team Lead activated successfully.');
}

public function deactivate($id)
{
    $user = User::findOrFail($id);

    $user->delete(); // Soft delete (make inactive)

    return redirect()->back()->with('success', 'Team Lead deactivated successfully.');
}

public function edit($id)
{
    $teamlead = User::where('designation', 'team_lead')
                    ->where('created_by', auth()->id())
                    ->findOrFail($id);

    return view('operations.teamleads.edit', compact('teamlead'));
}


public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string',
        'photo' => 'nullable|image|max:2048',
    ]);

    $user = User::findOrFail($id);

    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->address = $request->address ?? null;

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('profile_photos', 'public');
        $user->profile_photo = $path;
    }

    $user->save();

    return response()->json([
        'message' => 'Team Lead updated successfully!',
        'updated_teamlead' => $user
    ]);
}




}
