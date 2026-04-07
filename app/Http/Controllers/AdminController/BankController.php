<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the banks.
     * NOW: Only shows active banks (is_active = 1)
     */
  public function index(Request $request)
{
    $status = $request->get('status'); // active | inactive | null

    $banks = Bank::query()
        ->when($status === 'active', function ($q) {
            $q->where('is_active', true);
        })
        ->when($status === 'inactive', function ($q) {
            $q->where('is_active', false);
        })
        ->latest()
        ->paginate(10)
        ->withQueryString(); // keeps filter on pagination

    return view('admin.banks.index', compact('banks', 'status'));
}

    /**
     * Store a newly created bank in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255|unique:banks,bank_name',
        ]);

        Bank::create([
            'bank_name' => $request->bank_name,
            'is_active' => true // Default to active
        ]);

        return redirect()->back()->with('success', 'Bank added successfully!');
    }

    /**
     * Update the specified bank in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255|unique:banks,bank_name,' . $bank->id,
        ]);

        $bank->update([
            'bank_name' => $request->bank_name
        ]);

        return redirect()->back()->with('success', 'Bank updated successfully!');
    }

    /**
     * Toggle the Active/Inactive status of a bank.
     */
    public function toggleStatus($id)
    {
        // We find by ID directly so we can find hidden/inactive banks if needed
        $bank = Bank::findOrFail($id);

        // Toggle the boolean value (if 1 becomes 0, if 0 becomes 1)
        $bank->is_active = !$bank->is_active;
        $bank->save();

        $statusMessage = $bank->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Bank {$statusMessage} successfully!");
    }

    /**
     * Remove the specified bank from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();
        return redirect()->back()->with('success', 'Bank deleted successfully!');
    }
}