@php
    $messageLabels = [
        'comment' => 'Comment',
        'remark' => 'Remark to customer',
        'action' => 'Action taken',
        'close' => 'Ticket closed',
        'status' => 'Status update',
    ];
    $roleLabels = [
        'client' => 'Client',
        'support' => 'Support',
        'admin' => 'Admin',
        'system' => 'System',
    ];
    $encodedId = base64_encode($data->complaint_number);
@endphp

<div class="row mt-3">
    <div class="col-xl-12">
        <x-card title="Communication thread">
            <div id="complaint-thread-rating-wrap">
                @if ($isClosed && $data->rating)
                    @include('complaint._threadRating', ['rating' => $data->rating])
                @endif
            </div>

            <div class="portal-thread" id="complaint-thread">
                @forelse ($messages as $message)
                    @include('complaint._threadMessage', ['message' => $message])
                @empty
                    <p class="portal-thread__empty" id="complaint-thread-empty">No messages yet.</p>
                @endforelse
            </div>

            <div id="complaint-thread-compose-wrap">
            @if ($canReply)
                <div class="portal-thread-compose">
                    <h3 class="portal-form-section__title">Post a reply</h3>
                    <form id="threadReplyForm" class="portal-form">
                        @if ($isStaff)
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="thread_message_type">Message type</label>
                                    <select id="thread_message_type" name="message_type" class="form-control">
                                        <option value="comment">General comment</option>
                                        <option value="remark">Remark to customer</option>
                                        <option value="action">Action taken</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4 d-flex align-items-end">
                                    <label class="portal-checkbox mb-2">
                                        <input type="checkbox" id="thread_is_internal" name="is_internal" value="1">
                                        Internal note (staff only)
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="thread_body">Message</label>
                            <textarea id="thread_body" name="body" class="form-control" rows="4" maxlength="2000" placeholder="Write your reply…" required></textarea>
                        </div>
                        <x-button type="submit" variant="primary" id="threadReplySubmit">Send reply</x-button>
                    </form>
                </div>
            @elseif ($isClosed)
                <p class="portal-thread__closed-note" id="complaint-thread-closed-note">This ticket is closed. No further replies can be added.</p>
            @endif
            </div>

            <div id="complaint-thread-close-wrap">
            @if ($canClose)
                <div class="portal-thread-close">
                    <h3 class="portal-form-section__title">Close ticket</h3>
                    <p class="text-muted mb-3">Only clients and admins can close tickets. Clients must provide a rating when marking complete.</p>
                    <form id="threadCloseForm" class="portal-form">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="close_status">Close as</label>
                                <select id="close_status" name="status" class="form-control">
                                    <option value="CM">Complete</option>
                                    <option value="CL">Cancel</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4" id="close_rating_wrap">
                                <label for="close_rating">Rating @if (!$isStaff || ($isAdmin ?? false) === false)<span class="text-danger">*</span>@endif</label>
                                <select id="close_rating" name="rating" class="form-control" @if (!$isStaff) required @endif>
                                    <option value="">Select rating…</option>
                                    @for ($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}">{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="close_body">Closing note (optional)</label>
                            <textarea id="close_body" name="body" class="form-control" rows="2" maxlength="2000" placeholder="Add a final note…"></textarea>
                        </div>
                        <x-button type="submit" variant="secondary" id="threadCloseSubmit">Close ticket</x-button>
                    </form>
                </div>
            @endif
            </div>
        </x-card>
    </div>
</div>

<div id="complaint-thread-panel"
    data-reply-url="{{ route('complaint.messages.store', ['id' => $encodedId]) }}"
    data-close-url="{{ route('complaint.close', ['id' => $encodedId]) }}"
    data-is-client="{{ $isStaff ? '0' : '1' }}">
</div>
