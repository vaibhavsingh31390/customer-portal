(function ($) {
    'use strict';

    function getPanel() {
        return document.getElementById('complaint-thread-panel');
    }

    function appendThreadHtml(html) {
        if (!html) {
            return;
        }

        const thread = document.getElementById('complaint-thread');
        if (!thread) {
            return;
        }

        const empty = document.getElementById('complaint-thread-empty');
        if (empty) {
            empty.remove();
        }

        thread.insertAdjacentHTML('beforeend', html);
        thread.scrollTop = thread.scrollHeight;
    }

    function applyThreadState(state) {
        if (!state) {
            return;
        }

        const composeWrap = document.getElementById('complaint-thread-compose-wrap');
        const closeWrap = document.getElementById('complaint-thread-close-wrap');
        const ratingWrap = document.getElementById('complaint-thread-rating-wrap');
        const statusSelect = document.getElementById('P3_STATUS_TYPE');

        if (state.ratingHtml && ratingWrap) {
            ratingWrap.innerHTML = state.ratingHtml;
        }

        if (statusSelect && state.status) {
            statusSelect.value = state.status;
        }

        if (state.isClosed) {
            if (composeWrap) {
                composeWrap.innerHTML =
                    '<p class="portal-thread__closed-note" id="complaint-thread-closed-note">This ticket is closed. No further replies can be added.</p>';
            }
            if (closeWrap) {
                closeWrap.innerHTML = '';
            }
        } else {
            if (!state.canClose && closeWrap) {
                closeWrap.innerHTML = '';
            }
        }
    }

    function resetReplyForm() {
        const form = document.getElementById('threadReplyForm');
        if (!form) {
            return;
        }
        form.reset();
        const internal = document.getElementById('thread_is_internal');
        if (internal) {
            internal.checked = false;
        }
    }

    function resetCloseForm() {
        const form = document.getElementById('threadCloseForm');
        if (!form) {
            return;
        }
        form.reset();
        toggleCloseRating();
    }

    function toggleCloseRating() {
        const panel = getPanel();
        if (!panel) {
            return;
        }

        const isClient = panel.dataset.isClient === '1';
        const statusEl = document.getElementById('close_status');
        const ratingEl = document.getElementById('close_rating');
        const wrap = document.getElementById('close_rating_wrap');

        if (!statusEl || !wrap) {
            return;
        }

        const isComplete = statusEl.value === 'CM';

        if (isClient && isComplete) {
            wrap.style.display = '';
            if (ratingEl) {
                ratingEl.required = true;
            }
        } else if (isClient) {
            wrap.style.display = 'none';
            if (ratingEl) {
                ratingEl.required = false;
                ratingEl.value = '';
            }
        } else {
            wrap.style.display = isComplete ? '' : 'none';
            if (ratingEl) {
                ratingEl.required = false;
            }
        }
    }

    function setButtonLoading(button, loading) {
        if (!button) {
            return;
        }
        button.disabled = loading;
        button.classList.toggle('is-loading', loading);
    }

    function initComplaintThread() {
        const panel = getPanel();
        if (!panel) {
            return;
        }

        const replyUrl = panel.dataset.replyUrl;
        const closeUrl = panel.dataset.closeUrl;
        const csrf = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrf } });

        $('#threadReplyForm').on('submit', function (e) {
            e.preventDefault();
            const button = document.getElementById('threadReplySubmit');
            setButtonLoading(button, true);

            $.post(replyUrl, {
                body: $('#thread_body').val(),
                message_type: $('#thread_message_type').val(),
                is_internal: $('#thread_is_internal').is(':checked') ? 1 : 0,
            })
                .done(function (res) {
                    if (res.type) {
                        appendThreadHtml(res.html);
                        applyThreadState(res.state);
                        resetReplyForm();
                        if (typeof showToast === 'function') {
                            showToast(res.message, true);
                        }
                    } else if (typeof showToast === 'function') {
                        showToast(res.message, false);
                    }
                })
                .always(function () {
                    setButtonLoading(button, false);
                });
        });

        $('#close_status').on('change', toggleCloseRating);
        toggleCloseRating();

        $('#threadCloseForm').on('submit', function (e) {
            e.preventDefault();
            const button = document.getElementById('threadCloseSubmit');
            setButtonLoading(button, true);

            $.post(closeUrl, {
                status: $('#close_status').val(),
                rating: $('#close_rating').val(),
                body: $('#close_body').val(),
            })
                .done(function (res) {
                    if (res.type) {
                        appendThreadHtml(res.html);
                        applyThreadState(res.state);
                        resetCloseForm();
                        if (typeof showToast === 'function') {
                            showToast(res.message, true);
                        }
                    } else if (typeof showToast === 'function') {
                        showToast(res.message, false);
                    }
                })
                .always(function () {
                    setButtonLoading(button, false);
                });
        });
    }

    $(initComplaintThread);
})(jQuery);
