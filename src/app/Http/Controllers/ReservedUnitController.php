<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\ReservedUnit;
use Illuminate\Support\Facades\Auth;

class ReservedUnitController extends Controller
{
    public function store(Request $req) {
        $this->validate($req, [
            'unit_id' => 'required|exists:units,id',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
        ]);

        $unit = Unit::where('id', $req->input('unit_id'))->first();
        if ($unit->unit_group_id != null) {
            abort(403, 'This unit is not available to rent');
        }

        if (ReservedUnit::where('unit_id', $req->input('unit_id'))
            ->where('start_datetime', '<=', $req->input('end_datetime'))
            ->where('end_datetime', '>=', $req->input('start_datetime'))
            ->first()) {
            abort(403, 'Unit is not available');
        }

        $reservedUnit = new ReservedUnit();
        $reservedUnit->unit_id = $req->input('unit_id');
        $reservedUnit->start_datetime = $req->input('start_datetime');
        $reservedUnit->end_datetime = $req->input('end_datetime');
        $reservedUnit->tenant_user_id = Auth::user()->id;
        $reservedUnit->save();

        $reservedUnit = ReservedUnit::where('id', $reservedUnit->id)
            ->with(['unit', 'tenant'])
            ->first();

        return response()->json($reservedUnit, 200);
    }

    public function list() {
        $reservedUnits = ReservedUnit::where('tenant_user_id', Auth::user()->id)
            ->with(['unit.property', 'tenant'])
            ->paginate();

        return response()->json($reservedUnits, 200);
    }

    public function detail($id) {
        $reservedUnit = ReservedUnit::where('id', $id)
            ->where('tenant_user_id', Auth::user()->id)
            ->with(['unit.property', 'tenant'])
            ->first();

        if (!$reservedUnit) abort(404, 'Unit not found!');

        return response()->json($reservedUnit, 200);
    }

    public function delete($id) {
        $deletedRows = ReservedUnit::where('id', $id)
            ->where('tenant_user_id', Auth::user()->id)
            ->delete();

        if ($deletedRows === 0) abort(400, "You don't have reservved unit with specified id");

        return response()->json([
            "message" => "reserved unit deleted successfully",
        ], 200);
    }
}

?>
