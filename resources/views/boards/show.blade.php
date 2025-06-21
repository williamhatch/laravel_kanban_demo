@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>{{ $board->name }}</h1>
                    <p class="text-muted">{{ $board->description ?: 'No description' }}</p>
                </div>
                <div>
                    <a href="{{ route('boards.edit', $board) }}" class="btn btn-outline-secondary">Edit Board</a>
                    <a href="{{ route('boards.index') }}" class="btn btn-outline-primary">Back to Boards</a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Kanban Board -->
            <div class="kanban-board" id="kanban-board">
                @foreach($board->lists as $list)
                    <div class="kanban-list" data-list-id="{{ $list->id }}">
                        <div class="kanban-list-header">
                            <h5>{{ $list->name }}</h5>
                            <button class="btn btn-sm btn-outline-primary add-card-btn" data-list-id="{{ $list->id }}">
                                <i class="fas fa-plus"></i> Add Card
                            </button>
                        </div>
                        <div class="kanban-list-body" data-list-id="{{ $list->id }}">
                            @foreach($list->cards as $card)
                                <div class="kanban-card" data-card-id="{{ $card->id }}" draggable="true">
                                    <div class="card-header">
                                        <h6 class="card-title">{{ $card->title }}</h6>
                                        <div class="card-actions">
                                            <button class="btn btn-sm btn-outline-secondary edit-card-btn" data-card-id="{{ $card->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-card-btn" data-card-id="{{ $card->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @if($card->description)
                                        <div class="card-body">
                                            <p class="card-text">{{ Str::limit($card->description, 100) }}</p>
                                        </div>
                                    @endif
                                    <div class="card-footer">
                                        <span class="badge bg-{{ $card->priority === 'high' ? 'danger' : ($card->priority === 'medium' ? 'warning' : 'success') }}">
                                            {{ ucfirst($card->priority) }}
                                        </span>
                                        @if($card->due_date)
                                            <small class="text-muted d-block">
                                                Due: {{ $card->due_date->format('M d, Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Add Card Modal -->
<div class="modal fade" id="addCardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCardForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cardTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="cardTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="cardDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="cardDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="cardPriority" class="form-label">Priority</label>
                        <select class="form-control" id="cardPriority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cardDueDate" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="cardDueDate" name="due_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Card</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Card Modal -->
<div class="modal fade" id="editCardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCardForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editCardTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editCardTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCardDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editCardDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editCardPriority" class="form-label">Priority</label>
                        <select class="form-control" id="editCardPriority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editCardDueDate" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="editCardDueDate" name="due_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Card</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.kanban-board {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding: 1rem 0;
}

.kanban-list {
    min-width: 300px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.kanban-list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.kanban-list-body {
    min-height: 200px;
}

.kanban-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin-bottom: 0.5rem;
    cursor: grab;
    transition: box-shadow 0.2s;
}

.kanban-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.kanban-card.dragging {
    opacity: 0.5;
    cursor: grabbing;
}

.card-header {
    padding: 0.75rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-actions {
    display: flex;
    gap: 0.25rem;
}

.card-body {
    padding: 0.75rem;
}

.card-footer {
    padding: 0.75rem;
    border-top: 1px solid #dee2e6;
}

.kanban-list.drag-over {
    background: #e9ecef;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize drag and drop
    const lists = document.querySelectorAll('.kanban-list-body');
    lists.forEach(list => {
        new Sortable(list, {
            group: 'cards',
            animation: 150,
            onEnd: function(evt) {
                const cardId = evt.item.dataset.cardId;
                const newListId = evt.to.dataset.listId;
                const newPosition = evt.newIndex;
                
                // Update card position via AJAX
                fetch(`/cards/${cardId}/move`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        list_id: newListId,
                        position: newPosition
                    })
                });
            }
        });
    });

    // Add card functionality
    let currentListId = null;
    
    document.querySelectorAll('.add-card-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentListId = this.dataset.listId;
            document.getElementById('addCardModal').classList.add('show');
            document.getElementById('addCardModal').style.display = 'block';
        });
    });

    document.getElementById('addCardForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        fetch(`/lists/${currentListId}/cards`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            location.reload();
        });
    });

    // Edit card functionality
    let currentCardId = null;
    
    document.querySelectorAll('.edit-card-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentCardId = this.dataset.cardId;
            
            // Fetch card data
            fetch(`/cards/${currentCardId}`)
            .then(response => response.json())
            .then(card => {
                document.getElementById('editCardTitle').value = card.title;
                document.getElementById('editCardDescription').value = card.description || '';
                document.getElementById('editCardPriority').value = card.priority;
                document.getElementById('editCardDueDate').value = card.due_date ? card.due_date.split('T')[0] : '';
                
                document.getElementById('editCardModal').classList.add('show');
                document.getElementById('editCardModal').style.display = 'block';
            });
        });
    });

    document.getElementById('editCardForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        fetch(`/cards/${currentCardId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            location.reload();
        });
    });

    // Delete card functionality
    document.querySelectorAll('.delete-card-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this card?')) {
                const cardId = this.dataset.cardId;
                fetch(`/cards/${cardId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    location.reload();
                });
            }
        });
    });

    // Close modals
    document.querySelectorAll('.btn-close, .btn-secondary').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('show');
                modal.style.display = 'none';
            });
        });
    });
});
</script>
@endpush 