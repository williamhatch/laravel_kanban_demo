<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\KanbanList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KanbanListController extends Controller
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
    public function store(Request $request, Board $board)
    {
        $this->authorize('create', [KanbanList::class, $board]);
        
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $position = $board->lists()->max('position') + 1;
        
        $list = $board->lists()->create([
            'name' => $request->name,
            'position' => $position
        ]);

        return response()->json($list);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, KanbanList $list)
    {
        $this->authorize('update', $list);
        
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $list->update($request->only(['name']));

        return response()->json($list);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KanbanList $list)
    {
        $this->authorize('delete', $list);
        $list->delete();
        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'lists' => 'required|array',
            'lists.*.id' => 'required|exists:lists,id',
            'lists.*.position' => 'required|integer'
        ]);

        foreach ($request->lists as $listData) {
            KanbanList::where('id', $listData['id'])->update(['position' => $listData['position']]);
        }

        return response()->json(['success' => true]);
    }
}
