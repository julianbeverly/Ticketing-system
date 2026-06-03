<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Ticket Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/dist/css/style.css">
    @vite(['resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .lightbox-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(5px); justify-content: center; align-items: center; }
        .lightbox-content { position: relative; background-color: #d1d5db; padding: 40px; border-radius: 20px; width: 90%; max-width: 800px; min-height: 500px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .lightbox-header { position: absolute; top: 20px; left: 20px; right: 20px; display: flex; justify-content: space-between; color: #4b5563; font-weight: bold; font-size: 1.1rem; z-index: 10; }
        .lightbox-close { cursor: pointer; font-size: 1.5rem; color: #4b5563; }
        .lightbox-main { width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; }
        .lightbox-main img, .lightbox-main video { max-width: 100%; max-height: 70vh; border-radius: 12px; }
        .lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 45px; height: 45px; background: rgba(0, 0, 0, 0.4); color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; border: none; z-index: 10; }
        .lightbox-prev { left: -60px; }
        .lightbox-next { right: -60px; }
        #mainAttachmentImg, .video-placeholder { cursor: pointer; }

        /* WhatsApp-inspired Chat Styles */
        .comm-history {
            background-color: #efe7dd;
            background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
            background-repeat: repeat;
            display: flex;
            flex-direction: column;
        }
        .chat-msg {
            display: flex;
            margin-bottom: 12px;
            width: 100%;
        }
        .chat-msg.me-msg {
            justify-content: flex-end;
        }
        .chat-content {
            max-width: 85%;
            display: flex;
            flex-direction: column;
        }
        .chat-bubble {
            padding: 8px 12px;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .chat-bubble.sent {
            background-color: #e7f3ff; /* User preference: Light Blue */
            color: #000000;
            border-top-right-radius: 2px;
            margin-right: 8px;
        }
        .chat-bubble.received {
            background-color: #ffffff;
            color: #000000;
            border-top-left-radius: 2px;
            margin-left: 8px;
        }
        .chat-head {
            font-size: 0.7rem;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .role-label {
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .role-admin { color: #dc2626; }
        .role-technician { color: #7c3aed; }
        .role-employee { color: #059669; }
        
        .chat-time {
            font-size: 0.65rem;
            color: #667781;
            text-align: right;
            margin-top: 4px;
            display: block;
        }
        .chat-attachment {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(0,0,0,0.05);
            padding: 8px;
            border-radius: 6px;
            margin-top: 6px;
            text-decoration: none;
            color: #2563eb;
            font-size: 0.85rem;
        }
        .chat-attachment:hover { background: rgba(0,0,0,0.08); }
        .pill-overdue { background-color: #fecaca; color: #dc2626; border: 1px solid #f87171; }

        /* Main Carousel Dots and Buttons */
        .round-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.4);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: background 0.3s;
        }
        .round-btn:hover { background: rgba(0,0,0,0.6); }
        .right-btn { right: 10px; }
        .left-btn { left: 10px; }
        
        .carousel-dots { text-align: center; margin-top: 15px; }
        .dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #d1d5db;
            border-radius: 50%;
            margin: 0 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .dot.active { background: #2563eb; transform: scale(1.2); }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        @include('employee.partials.sidebar')

        <main class="main-content">
                <div class="header-actions">
                    
                    <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
                        <i class="fa-regular fa-circle-user"></i>
                    </a>
                </div>

            <div class="dashboard-content ticket-details-view">
               <div class="ticket-details-main">
                  <div class="td-left-card">
                      <div class="td-header"><span class="td-breadcrumb">MY TICKETS <i class="fa-solid fa-chevron-right"></i> <span class="td-id">ID: {{ $ticket->ticket_id }}</span></span></div>
                      
                      <div class="td-attributes">
                         <div class="td-attr-row">
                             <div class="td-attr"><span class="td-lbl">STATUS:</span><span class="td-pill pill-{{ str_replace(' ', '', $ticket->status) }}">{{ strtoupper(str_replace('_', ' ', $ticket->status)) }}</span></div>
                         </div>
                         <div class="td-attr-row">
                             <div class="td-attr"><span class="td-lbl">PRIORITY:</span><span class="td-pill pill-{{ $ticket->priority }}">{{ strtoupper($ticket->priority) }}</span></div>
                         </div>
                         <div class="td-attr-row">
                             <div class="td-attr">
                                 <span class="td-lbl">TECHNICIAN:</span>
                                 @if($ticket->technician)
                                     <span class="td-pill pill-grey">{{ strtoupper($ticket->technician->name) }}</span>
                                 @else
                                     <span class="td-pill pill-grey">UNASSIGNED</span>
                                 @endif
                             </div>
                         </div>
                      </div>

                      <div class="td-description-sec"><h3><i class="fa-regular fa-file-lines text-blue"></i> My Description</h3><p>{{ $ticket->description }}</p></div>

                      @if($ticket->status === 'resolved' || $ticket->status === 'closed')
                      <div class="td-description-sec" style="margin-top: 20px; border: 2px solid #059669; border-radius: 12px; padding: 1.5rem; background: #f0fdf4;">
                          <h3></i> Resolution Details</h3>
                          <p style="color: #166534; line-height: 1.6;">{{ $ticket->resolution_note }}</p>
                          <div style="font-size: 0.8rem; color: #6b7280; margin-top: 10px;">
                              Resolved on {{ $ticket->resolved_at ? $ticket->resolved_at->format('M d, Y h:i A') : 'N/A' }}
                          </div>

                          @if($ticket->status === 'resolved')
                          <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                              <button id="acceptSolutionBtn" style="flex: 1; padding: 0.75rem; background: #059669; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                  <i class="fa-solid fa-thumbs-up"></i> Accept Solution
                              </button>
                              <button id="rejectSolutionBtn" style="flex: 1; padding: 0.75rem; background: #dc2626; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                  <i class="fa-solid fa-thumbs-down"></i> Reject Solution
                              </button>
                          </div>
                          @endif
                      </div>
                      @endif

                      @if($ticket->rejection_note)
                      <div class="td-description-sec" style="margin-top: 15px; border: 2px solid #dc2626; border-radius: 12px; padding: 1.5rem; background: #fef2f2;">
                          <h3 style="color: #dc2626; margin: 0 0 0.75rem 0;"><i class="fa-solid fa-circle-xmark"></i> Employee Rejection Note</h3>
                          <p style="color: #991b1b; line-height: 1.6; margin: 0;">{{ $ticket->rejection_note }}</p>
                          <div style="font-size: 0.8rem; color: #6b7280; margin-top: 10px;">
                              Rejected on {{ $ticket->rejected_at ? $ticket->rejected_at->format('M d, Y h:i A') : 'N/A' }}
                          </div>
                      </div>
                      @endif

                      @if($ticket->attachments->count() > 0)
                       <div class="td-attachment-carousel">
                          <div class="td-carousel-container" onclick="openLightbox(currentIndex)" style="position: relative;">
                             <div id="mainAttachmentDisplay" style="width: 100%; height: 300px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 8px; background: #f3f4f6;">
                                @php $firstAttachment = $ticket->attachments->first(); @endphp
                                @if(str_contains($firstAttachment->mime_type, 'image'))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($firstAttachment->file_path) }}" alt="Ticket Attachment" id="mainAttachmentImg" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div class="video-placeholder" style="display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-circle-play" style="font-size: 3rem; color: #4b5563;"></i>
                                        <p style="margin-left: 10px; margin-bottom: 0; color: #4b5563; font-weight: bold;">Click to play video</p>
                                    </div>
                                @endif
                             </div>

                             @if($ticket->attachments->count() > 1)
                                 <div class="round-btn right-btn" id="nextAttachment"><i class="fa-solid fa-chevron-right"></i></div>
                                 <div class="round-btn left-btn" id="prevAttachment" style="left: 10px; right: auto;"><i class="fa-solid fa-chevron-left"></i></div>
                             @endif
                          </div>
                          <div class="carousel-dots">@foreach($ticket->attachments as $index => $attachment)<span class="dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></span>@endforeach</div>
                          <div class="td-attachment-info" style="margin-top: 15px;">
                              <div class="att-name" id="attachmentName"><i class="fa-regular fa-file"></i> {{ $firstAttachment->original_name }}</div>
                              <div class="att-time">Uploaded {{ $firstAttachment->created_at->format('M d, h:i A') }}</div>
                          </div>
                       </div>
                      @endif

                      <div class="td-meta-footer" style="display: flex; flex-direction: column; gap: 1.5rem;">
                          <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                              <div class="td-meta-box"><span class="meta-lbl">DATE CREATED</span><span class="meta-val">{{ $ticket->created_at->format('M d, Y - h:i A') }}</span></div>
                              <div class="td-meta-box"><span class="meta-lbl">CATEGORY</span><span class="meta-val">{{ $ticket->category ? $ticket->category->name : 'N/A' }}</span></div>
                              <div class="td-meta-box"><span class="meta-lbl">INCIDENT TYPE</span><span class="meta-val">{{ $ticket->custom_type ?? ($ticket->type ? $ticket->type->name : 'N/A') }}</span></div>
                          </div>
                          <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                              <div class="td-meta-box" style="flex: 2;"><span class="meta-lbl">SUBJECT</span><span class="meta-val">{{ $ticket->subject }}</span></div>
                              
                              @if($ticket->due_at && !in_array($ticket->status, ['resolved', 'closed']))
                              <div class="td-meta-box" style="background: #fff7ed; border: 1px solid #ffedd5; flex: 1;">
                                  <span class="meta-lbl" style="color: #9a3412;">DEADLINE COUNTDOWN</span>
                                  <span class="meta-val" id="slaCountdown" style="color: #c2410c; font-weight: 800; font-family: monospace; font-size: 1.1rem;">--:--:--</span>
                              </div>
                              @endif
                          </div>
                      </div>

                      @if($ticket->due_at && !in_array($ticket->status, ['resolved', 'closed']))
                      <script>
                          (function() {
                              const dueDate = new Date("{{ $ticket->due_at->toIso8601String() }}").getTime();
                              const countdownEl = document.getElementById('slaCountdown');

                              const timer = setInterval(function() {
                                  const now = new Date().getTime();
                                  const distance = dueDate - now;

                                  if (distance < 0) {
                                      clearInterval(timer);
                                      countdownEl.innerHTML = "EXPIRED";
                                      countdownEl.style.color = "#dc2626";
                                      return;
                                  }

                                  const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                  const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                  const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                  const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                  let display = "";
                                  if (days > 0) display += days + "d ";
                                  display += (hours < 10 ? "0" + hours : hours) + "h "
                                          + (minutes < 10 ? "0" + minutes : minutes) + "m "
                                          + (seconds < 10 ? "0" + seconds : seconds) + "s";

                                  countdownEl.innerHTML = display;
                              }, 1000);
                          })();
                      </script>
                      @endif
                  </div>
                  
                  <div class="td-right-panel">
                       <div class="comm-header">
                           <h3>Communication with Support</h3>
                           <div class="comm-meta"><span class="status-dot"></span> <span id="onlineCount">1</span> ONLINE</div>
                       </div>
                       <div id="chatHistory" class="comm-history" style="height: 400px; overflow-y: auto; padding: 1rem;"></div>
                       <div class="comm-input-area">
                           <input type="text" id="chatInput" placeholder="Type a message to support...">
                           <div class="comm-actions">
                               <div class="comm-icons">
                                   <label for="fileUpload" style="cursor: pointer;"><i class="fa-solid fa-paperclip"></i></label>
                                   <input type="file" id="fileUpload" multiple style="display: none;">
                               </div>
                               <button class="send-msg-btn" id="sendMessageBtn">Send <i class="fa-solid fa-paper-plane"></i></button>
                           </div>
                           <div id="filePreview" style="margin-top: 5px; font-size: 0.8rem; color: #6b7280;"></div>
                       </div>
                  </div>
               </div>
            </div>
        </main>
    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="lightbox-modal">
        <div class="lightbox-content">
            <div class="lightbox-header"><span id="lightboxCounter">1/1</span><span class="lightbox-close" onclick="closeLightbox()">&times;</span></div>
            <div class="lightbox-main" id="lightboxMain"></div>
            @if($ticket->attachments->count() > 1)
                <button class="lightbox-nav lightbox-prev" onclick="changeLightbox(-1)"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="lightbox-nav lightbox-next" onclick="changeLightbox(1)"><i class="fa-solid fa-chevron-right"></i></button>
            @endif
        </div>
    </div>

    <script>
        const attachments = @json($attachmentsData);
        let currentIndex = 0;
        const ticketId = "{{ $ticket->id }}";

        // Lightbox Logic
        const lightbox = document.getElementById('lightbox');
        const lightboxMain = document.getElementById('lightboxMain');
        const lightboxCounter = document.getElementById('lightboxCounter');

        function openLightbox(index) { currentIndex = index; lightbox.style.display = 'flex'; renderLightboxContent(); }
        function closeLightbox() { lightbox.style.display = 'none'; lightboxMain.innerHTML = ''; }
        function changeLightbox(step) { currentIndex = (currentIndex + step + attachments.length) % attachments.length; renderLightboxContent(); }
        function renderLightboxContent() {
            const att = attachments[currentIndex];
            lightboxCounter.innerText = `${currentIndex + 1}/${attachments.length}`;
            if (att.mime.includes('image')) lightboxMain.innerHTML = `<img src="${att.url}" alt="Attachment">`;
            else lightboxMain.innerHTML = `<video controls autoplay style="max-height: 70vh; border-radius: 12px;"><source src="${att.url}" type="${att.mime}">Your browser does not support the video tag.</video>`;
        }
        lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });

        // Main Carousel Logic
        function updateMainAttachment(index) {
            currentIndex = (index + attachments.length) % attachments.length;
            const att = attachments[currentIndex];
            const display = document.getElementById('mainAttachmentDisplay');
            
            if (att.mime.includes('image')) {
                display.innerHTML = `<img src="${att.url}" alt="Ticket Attachment" id="mainAttachmentImg" style="width: 100%; height: 100%; object-fit: cover;">`;
            } else {
                display.innerHTML = `
                    <div class="video-placeholder" style="display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-circle-play" style="font-size: 3rem; color: #4b5563;"></i>
                        <p style="margin-left: 10px; margin-bottom: 0; color: #4b5563; font-weight: bold;">Click to play video</p>
                    </div>`;
            }
            
            // Update dots
            document.querySelectorAll('.dot').forEach((dot, idx) => {
                dot.classList.toggle('active', idx === currentIndex);
            });
            
            // Update name
            if (document.getElementById('attachmentName')) {
                document.getElementById('attachmentName').innerHTML = `<i class="fa-regular fa-file"></i> ${att.name}`;
            }
        }

        const nextBtn = document.getElementById('nextAttachment');
        const prevBtn = document.getElementById('prevAttachment');
        if (nextBtn) nextBtn.addEventListener('click', (e) => { e.stopPropagation(); updateMainAttachment(currentIndex + 1); });
        if (prevBtn) prevBtn.addEventListener('click', (e) => { e.stopPropagation(); updateMainAttachment(currentIndex - 1); });

        document.querySelectorAll('.dot').forEach(dot => {
            dot.addEventListener('click', (e) => {
                e.stopPropagation();
                updateMainAttachment(parseInt(dot.dataset.index));
            });
        });

        // Response to Resolution Logic
        const acceptBtn = document.getElementById('acceptSolutionBtn');
        const rejectBtn = document.getElementById('rejectSolutionBtn');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', () => respondToRes('accept'));
        }
        if (rejectBtn) {
            rejectBtn.addEventListener('click', () => respondToRes('reject'));
        }

        function respondToRes(action) {
            if (action === 'accept') {
                Swal.fire({
                    title: 'Accept Solution?',
                    text: 'Are you sure you really want to accept this ticket solution?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Accept it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitResponse('accept', null);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Reject Solution?',
                    html: '<p style="margin-bottom: 10px; color: #6b7280;">Please provide a reason for rejecting this solution:</p>',
                    input: 'textarea',
                    inputPlaceholder: 'Enter your reason for rejection...',
                    inputAttributes: {
                        'aria-label': 'Rejection reason',
                        style: 'min-height: 100px;'
                    },
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Reject it',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => {
                        if (!value || !value.trim()) {
                            return 'Please enter a reason for rejection!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitResponse('reject', result.value);
                    }
                });
            }
        }

        function submitResponse(action, rejectionNote) {
            const body = { action: action };
            if (rejectionNote) {
                body.rejection_note = rejectionNote;
            }

            fetch(`/tickets/${ticketId}/respond`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Something went wrong', 'error');
                }
            });
        }

    // ─── Chat Logic ────────────────────────────────────────────────
    const chatHistory = document.getElementById('chatHistory');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendMessageBtn');
    const fileUpload = document.getElementById('fileUpload');
    const filePreview = document.getElementById('filePreview');

    // Track rendered message IDs to prevent duplicates
    const renderedIds = new Set();

    // Load old messages once
    function fetchMessages() {
        fetch(`/tickets/${ticketId}/messages?t=` + new Date().getTime(), {
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('Server error: ' + res.status);
                return res.json();
            })
            .then(data => {
                document.getElementById('onlineCount').innerText = data.online_count;
                renderMessages(data.messages);
            })
            .catch(err => console.error('[Chat] fetchMessages failed:', err));
    }

    // Render all messages (full re-render on initial load)
    function renderMessages(messages) {
        chatHistory.innerHTML = '';
        renderedIds.clear();
        messages.forEach(msg => appendMessage(msg));
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    // Append single message
    function appendMessage(msg) {
        // Prevent duplicate messages
        if (msg.id && renderedIds.has(msg.id)) return;
        if (msg.id) renderedIds.add(msg.id);

        const isAtBottom = chatHistory.scrollHeight - chatHistory.scrollTop <= chatHistory.clientHeight + 100;

        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-msg ${msg.is_me ? 'me-msg' : ''}`;

        let attsHtml = '';
        (msg.attachments || []).forEach(a => {
            attsHtml += `
                <a href="${a.url}" target="_blank" class="chat-attachment">
                    <i class="fa-solid fa-paperclip"></i>
                    <span>${a.name}</span>
                </a>
            `;
        });

        const role = msg.role || 'user';
        const roleClass = `role-${role.toLowerCase()}`;

        msgDiv.innerHTML = `
            <div class="chat-content">
                <div class="chat-bubble ${msg.is_me ? 'sent' : 'received'}">
                    <div class="chat-head">
                        <span class="role-label ${roleClass}">${role}</span>
                        <span style="font-weight: 600;">${msg.user_name || ''}</span>
                    </div>
                    <div class="chat-body">
                        <p style="margin: 0; white-space: pre-wrap;">${msg.content || ''}</p>
                        ${attsHtml}
                    </div>
                    <span class="chat-time">${msg.time || ''}</span>
                </div>
            </div>
        `;

        chatHistory.appendChild(msgDiv);

        if (isAtBottom) {
            chatHistory.scrollTop = chatHistory.scrollHeight;
        }
    }

    // Send message
    sendBtn.addEventListener('click', sendMessage);

    chatInput.addEventListener('keypress', (e) => {

        if (e.key === 'Enter') {

            sendMessage();
        }
    });

    function sendMessage() {

        const content = chatInput.value;

        const files = fileUpload.files;

        if (!content && files.length === 0) {
            return;
        }

        const fd = new FormData();

        fd.append('content', content);

        for (let i = 0; i < files.length; i++) {

            fd.append('attachments[]', files[i]);
        }

        fetch(`/tickets/${ticketId}/messages`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Socket-Id': window.Echo ? window.Echo.socketId() : '',
                'Accept': 'application/json'
            },

            body: fd

        })
        .then(res => {
            if (!res.ok) throw new Error('Server error: ' + res.status);
            return res.json();
        })
        .then(data => {

            if (data.error) {
                console.error('[Chat] sendMessage error:', data.error);
                return;
            }

            // Show sender message instantly
            appendMessage(data.message);

            // Clear fields
            chatInput.value = '';

            fileUpload.value = '';

            filePreview.innerText = '';
        })
        .catch(err => console.error('[Chat] sendMessage failed:', err));
    }

    // File preview
    fileUpload.addEventListener('change', function () {
        filePreview.innerText = this.files.length > 0 ? `${this.files.length} file(s) selected` : '';
    });

    // Initial load — always runs first, regardless of WebSocket status
    fetchMessages();

    // Poll every 3 seconds as a reliable fallback for real-time updates
    setInterval(fetchMessages, 3000);

    // REALTIME SOCKET LISTENER (bonus: instant updates when Reverb is running)
    try {
        if (window.Echo) {
            window.Echo.private(`ticket.${ticketId}`)
                .listen('.message.sent', (e) => {
                    if (e && e.message) appendMessage(e.message);
                });
        }
    } catch (err) {
        console.warn('[Chat] WebSocket unavailable, using polling only:', err);
    }

    </script>
</body>
</html>
