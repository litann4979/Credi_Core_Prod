<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\GeofenceSettings;
use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    public function index()
    {
        $geofences = GeofenceSettings::query()
            ->latest()
            ->paginate(10);

        return view('admin.geofence.index', compact('geofences'));
    }

    public function create()
    {
        return view('admin.geofence.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        GeofenceSettings::create($data);

        return redirect()
            ->route('admin.geofence.index')
            ->with('success', 'Geofence location created successfully.');
    }

    public function edit(GeofenceSettings $geofence)
    {
        return view('admin.geofence.edit', compact('geofence'));
    }

    public function update(Request $request, GeofenceSettings $geofence)
    {
        $data = $this->validatePayload($request);

        $geofence->update($data);

        return redirect()
            ->route('admin.geofence.index')
            ->with('success', 'Geofence location updated successfully.');
    }

    public function destroy(GeofenceSettings $geofence)
    {
        $geofence->delete();

        return redirect()
            ->route('admin.geofence.index')
            ->with('success', 'Geofence location deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'office_name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['required', 'numeric', 'min:1'],
        ]);
    }
}
