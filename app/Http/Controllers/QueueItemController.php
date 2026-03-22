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

    public function storeMany(Request $request)
{
    $fields = $request->validate([
        'song_ids' => 'required|array',
        'song_ids.*' => 'exists:songs,id'
    ]);

    $userId = auth()->id();

    $currentMaxPosition = QueueItem::where('user_id', $userId)->max('position') ?? 0;

    $newItems = [];
    $now = now();

    foreach ($fields['song_ids'] as $index => $songId) {
        $newItems[] = [
            'user_id' => $userId,
            'song_id' => $songId,
            'position' => $currentMaxPosition + ($index + 1),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    QueueItem::insert($newItems);

    return response()->json([
        'message' => count($newItems) . ' songs added to queue!',
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
            return response()->json(['message' => 'Position did not change!']);
        }

        $item = QueueItem::where('user_id', $userId)
            ->where('position', $oldPos)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Queue item not found in old position!'], 404);
        }

        $maxPos = QueueItem::where('user_id', $userId)->max('position');
        if ($newPos > $maxPos) $newPos = $maxPos;
        if ($newPos < 1) $newPos = 1;

        DB::transaction(function () use ($userId, $item, $oldPos, $newPos) {
            if ($newPos < $oldPos) {
                QueueItem::where('user_id', $userId)
                    ->whereBetween('position', [$newPos, $oldPos - 1])
                    ->increment('position');
            } else {
                QueueItem::where('user_id', $userId)
                    ->whereBetween('position', [$oldPos + 1, $newPos])
                    ->decrement('position');
            }

            // Az elem elhelyezése az új pozícióba
            $item->update(['position' => $newPos]);
        });

        return response()->json([
            'message' => "Successfully updated queue item from position: {$oldPos} to position: {$newPos}!",
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

        $item = QueueItem::where('user_id', $userId)
            ->where('position', $position)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Queue item not found in given position'], 404);
        }

        DB::transaction(function () use ($userId, $position, $item) {
            $item->delete();

            QueueItem::where('user_id', $userId)
                ->where('position', '>', $position)
                ->decrement('position');
        });

        return response()->json(['message' => "Succesfully deleted queue item from position {$position}!"]);
    }

    public function clear()
    {
        $userId = auth()->id();

        QueueItem::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'Queue cleared succesfully!'
        ], 200);
    }
}
