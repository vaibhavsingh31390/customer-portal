@php
    $messageLabels = $messageLabels ?? [
        'comment' => 'Comment',
        'remark' => 'Remark to customer',
        'action' => 'Action taken',
        'close' => 'Ticket closed',
        'status' => 'Status update',
    ];
    $roleLabels = $roleLabels ?? [
        'client' => 'Client',
        'support' => 'Support',
        'admin' => 'Admin',
        'system' => 'System',
    ];
@endphp

<article class="portal-thread__item portal-thread__item--{{ $message->author_role }}{{ $message->is_internal ? ' portal-thread__item--internal' : '' }}">
    <header class="portal-thread__meta">
        <strong>{{ $message->author_name ?: $message->author_user_code }}</strong>
        <span class="portal-thread__badge">{{ $roleLabels[$message->author_role] ?? ucfirst($message->author_role) }}</span>
        <span class="portal-thread__type">{{ $messageLabels[$message->message_type] ?? 'Message' }}</span>
        @if ($message->is_internal)
            <span class="portal-thread__badge portal-thread__badge--internal">Internal</span>
        @endif
        <time class="portal-thread__time">{{ $message->created_at?->format('d M Y, H:i') }}</time>
    </header>
    <div class="portal-thread__body">{{ $message->body }}</div>
    @if ($message->rating)
        <div class="portal-thread__rating">
            @for ($i = 1; $i <= 5; $i++)
                <span class="portal-thread-rating__star{{ $i <= $message->rating ? ' is-filled' : '' }}">★</span>
            @endfor
        </div>
    @endif
</article>
