<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OfferController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Get filter parameters
        $filter = $request->query('filter', 'this_month'); // Default to current month
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Validate filter parameter
        $validFilters = ['today', 'this_month'];
        if (!in_array($filter, $validFilters) && (!$startDate || !$endDate)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid filter. Use "today", "this_month", or provide start_date and end_date.',
            ], 400);
        }

        // Base query (exclude soft-deleted offers)
        $query = Offer::query()->whereNull('deleted_at');

        // Apply date filters
        if ($filter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($filter === 'this_month') {
            $query->whereYear('created_at', Carbon::now()->year)
                  ->whereMonth('created_at', Carbon::now()->month);
        } elseif ($startDate && $endDate) {
            try {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                if ($start->gt($end)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'start_date must be before end_date',
                    ], 400);
                }
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid date format. Use YYYY-MM-DD.',
                ], 400);
            }
        }

        // Optionally scope to authenticated user (uncomment if needed)
        /*
        if (Auth::check()) {
            $query->where('sender_id', Auth::id());
        }
        */

        // Fetch offers with sender relationship
        $offers = $query->with(['sender'])->get()->map(function ($offer) {
            return [
                'id' => $offer->id,
                'title' => $offer->title,
                'description' => $offer->description,
                'attachment' => $offer->attachment ? json_decode($offer->attachment, true) : null,
                'sender' => $offer->sender ? [
                    'id' => $offer->sender->id,
                    'name' => $offer->sender->name,
                    'email' => $offer->sender->email ?? null,
                ] : null,
                'created_at' => $offer->created_at->toISOString(),
                'updated_at' => $offer->updated_at ? $offer->updated_at->toISOString() : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Offers retrieved successfully',
            'data' => [
                'offers' => $offers,
                'filters_applied' => [
                    'filter' => $filter,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
            ],
        ], 200);
    }
}