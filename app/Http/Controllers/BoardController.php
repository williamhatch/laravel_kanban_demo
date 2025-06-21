<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Board::class, 'board');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boards = Auth::user()->boards()->latest()->get();
        return view('boards.index', compact('boards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('boards.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBoardRequest $request)
    {
        $board = Auth::user()->boards()->create($request->validated());

        // Create default lists
        $defaultLists = ['To Do', 'In Progress', 'Done'];
        foreach ($defaultLists as $index => $listName) {
            $board->lists()->create([
                'name' => $listName,
                'position' => $index
            ]);
        }

        return redirect()->route('boards.show', $board)->with('success', 'Board created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Board $board)
    {
        $board->load(['lists.cards' => function($query) {
            $query->orderBy('position');
        }]);
        
        return view('boards.show', compact('board'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Board $board)
    {
        return view('boards.edit', compact('board'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBoardRequest $request, Board $board)
    {
        $board->update($request->validated());

        return redirect()->route('boards.show', $board)->with('success', 'Board updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Board $board)
    {
        $board->delete();
        return redirect()->route('boards.index')->with('success', 'Board deleted successfully!');
    }
}
