<?php

namespace App\Http\Controllers\OpearationController;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Offer;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class OperationsOfferController extends Controller
{
    public function create()
    {

        // Fetch recent offers sent by the current operation user
        $offers = Offer::where('sender_id', auth()->user()->id)
            ->withCount('notifications')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('Opearation.offers.create' ,compact('offers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachment.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        try {
            // Fetch all employees under team leads created by the current operation user
            $operationId = auth()->user()->id;
            $recipients = User::where('designation', 'employee')
                ->whereHas('teamLead', function ($query) use ($operationId) {
                    $query->where('created_by', $operationId);
                })
                ->pluck('id');

            if ($recipients->isEmpty()) {
                return redirect()->route('operations.offers.create')
                    ->with('error', 'No employees found under team leads created by you.')
                    ->withInput();
            }

            // Upload attachments
            $attachmentPaths = [];
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    $path = $file->store('task_attachments', 'public');
                    $attachmentPaths[] = $path;
                }
            }

            // Create offer record
            $offer = Offer::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'attachment' => $attachmentPaths ? json_encode($attachmentPaths) : null,
                'sender_id' => $operationId,
            ]);

        $attachmentPath = $attachmentPaths[0] ?? null;

            // Create notification for each employee
            foreach ($recipients as $userId) {
                Notification::create([
                    'user_id' => $userId,
                    'offer_id' => $offer->id,
                    'message' => $validated['title'], // Using title as the notification message
                    'is_read' => false,
                ]);

                 // 🔔 Push Notification
            NotificationHelper::sendOfferNotification(
                $userId,
                $offer->id,
                'New Offer Available',
                $validated['title'],
                $attachmentPath
            );
            }

            return redirect()->route('operations.offers.create')
                ->with('success', 'Notifications sent successfully to all employees!');
        } catch (Exception $e) {
            return redirect()->route('operations.offers.create')
                ->with('error', 'Failed to send notifications: ' . $e->getMessage())
                ->withInput();
        }
    }
}
