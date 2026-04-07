<?php

namespace App\Http\Controllers\HrController;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HrHolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::withTrashed()->orderBy('date', 'asc')->get();
        return view('hr.holidays.index', compact('holidays'));
    }

    public function edit($id)
{
    $editHoliday = Holiday::withTrashed()->findOrFail($id);
    $holidays = Holiday::withTrashed()->orderBy('date', 'asc')->get();

    return view('hr.holidays.index', compact('holidays', 'editHoliday'));
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        Holiday::create($request->only('name', 'date'));

        return redirect()->route('hr.holidays.index')->with('success', 'Holiday added successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $holiday = Holiday::withTrashed()->findOrFail($id);
        $holiday->update($request->only('name', 'date'));

        return redirect()->route('hr.holidays.index')->with('success', 'Holiday updated successfully');
    }

    public function toggle($id)
    {
        $holiday = Holiday::withTrashed()->findOrFail($id);

        if ($holiday->trashed()) {
            $holiday->restore();
            $message = 'Holiday activated successfully';
        } else {
            $holiday->delete();
            $message = 'Holiday deactivated successfully';
        }

        return redirect()->route('hr.holidays.index')->with('success', $message);
    }
}
