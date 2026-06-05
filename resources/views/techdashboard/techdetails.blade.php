<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Ticket Details - {{ $ticket->ticket_id }}</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="/dist/css/style.css">
  @vite(['resources/js/app.js'])
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
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
    .online-indicator { color: #10b981; font-weight: bold; }
    .pill-overdue { background-color: #fecaca; color: #dc2626; border: 1px solid #f87171; }

    /* Lightbox Modal Styles */
    .lightbox-modal { display: none; position: fixed; z-index: 20000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); backdrop-filter: blur(5px); justify-content: center; align-items: center; }
    .lightbox-content { position: relative; background-color: #d1d5db; padding: 40px; border-radius: 20px; width: 90%; max-width: 900px; min-height: 500px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .lightbox-header { position: absolute; top: 20px; left: 20px; right: 20px; display: flex; justify-content: space-between; align-items: center; }
    .lightbox-close { font-size: 35px; font-weight: bold; color: #1f2937; cursor: pointer; transition: color 0.2s; }
    .lightbox-close:hover { color: #dc2626; }
    .lightbox-main { width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; }
    .lightbox-main img, .lightbox-main video { max-width: 100%; max-height: 70vh; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    .lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); background: white; border: none; width: 50px; height: 50px; border-radius: 25px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #1f2937; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: all 0.2s; }
    .lightbox-nav:hover { background: #f3f4f6; transform: translateY(-50%) scale(1.1); }
    .lightbox-prev { left: -25px; }
    .lightbox-next { right: -25px; }
    #lightboxCounter { background: #1f2937; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
  </style>
</head>
<body>
  <div class="dashboard-layout">
        @include('techdashboard.partials.sidebar')

    <main class="main-content">
      <header class="top-header">
        <button class="menu-toggle" id="menuToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div style="flex: 1;"></div>
                <div class="header-actions">
                    
                    <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
                        <i class="fa-regular fa-circle-user"></i>
                    </a>
                </div>
      </header>

      <div class="dashboard-content ticket-details-view">
        @if(session()->has('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb; width: 100%;">
          {{ session()->get('success') }}
        </div>
        @endif
        
        <div class="ticket-details-main">
          <div class="td-left-card">
            <div class="breadcrumb">
              <a href="{{ route('tech.tickets') }}">Ticket Management</a> &gt; <span>{{ $ticket->ticket_id }}</span>
            </div>

            <div class="td-attributes">
              <div class="td-attr-row">
                <div class="td-attr">
                  <span class="td-lbl">STATUS:</span>
                  <select id="statusUpdateSelect" style="padding: 4px 8px; border-radius: 4px; border: 1px solid #d1d5db; font-weight: bold; text-transform: uppercase;">
                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="assigned" {{ $ticket->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="overdue" {{ $ticket->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                  </select>
                </div>
              </div>
              <div class="td-attr-row">
                <div class="td-attr">
                  <span class="td-lbl">EMPLOYEE:</span>
                  <span class="td-pill pill-grey">{{ $ticket->user->name }}</span>
                </div>
              </div>
              <div class="td-attr-row">
                <div class="td-attr">
                  <span class="td-lbl">PRIORITY:</span>
                  <span class="td-pill pill-{{ $ticket->priority }}">{{ strtoupper($ticket->priority) }}</span>
                </div>
              </div>
              <div class="td-attr-row">
                <div class="td-attr" style="justify-content: flex-start; padding-top: 10px;">
                  <button class="btn-export blue-btn" id="resolveTicketBtn" style="padding: 10px 20px; font-weight: bold;">
                    <i class="fa-solid fa-check"></i>
                    <span>RESOLUTION NOTE</span>
                  </button>
                </div>
              </div>
            </div>

            <div class="td-description-sec">
              <h3><i class="fa-regular fa-file-lines text-blue"></i> Detailed Description</h3>
              <p>{{ $ticket->description }}</p>
            </div>

            @if($ticket->status === 'resolved' || $ticket->status === 'closed')
            <div class="td-description-sec" style="margin-top: 20px; border: 2px solid #059669; border-radius: 12px; padding: 1.5rem; background: #f0fdf4;">
                <h3 style="color: #059669;"><i class="fa-solid fa-check-double"></i> My Resolution Details</h3>
                <p style="color: #166534; line-height: 1.6;">{{ $ticket->resolution_note }}</p>
                <div style="font-size: 0.8rem; color: #6b7280; margin-top: 10px;">
                    Resolved on {{ $ticket->resolved_at ? $ticket->resolved_at->format('M d, Y h:i A') : 'N/A' }}
                </div>
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
                  @php $firstAttachment = $ticket->attachments->first(); @endphp
                  
                  {{-- Main Display Container --}}
                  <div id="mainAttachmentContainer" style="width: 100%; height: 300px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 8px; background: #f3f4f6;">
                      @if(Str::startsWith($firstAttachment->mime_type, 'image/'))
                          <img src="{{ Storage::url($firstAttachment->file_path) }}" alt="Ticket Attachment" id="mainAttachmentImg" style="width: 100%; height: 100%; object-fit: cover;">
                      @elseif(Str::startsWith($firstAttachment->mime_type, 'video/'))
                          <div class="video-placeholder" style="display: flex; align-items: center; justify-content: center;">
                              <i class="fa-solid fa-circle-play" style="font-size: 3rem; color: #4b5563;"></i>
                              <p style="margin-left: 10px; margin-bottom: 0; color: #4b5563; font-weight: bold;">Click to play video</p>
                          </div>
                      @else
                          <div class="file-placeholder" style="text-align: center;">
                              <i class="fa-solid fa-file" style="font-size: 4rem; color: #4b5563;"></i>
                              <p style="margin-top: 10px; color: #4b5563; font-weight: bold;">{{ $firstAttachment->original_name }}</p>
                          </div>
                      @endif
                  </div>

                  @if($ticket->attachments->count() > 1)
                      <button type="button" onclick="event.stopPropagation(); changeAttachment(-1)" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.4); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;">
                          <i class="fa-solid fa-chevron-left"></i>
                      </button>
                      <button type="button" onclick="event.stopPropagation(); changeAttachment(1)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.4); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;">
                          <i class="fa-solid fa-chevron-right"></i>
                      </button>
                  @endif
               </div>
               <div class="carousel-dots" style="text-align: center; margin-top: 10px;">
                   @foreach($ticket->attachments as $idx => $att)
                       <span class="dot {{ $idx === 0 ? 'active' : '' }}" style="display: inline-block; width: 8px; height: 8px; background: #d1d5db; border-radius: 50%; margin: 0 4px; transition: background 0.3s;"></span>
                   @endforeach
               </div>
               <div class="td-attachment-info" style="margin-top: 15px;">
                 <div class="att-time" id="attachmentTimeInfo">Uploaded {{ $ticket->created_at->format('M d, h:i A') }}</div>
               </div>
            </div>
            @endif

            <div class="td-meta-footer" style="display: flex; flex-direction: column; gap: 1.5rem;">
              <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <div class="td-meta-box"><span class="meta-lbl">DATE CREATED</span><span class="meta-val">{{ $ticket->created_at->format('M d, Y - h:i A') }}</span></div>
                <div class="td-meta-box"><span class="meta-lbl">CATEGORY</span><span class="meta-val">{{ $ticket->category ? $ticket->category->name : 'N/A' }}</span></div>
                <div class="td-meta-box"><span class="meta-lbl">INCIDENT TYPE</span><span class="meta-val">{{ $ticket->type ? $ticket->type->name : $ticket->custom_type }}</span></div>
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

          <!-- Chat System -->
          <div class="td-right-panel">
            <div class="comm-header">
              <h3>Communication Log</h3>
              <div class="comm-meta"><span class="status-dot"></span> <span id="onlineCount">1</span> PARTICIPANTS ONLINE</div>
            </div>

            <div id="chatHistory" class="comm-history" style="height: 400px; overflow-y: auto; padding: 1rem;">
              <!-- Messages will be loaded here via AJAX -->
            </div>

            <div class="comm-input-area">
              <input type="text" id="chatInput" placeholder="Type a message...">
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

  <!-- Resolution Modal -->
  <div id="resolveModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 10000; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
      <div class="modal-box" style="background: white; padding: 2rem; border-radius: 12px; width: 450px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
          <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
              <h2 style="margin: 0; font-size: 1.5rem; color: #111827;">Resolution Note</h2>
              <button id="closeResolveModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #9ca3af;">&times;</button>
          </div>
          <form id="resolveForm" action="{{ route('tech.tickets.resolve', $ticket->id) }}" method="POST">
              @csrf
              <div style="margin-bottom: 1.25rem;">
                  <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">RESOLUTION DESCRIPTION</label>
                  <textarea name="resolution_note" required placeholder="Describe the steps taken to resolve the issue..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; min-height: 100px; resize: vertical;"></textarea>
              </div>
              <div style="margin-bottom: 1.5rem;">
                  <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">COMPLETION TIMESTAMP</label>
                  <input type="text" value="{{ now()->format('M d, Y h:i A') }}" readonly style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; background: #f9fafb; color: #6b7280;">
              </div>
              <div style="display: flex; gap: 1rem;">
                  <button type="button" id="cancelResolveBtn" style="flex: 1; padding: 0.75rem; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                  <button type="submit" style="flex: 2; padding: 0.75rem; background: #003399; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Confirm & Mark Resolved</button>
              </div>
          </form>
      </div>
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
    const ticketId = "{{ $ticket->id }}";
    const chatHistory = document.getElementById('chatHistory');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendMessageBtn');
    const fileUpload = document.getElementById('fileUpload');
    const filePreview = document.getElementById('filePreview');
    const statusSelect = document.getElementById('statusUpdateSelect');

    // Resolution Modal Logic
    const resolveModal = document.getElementById('resolveModal');
    const resolveBtn = document.getElementById('resolveTicketBtn');
    const closeResolveBtn = document.getElementById('closeResolveModal');
    const cancelResolveBtn = document.getElementById('cancelResolveBtn');
    const resolveForm = document.getElementById('resolveForm');

    if (resolveBtn) {
        resolveBtn.addEventListener('click', () => {
            resolveModal.style.display = 'flex';
        });
    }

    const closeResolve = () => {
        resolveModal.style.display = 'none';
        resolveForm.reset();
    };

    if (closeResolveBtn) closeResolveBtn.addEventListener('click', closeResolve);
    if (cancelResolveBtn) cancelResolveBtn.addEventListener('click', closeResolve);

    window.addEventListener('click', (e) => {
        if (e.target === resolveModal) closeResolve();
    });

    if (resolveForm) {
        resolveForm.addEventListener('submit', function(e) {
            // Close modal immediately
            resolveModal.style.display = 'none';
            // Allow form to submit naturally so browser reload icon turns
        });
    }

    // Status Update
    statusSelect.addEventListener('change', function() {
        const newStatus = this.value;
        fetch(`/tickets/${ticketId}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to update status');
            }
        });
    });

    // ── Chat: Track rendered messages to prevent duplicates ──
    const renderedIds = new Set();

    // Chat Logic
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

    function renderMessages(messages) {
        chatHistory.innerHTML = '';
        renderedIds.clear();
        messages.forEach(msg => appendMessage(msg));
    }

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
                </a>`;
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

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => { if(e.key === 'Enter') sendMessage(); });

    function sendMessage() {
        const content = chatInput.value;
        const files = fileUpload.files;
        if (!content && files.length === 0) return;

        const formData = new FormData();
        formData.append('content', content);
        for (let i = 0; i < files.length; i++) {
            formData.append('attachments[]', files[i]);
        }

        fetch(`/tickets/${ticketId}/messages`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Socket-Id': window.Echo ? window.Echo.socketId() : '',
                'Accept': 'application/json'
            },
            body: formData
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
            if (data.success) {
                appendMessage(data.message);
                chatInput.value = '';
                fileUpload.value = '';
                filePreview.innerText = '';
            }
        })
        .catch(err => console.error('[Chat] sendMessage failed:', err));
    }

    fileUpload.addEventListener('change', function() {
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

    // Lightbox Logic
    const attachments = @json($ticket->attachments->map(function($a) {
        return [
            'url' => Storage::url($a->file_path),
            'name' => $a->original_name,
            'mime' => $a->mime_type
        ];
    }));

    let currentIndex = 0;
    const lightbox = document.getElementById('lightbox');
    const lightboxMain = document.getElementById('lightboxMain');
    const lightboxCounter = document.getElementById('lightboxCounter');

    function openLightbox(index) {
        currentIndex = index;
        updateLightbox();
        lightbox.style.display = 'flex';
    }

    function changeAttachment(step) {
        currentIndex = (currentIndex + step + attachments.length) % attachments.length;
        updateMainDisplay();
    }

    function updateMainDisplay() {
        const att = attachments[currentIndex];
        const container = document.getElementById('mainAttachmentContainer');
        const dots = document.querySelectorAll('.carousel-dots .dot');

        container.innerHTML = '';
        if (att.mime.startsWith('image/')) {
            container.innerHTML = `<img src="${att.url}" alt="Attachment" style="width: 100%; height: 100%; object-fit: cover;">`;
        } else if (att.mime.startsWith('video/')) {
            container.innerHTML = `
                <div class="video-placeholder" style="display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-circle-play" style="font-size: 3rem; color: #4b5563;"></i>
                    <p style="margin-left: 10px; margin-bottom: 0; color: #4b5563; font-weight: bold;">Click to play video</p>
                </div>`;
        } else {
            container.innerHTML = `
                <div class="file-placeholder" style="text-align: center;">
                    <i class="fa-solid fa-file" style="font-size: 4rem; color: #4b5563;"></i>
                    <p style="margin-top: 10px; color: #4b5563; font-weight: bold;">${att.name}</p>
                </div>`;
        }

        // Update dots
        dots.forEach((dot, idx) => {
            if (idx === currentIndex) {
                dot.style.background = '#4b5563'; // Active dot
            } else {
                dot.style.background = '#d1d5db'; // Inactive dot
            }
        });
    }

    function closeLightbox() {
        lightbox.style.display = 'none';
        lightboxMain.innerHTML = '';
    }

    function changeLightbox(step) {
        currentIndex = (currentIndex + step + attachments.length) % attachments.length;
        updateLightbox();
    }

    function updateLightbox() {
        const att = attachments[currentIndex];
        lightboxMain.innerHTML = '';
        lightboxCounter.innerText = `${currentIndex + 1}/${attachments.length}`;

        if (att.mime.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = att.url;
            lightboxMain.appendChild(img);
        } else if (att.mime.startsWith('video/')) {
            const video = document.createElement('video');
            video.src = att.url;
            video.controls = true;
            video.autoplay = true;
            lightboxMain.appendChild(video);
        } else {
            lightboxMain.innerHTML = `<div style="text-align:center; color:white;"><i class="fa-solid fa-file" style="font-size:5rem;"></i><p style="margin-top:1rem;">${att.name}</p><a href="${att.url}" download style="color:#3b82f6; text-decoration:underline;">Download File</a></div>`;
        }
    }

    // Close lightbox on click outside
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) closeLightbox();
    });
  </script>
<script>
        const menuToggle = document.getElementById("menuToggle");
        const sidebar = document.querySelector(".sidebar-nav");
        const overlay = document.getElementById("sidebarOverlay");
        
        if (menuToggle && sidebar && overlay) {
            menuToggle.addEventListener("click", () => {
                sidebar.classList.toggle("active");
                overlay.classList.toggle("show");
            });
            overlay.addEventListener("click", () => {
                sidebar.classList.remove("active");
                overlay.classList.remove("show");
            });
        }
    </script>
</body>
</html>
