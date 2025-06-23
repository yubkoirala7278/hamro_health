@extends('admin.master')

@section('header-content')
    <style>
        .list-group-item.active {
            background-color: #f8f9fa;
            color: #000;
            border-color: rgba(0, 0, 0, .125);
        }

        .list-group-item.active .text-muted {
            color: rgba(0, 0, 0, .6) !important;
        }

        .rounded-3 {
            border-radius: 0.75rem !important;
        }

        .overflow-auto {
            scrollbar-width: thin;
        }

        .overflow-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .overflow-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
    </style>
@endsection

@section('content')
    <div class="row" >
        <!-- Left sidebar - Students list -->
        <div class="col-md-4 col-lg-3 px-0 border-end" style="max-height: 90vh;overflow-y:scroll">
            <div class="d-flex flex-column h-100">
                <!-- Header -->
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="flex-grow-1">
                        <h5 class="mb-0">Messages</h5>
                    </div>
                </div>

                <!-- Search -->
                <div class="p-3 border-bottom">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-0" placeholder="Search messages..." id="searchStudents">
                    </div>
                </div>

                <!-- Students list -->
                <div class="flex-grow-1 overflow-auto">
                    <div class="list-group list-group-flush" id="conversationList">
                        @foreach ($conversations as $conversation)
                            <a href="#" class="list-group-item list-group-item-action conversation-item {{ $loop->first ? 'active' : '' }}"
                               data-conversation-id="{{ $conversation->id }}"
                               data-student-name="{{ $conversation->student->name }}">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative">
                                        <img src="https://img.freepik.com/free-psd/3d-illustration-person-with-sunglasses_23-2149436188.jpg?semt=ais_hybrid&w=740"
                                             class="rounded-circle me-2" width="40" height="40">
                                        <span class="position-absolute bottom-0 end-0 bg-{{ $conversation->student->phone_number_verified_at ? 'success' : 'secondary' }} rounded-circle p-1 border border-2 border-white"></span>
                                    </div>
                                    <div class="flex-grow-1 ms-2 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $conversation->student->name }}</h6>
                                            <small class="{{ $loop->first ? 'text-white-50' : 'text-muted' }}">{{ $conversation->last_message_at ? $conversation->last_message_at->format('h:i A') : '' }}</small>
                                        </div>
                                        <p class="mb-0 text-truncate text-muted">{{ $conversation->messages->first()->content ?? 'No messages yet' }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Chat area -->
        <div class="col-md-8 col-lg-9 px-0 d-flex flex-column" style="max-height: 90vh;min-height:79vh;overflow-y:scroll">
            <!-- Chat header -->
            <div class="d-flex align-items-center p-3 border-bottom" id="chatHeader">
                @if($conversations->isNotEmpty())
                    <div class="d-flex align-items-center">
                        <img src="https://img.freepik.com/free-psd/3d-illustration-person-with-sunglasses_23-2149436188.jpg?semt=ais_hybrid&w=740"
                             class="rounded-circle me-2" width="40" height="40">
                        <div>
                            <h6 class="mb-0">{{ $conversations->first()->student->name }}</h6>
                            <small class="text-muted">{{ $conversations->first()->student->phone_number_verified_at ? 'Online' : 'Offline' }}</small>
                        </div>
                    </div>
                @else
                    <h6 class="mb-0">Select a student to chat</h6>
                @endif
            </div>

            <!-- Messages -->
            <div class="flex-grow-1 p-3 overflow-auto" style="background-color: #f5f6fa;" id="messageArea">
                <div class="d-flex flex-column" style="gap: 1rem;" id="messages">
                    <!-- Messages will be loaded via AJAX -->
                </div>
            </div>

            <!-- Message input -->
            <div class="p-3 border-top" id="messageInput" style="{{ $conversations->isEmpty() ? 'display: none;' : '' }}">
                <div class="input-group">
                    <input type="text" class="form-control rounded-pill" placeholder="Type a message..." id="messageContent">
                    <button class="btn btn-primary rounded-circle ms-1" id="sendMessage">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('js/laravel-reverb.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            let currentConversationId = @json($conversations->isNotEmpty() ? $conversations->first()->id : null);

            // Load initial conversation
            if (currentConversationId) {
                loadMessages(currentConversationId);
            }

            // Load conversation messages
            $('.conversation-item').click(function (e) {
                e.preventDefault();
                currentConversationId = $(this).data('conversation-id');
                const studentName = $(this).data('student-name');

                $('.conversation-item').removeClass('active');
                $(this).addClass('active');
                $(this).find('small').removeClass('text-muted').addClass('text-white-50');
                $('.conversation-item').not(this).find('small').removeClass('text-white-50').addClass('text-muted');

                $('#chatHeader').html(`
                    <div class="d-flex align-items-center">
                        <img src="${$(this).find('img').attr('src')}" class="rounded-circle me-2" width="40" height="40">
                        <div>
                            <h6 class="mb-0">${studentName}</h6>
                            <small class="text-muted">${$(this).find('.bg-success').length ? 'Online' : 'Offline'}</small>
                        </div>
                    </div>
                `);
                $('#messageInput').show();

                loadMessages(currentConversationId);
            });

            // Send message
            $('#sendMessage').click(function () {
                const content = $('#messageContent').val().trim();
                if (!content || !currentConversationId) return;

                $.post(`/admin/chat/${currentConversationId}/messages`, {
                    content: content,
                    _token: '{{ csrf_token() }}'
                }, function (data) {
                    $('#messages').append(`
                        <div class="d-flex flex-row-reverse">
                            <div class="text-end">
                                <div class="bg-primary text-white p-3 rounded-3" style="max-width: 500px;">
                                    <p class="mb-0">${data.content}</p>
                                </div>
                                <small class="text-muted">${new Date(data.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
                            </div>
                        </div>
                    `);
                    $('#messageContent').val('');
                    $('#messageArea').scrollTop($('#messageArea')[0].scrollHeight);

                    // Update conversation list
                    const conversationItem = $(`.conversation-item[data-conversation-id="${currentConversationId}"]`);
                    conversationItem.find('p').text(data.content);
                    conversationItem.find('small').text(new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
                    conversationItem.prependTo('#conversationList');
                });
            });

            // Search students
            $('#searchStudents').on('input', function () {
                const search = $(this).val().toLowerCase();
                $('.conversation-item').each(function () {
                    const name = $(this).data('student-name').toLowerCase();
                    $(this).toggle(name.includes(search));
                });
            });

            // Real-time messages with Reverb
            if (currentConversationId) {
                window.Echo.channel(`conversation.${currentConversationId}`)
                    .listen('MessageSent', (e) => {
                        if (e.message.sender_id !== {{ Auth::id() }}) {
                            $('#messages').append(`
                                <div class="d-flex">
                                    <img src="https://img.freepik.com/free-psd/3d-illustration-person-with-sunglasses_23-2149436188.jpg?semt=ais_hybrid&w=740"
                                         class="rounded-circle me-2" width="32" height="32">
                                    <div>
                                        <div class="bg-white p-3 rounded-3" style="max-width: 500px;">
                                            <p class="mb-0">${e.message.content}</p>
                                        </div>
                                        <small class="text-muted">${new Date(e.message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
                                    </div>
                                </div>
                            `);
                            $('#messageArea').scrollTop($('#messageArea')[0].scrollHeight);

                            // Update conversation list
                            const conversationItem = $(`.conversation-item[data-conversation-id="${currentConversationId}"]`);
                            conversationItem.find('p').text(e.message.content);
                            conversationItem.find('small').text(new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
                            conversationItem.prependTo('#conversationList');
                        }
                    });
            }

            function loadMessages(conversationId) {
                $.get(`/admin/chat/${conversationId}`, function (data) {
                    $('#messages').empty();
                    data.messages.forEach(message => {
                        const isSender = message.sender_id === {{ Auth::id() }};
                        $('#messages').append(`
                            <div class="d-flex ${isSender ? 'flex-row-reverse' : ''}">
                                ${!isSender ? `<img src="https://img.freepik.com/free-psd/3d-illustration-person-with-sunglasses_23-2149436188.jpg?semt=ais_hybrid&w=740"
                                                    class="rounded-circle me-2" width="32" height="32">` : ''}
                                <div ${isSender ? 'class="text-end"' : ''}>
                                    <div class="${isSender ? 'bg-primary text-white' : 'bg-white'} p-3 rounded-3" style="max-width: 500px;">
                                        <p class="mb-0">${message.content}</p>
                                    </div>
                                    <small class="text-muted">${new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
                                </div>
                            </div>
                        `);
                    });
                    $('#messageArea').scrollTop($('#messageArea')[0].scrollHeight);
                });
            }
        });
    </script>
@endsection