<?php

namespace App\Http\Controllers;

use App\Models\QueueItem;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueueItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $queue = QueueItem::with('song')
            ->where('user_id', auth()->id())
            ->orderBy('position', 'asc')
            ->get();

        return response()->json([
            'queue' => $queue
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $songId)
    {
        if (!Song::where('id', $songId)->exists()) {
            return response()->json(['message' => 'Song not found!'], 404);
        }

        $userId = auth()->id();

        $maxPosition = QueueItem::where('user_id', $userId)->max('position') ?? 0;

        $queueItem = QueueItem::create([
            'user_id' => $userId,
            'song_id' => $songId,
            'position' => $maxPosition + 1
        ]);

        return response()->json([
            'message' => 'Song added to queue!',
            'queue_item' => $queueItem->load('song')
        ], 201);
    }

    public function showByPosition($position)
    {
        $item = QueueItem::with('song')
            ->where('user_id', auth()->id())
            ->where('position', $position)
            ->firstOrFail();

        return response()->json($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(Queue $queue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Queue $queue)
    {
        //
    }

    public function updatePositionByPositions($oldPosition, $newPosition)
    {
        $userId = auth()->id();
        $oldPos = (int)$oldPosition;
        $newPos = (int)$newPosition;

        if ($oldPos === $newPos) {
            return response()->json(['message' => 'A pozíció nem változott.']);
        }

        // 1. Megkeressük az áthelyezni kívánt elemet
        $item = QueueItem::where('user_id', $userId)
            ->where('position', $oldPos)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Nem található elem a megadott eredeti pozíción!'], 404);
        }

        // 2. Max pozíció ellenőrzése a biztonság kedvéért
        $maxPos = QueueItem::where('user_id', $userId)->max('position');
        if ($newPos > $maxPos) $newPos = $maxPos;
        if ($newPos < 1) $newPos = 1;

        DB::transaction(function () use ($userId, $item, $oldPos, $newPos) {
            if ($newPos < $oldPos) {
                // Előrébb mozgatás (pl. 5 -> 2): a 2, 3, 4 pozíciójúak +1-et kapnak
                QueueItem::where('user_id', $userId)
                    ->whereBetween('position', [$newPos, $oldPos - 1])
                    ->increment('position');
            } else {
                // Hátrébb mozgatás (pl. 2 -> 5): a 3, 4, 5 pozíciójúak -1-et kapnak
                QueueItem::where('user_id', $userId)
                    ->whereBetween('position', [$oldPos + 1, $newPos])
                    ->decrement('position');
            }

            // Az elem elhelyezése az új pozícióba
            $item->update(['position' => $newPos]);
        });

        return response()->json([
            'message' => "Sikeresen áthelyezve {$oldPos} -> {$newPos} pozícióba.",
            'queue' => QueueItem::with('song')->where('user_id', $userId)->orderBy('position', 'asc')->get()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        
    }

    public function destroyByPosition($position)
    {
        $userId = auth()->id();
        
        // 1. Megkeressük az elemet a pozíció és a user_id alapján
        $item = QueueItem::where('user_id', $userId)
            ->where('position', $position)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Nincs elem ezen a pozíción!'], 404);
        }

        DB::transaction(function () use ($userId, $position, $item) {
            // 2. Töröljük az elemet
            $item->delete();

            // 3. Minden utána lévő elemet eggyel előrébb hozunk (csak az adott usernél)
            QueueItem::where('user_id', $userId)
                ->where('position', '>', $position)
                ->decrement('position');
        });

        return response()->json(['message' => "A(z) {$position}. pozíciójú elem törölve, a sorrend frissítve."]);
    }

    public function clear()
    {
        $userId = auth()->id();
        
        QueueItem::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'A várólista sikeresen kiürítve!'
        ], 200);
    }
}
