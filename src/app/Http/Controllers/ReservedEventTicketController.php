<?php

namespace App\Http\Controllers;

use App\Models\ReservedEventTicket;
use Illuminate\Support\Facades\Auth;

class ReservedEventTicketController extends Controller
{
    public function list() {
        $tickets = ReservedEventTicket::where(
            'user_id',
            Auth::user()->id
        )
            ->with(['event', 'unit'])
            ->paginate();

        return response()->json($tickets, 200);
    }

    public function detail($id) {
        $ticket = ReservedEventTicket::where('id', $id)
            ->where(
                'user_id',
                Auth::user()->id
            )
            ->with(['event', 'unit.unit_group.property', 'user'])
            ->paginate();

        return response()->json($ticket, 200);
    }

    public function delete($id) {
        $deletedRows = ReservedEventTicket::where('id', $id)
            ->where(
                'user_id',
                Auth::user()->id
            )
            ->delete();

        if ($deletedRows === 0) abort(400, "You don't have ticket with specified id");

        return response()->json([
            "message" => "ticket deleted successfully",
        ], 200);
    }
}

?>
