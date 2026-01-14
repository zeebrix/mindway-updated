<div class="action-buttons">
    <button type="button" class="btn btn-primary add-session-btn mindway-btn">
        Log
    </button>
    <a href="{{ route('counsellor.book.session', ['id' => $customer->id]) }}" 
       class="btn btn-success mindway-btn">
        Book
    </a>
</div>
