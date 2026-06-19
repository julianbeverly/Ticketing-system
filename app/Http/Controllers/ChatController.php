<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Ticket;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Fetch all messages for a specific ticket.
     */
    public function fetchMessages(Ticket $ticket)
    {
        // Update current user's last seen status
        if (Auth::check()) {
            Auth::user()->update(['last_seen_at' => now()]);
        }

        $messages = Message::with(['user', 'attachments'])
            ->where('ticket_id', $ticket->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'user_name' => $message->user->name,
                    'role' => $message->user->role,
                    'content' => $message->content,
                    'time' => $message->created_at->format('h:i A'),
                    'is_me' => $message->user_id === Auth::id(),
                    'attachments' => $message->attachments->map(function ($att) {
                        return [
                            'url' => Storage::url($att->file_path),
                            'name' => $att->file_name,
                            'type' => $att->file_type,
                        ];
                    }),
                ];
            });

        return response()->json([
            'messages' => $messages,
            'online_count' => $this->getOnlineCountForTicket($ticket)
        ]);
    }

    /**
 * Send a new message.
 */
public function sendMessage(Request $request, Ticket $ticket)
{
    $request->validate([
        'content' => 'nullable|string',
        'attachments' => 'nullable|array',
        'attachments.*' => 'file|max:51200', // 50MB max
    ]);

    if (empty($request->content) && !$request->hasFile('attachments')) {
        return response()->json([
            'error' => 'Message or attachment is required'
        ], 422);
    }

    $message = Message::create([
        'ticket_id' => $ticket->id,
        'user_id' => Auth::id(),
        'content' => $request->content,
    ]);

    // Save attachments
    if ($request->hasFile('attachments')) {

        foreach ($request->file('attachments') as $file) {

            $path = $file->store('chat_attachments', 'public');

            MessageAttachment::create([
                'message_id' => $message->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
            ]);
        }
    }

    // Load relationships
    $message->load(['user', 'attachments']);

    // Format the message data for broadcast (is_me = false for all receivers)
    $broadcastData = [
        'id'          => $message->id,
        'ticket_id'   => $ticket->id,
        'user_id'     => $message->user_id,
        'user_name'   => $message->user->name,
        'role'        => $message->user->role,
        'content'     => $message->content,
        'time'        => $message->created_at->format('h:i A'),
        'is_me'       => false,   // receivers always see it as "not mine"
        'attachments' => $message->attachments->map(function ($att) {
            return [
                'url'  => Storage::url($att->file_path),
                'name' => $att->file_name,
                'type' => $att->file_type,
            ];
        }),
    ];

    // Broadcast to all OTHER connected users on this ticket channel
    broadcast(new MessageSent($broadcastData))->toOthers();

    // Update last seen timestamp for the sender
    Auth::user()->update(['last_seen_at' => now()]);

    // Log the activity
    \App\Models\TicketActivity::create([
        'ticket_id'   => $ticket->id,
        'user_id'     => Auth::id(),
        'action'      => 'message_sent',
        'description' => 'sent a message',
    ]);

    // Return to the SENDER with is_me = true so their message renders on the right (sent) side
    $senderData           = $broadcastData;
    $senderData['is_me']  = true;

    return response()->json([
        'success' => true,
        'message' => $senderData,
    ]);
}

    /**
     * Get the count of online users (active in the last 5 minutes).
     */
    public function getOnlineCount(Request $request)
    {
        // If a ticket_id is provided, scope count to that ticket's participants
        if ($request->filled('ticket_id')) {
            $ticket = Ticket::find($request->ticket_id);
            if ($ticket) {
                return response()->json(['online_count' => $this->getOnlineCountForTicket($ticket)]);
            }
        }
        return response()->json(['online_count' => $this->getOnlineCountRaw()]);
    }

    /**
     * Count only participants of a specific ticket who are online.
     * Participants = the employee who owns the ticket + the assigned technician.
     */
    private function getOnlineCountForTicket(Ticket $ticket): int
    {
        $participantIds = array_filter([
            $ticket->user_id,
            $ticket->technician_id,
        ]);

        return User::whereIn('id', $participantIds)
            ->where('last_seen_at', '>', now()->subMinutes(5))
            ->count();
    }

    private function getOnlineCountRaw(): int
    {
        return User::where('last_seen_at', '>', now()->subMinutes(5))->count();
    }
}
