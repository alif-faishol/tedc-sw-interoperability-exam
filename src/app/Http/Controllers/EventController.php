<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\UnitGroup;
use App\Models\Property;
use App\Models\ReservedEventTicket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    private function validateUnitGroupIds($unitGroupIds, $startDatetime, $endDatetime) {
        $unitGroups = UnitGroup::whereIn('id', $unitGroupIds)->get();
        $propertyId = $unitGroups[0]->property_id;
        foreach ($unitGroups as $unitGroup) {
            if ($unitGroup->property_id != $propertyId) {
                abort(403, "Unit Group can't be from different property from each other");
            }
            if (Event::where('start_datetime', '<=', $endDatetime)
                ->where('end_datetime', '>=', $startDatetime)
                ->first()) {
                abort(403, "There's already event in the schedule for Unit Group " . $unitGroup->id);
            }
            if (!$unitGroup->available_to_public) {
                // Allows to rent your own Unit Group
                if (Property::where('id', $propertyId)
                    ->where('owner_user_id', Auth::user()->id)
                    ->first()) {
                    continue;
                }
                abort(403, "Unit Group " . $unitGroup->id . " is not available for rent");
            }
        }
    }

    public function store(Request $req) {
        $this->validate($req, [
            'name' => 'required|string',
            'description' => 'required|string',
            'ticket_price' => 'required|integer',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'unitGroupIds' => 'required|array',
            'unitGroupIds.*' => 'exists:unit_groups,id'
        ]);

        $this->validateUnitGroupIds(
            $req->input('unitGroupIds'),
            $req->input('start_datetime'),
            $req->input('end_datetime')
        );

        $event = new Event();
        $event->name = $req->input('name');
        $event->description = $req->input('description');
        $event->ticket_price = $req->input('ticket_price');
        $event->start_datetime = $req->input('start_datetime');
        $event->end_datetime = $req->input('end_datetime');
        $event->tenant_user_id = Auth::user()->id;
        $event->save();

        $event->unitGroups()->sync($req->input('unitGroupIds'));
        return response()->json($event, 200);
    }

    public function update(Request $req, $id) {
        $this->validate($req, [
            'name' => 'string',
            'description' => 'string',
            'ticket_price' => 'integer',
            'start_datetime' => 'date',
            'end_datetime' => 'date|after:start_datetime',
            'unitGroupIds' => 'array',
            'unitGroupIds.*' => 'exists:unit_groups,id'
        ]);

        $event = Event::where('id', $id)
            ->where('tenant_user_id', Auth::user()->id)->first();

        if (!$event) abort(404, 'Event not found!');

        if ($req->input('name'))
            $event->name = $req->input('name');
        if ($req->input('description'))
            $event->name = $req->input('description');
        if ($req->input('ticket_price'))
            $event->name = $req->input('ticket_price');
        if ($req->input('start_datetime'))
            $event->name = $req->input('start_datetime');
        if ($req->input('end_datetime'))
            $event->name = $req->input('end_datetime');

        $event->save();

        if ($req->input('unitGroupIds')) {
            $this->validateUnitGroupIds(
                $req->input('unitGroupIds'),
                $event->start_datetime,
                $event->end_datetime
            );
            $event->unitGroups()->sync($req->input('unitGroupIds'));
        }

        return response()->json($event, 200);
    }

    public function list(Request $req) {
        $events = new Event();
        $events = $events->with(['unitGroups.property', 'tenant']);

        $initiator = $req->query('initiator');
        if ($initiator) {
            if ($initiator == 'me') {
                $events = $events->where('tenant_user_id', Auth::user()->id);
            } else {
                $events = $events->where('tenant_user_id', $initiator);
            }
        }
        $dateRangeStart = $req->query('drs');
        if ($dateRangeStart) {
            $events = $events->where(
                'start_datetime',
                '>=',
                $dateRangeStart
            );
        }
        $dateRangeEnd = $req->query('dre');
        if ($dateRangeEnd) {
            $events = $events->where(
                'start_datetime',
                '<=',
                $dateRangeEnd
            );
        }

        $events = $events->paginate();

        return response()->json($events, 200);
    }

    public function detail($id) {
        $event = Event::where('id', $id)
            ->with(['unitGroups.property', 'tenant', 'unitGroups.units:id,unit_group_id,name'])
            ->first();

        if (!$event) abort(404, 'Event not found');

        // Check ticket availability
        foreach($event->unitGroups as $unitGroup) {
            foreach($unitGroup->units as $unit) {
                $unit->ticket_sold = false;
                if (
                    ReservedEventTicket::where('unit_id', $unit->id)
                        ->where('event_id', $event->id)
                    ->first()
                ) $unit->ticket_sold = true;
            }
        }

        return response()->json($event, 200);
    }

    public function delete($id) {
        $deletedRows = Event::where('id', $id)
            ->where('tenant_user_id', Auth::user()->id)
            ->delete();

        if ($deletedRows === 0) abort(400, "Specified event not found or you don't have access to it");

        return response()->json([
            "message" => "event deleted successfully",
        ], 200);
    }

    public function buyTicket(Request $req, $event_id) {
        $this->validate($req, [
            'unit_group_id' => 'exists:unit_groups,id',
            'unit_id' => 'exists:units,id'
        ]);

        if ($req->input('unit_id') && !$req->input('unit_group_id')) {
            abort(403, 'unit_group_id is required when unit_id is specified');
        }

        $event = Event::where('id', $event_id)
            ->with('unitGroups.units')->first();

        if (!$event) {
            abort(404, 'Event not found!');
        }

        if ($event->start_datetime < Carbon::now()) {
            abort(403, 'Event already passed!');
        }

        if ($req->input('unit_group_id')) {
            $unitGroup = null;
            foreach ($event->unitGroups as $uG) {
                if ($uG->id == $req->input('unit_group_id')) {
                    $unitGroup =  $uG;
                }
            }

            if (!$unitGroup) {
                abort(403, 'Specified Unit Group is not available in this event');
            }

            $availableUnits = [];
            foreach($unitGroup->units as $unit) {
                if (!ReservedEventTicket::where('unit_id', $unit->id)
                    ->where('event_id', $event->id)
                    ->first()
                ) {
                    array_push($availableUnits, $unit);
                }
            }

            if (count($availableUnits) < 1) {
                abort(403, "There's no Unit available in the specified Unit Group");
            }

            $unit = $availableUnits[0];

            if ($req->input('unit_id')) {
                foreach ($availableUnits as $aU) {
                    if ($aU->id == $req->input('unit_id')) {
                        $unit = $aU;
                    }
                }
                if ($unit->id != $req->input('unit_id')) {
                    abort(403, 'Specified unit is not available');
                }
            }

            $ticket = new ReservedEventTicket();
            $ticket->unit_id = $unit->id;
            $ticket->event_id = $event->id;
            $ticket->user_id = Auth::user()->id;

            $ticket->save();

            $ticket = ReservedEventTicket::find($ticket->id)
                ->with(['unit', 'event', 'user'])
                ->first();

            return response()->json($ticket, 200);
        }

        $availableUnit = null;
        foreach ($event->unitGroups as $unitGroup) {
            foreach ($unitGroup->units as $unit) {
                if (!ReservedEventTicket::where('unit_id', $unit->id)
                    ->where('event_id', $event->id)
                    ->first()
                ) {
                    $availableUnit = $unit;
                    break;
                }
            }
            if ($availableUnit) break;
        }

        if (!$availableUnit) abort(403, 'All tickets for this event were sold out');

        $ticket = new ReservedEventTicket();
        $ticket->unit_id = $availableUnit->id;
        $ticket->event_id = $event->id;
        $ticket->user_id = Auth::user()->id;

        $ticket->save();

        $ticket = ReservedEventTicket::find($ticket->id)
            ->with(['unit', 'event', 'user'])
            ->first();

        return response()->json($ticket, 200);
    }
}

?>
