<?php

namespace App\Repositories;

use App\Repositories\Contracts\TicketRepositoryInterface;
use App\Models\Ticket;

class TicketRepository implements TicketRepositoryInterface
{
    public function getPopularTickets($limit = 4)
    {
        return Ticket::where('is_popular', true)->take($limit)->get();
    }
    public function getAllNewTickets()
    {
        return Ticket::latest()->get();
    }

    public function find($id)
    {
        return Ticket::find($id);
    }

    public function getPrice($ticketId)
    {
        $ticket = Ticket::find($ticketId);

        return $ticketId ? $ticket->price : 0;
    }
}
