@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>My Kanban Boards</h2>
                    <a href="{{ route('boards.create') }}" class="btn btn-primary">Create New Board</a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($boards->count() > 0)
                        <div class="row">
                            @foreach($boards as $board)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $board->name }}</h5>
                                            <p class="card-text">{{ $board->description ?: 'No description' }}</p>
                                            <p class="card-text"><small class="text-muted">Created: {{ $board->created_at->format('M d, Y') }}</small></p>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('boards.show', $board) }}" class="btn btn-outline-primary">View</a>
                                                <a href="{{ route('boards.edit', $board) }}" class="btn btn-outline-secondary">Edit</a>
                                                <form action="{{ route('boards.destroy', $board) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this board?')">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <h4>No boards yet</h4>
                            <p class="text-muted">Create your first Kanban board to get started!</p>
                            <a href="{{ route('boards.create') }}" class="btn btn-primary">Create Your First Board</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 