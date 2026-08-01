<div class="portal-thread-rating mb-3" id="complaint-thread-rating">
    <span class="portal-thread-rating__label">Customer rating:</span>
    @for ($i = 1; $i <= 5; $i++)
        <span class="portal-thread-rating__star{{ $i <= (int) $rating ? ' is-filled' : '' }}">★</span>
    @endfor
    <span class="portal-thread-rating__value">({{ (int) $rating }}/5)</span>
</div>
