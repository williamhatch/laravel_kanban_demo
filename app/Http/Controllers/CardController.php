<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\KanbanList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, KanbanList $list)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high',
            'due_date' => 'nullable|date'
        ]);

        $position = $list->cards()->max('position') + 1;
        
        $card = $list->cards()->create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
            'position' => $position,
            'user_id' => Auth::id()
        ]);

        return response()->json($card->load('user'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        return response()->json($card->load('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high',
            'due_date' => 'nullable|date'
        ]);

        $card->update($request->only(['title', 'description', 'priority', 'due_date']));

        return response()->json($card->load('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        $card->delete();
        return response()->json(['success' => true]);
    }

    public function move(Request $request, Card $card)
    {
        $request->validate([
            'list_id' => 'required|exists:lists,id',
            'position' => 'required|integer'
        ]);

        $card->update([
            'list_id' => $request->list_id,
            'position' => $request->position
        ]);

        return response()->json($card->load('user'));
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'cards' => 'required|array',
            'cards.*.id' => 'required|exists:cards,id',
            'cards.*.position' => 'required|integer',
            'cards.*.list_id' => 'required|exists:lists,id'
        ]);

        foreach ($request->cards as $cardData) {
            Card::where('id', $cardData['id'])->update([
                'position' => $cardData['position'],
                'list_id' => $cardData['list_id']
            ]);
        }

        return response()->json(['success' => true]);
    }
}
