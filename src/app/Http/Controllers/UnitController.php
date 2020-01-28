<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'required|string',
            'property_id' => 'required|exists:properties,id',
            'unit_group_id' => 'exists:unit_groups,id',
            'event_only' => 'boolean',
            'hourly_price' => 'required|integer',
            'daily_price' => 'required|integer',
            'monthly_price' => 'required|integer',
            'yearly_price' => 'required|integer',
            'available_to_public' => 'boolean'
        ]);

        if (!Property::where('owner_user_id', Auth::user()->id)
            ->where('id', $request->input('property_id'))
            ->first()) {
            abort(403, 'You can only add unit to your property');
        }

        $unit = Unit::create($request->all());

        return response()->json($unit, 200);
    }

    public function list() {
        $units = Unit::whereIn(
            'property_id',
            Property::where('owner_user_id', Auth::user()->id)->get()
        )
            ->with(['property', 'unit_group'])
            ->paginate();

        return response()->json($units, 200);
    }

    public function forRentList(Request $req) {
        $units = Unit::with(['property'])
            ->where('unit_group_id', null);
        if ($req->query('property_id')) {
            $units = $units->where('property_id', $req->query('property_id'));
        }
        $units = $units->paginate();

        return response()->json($units, 200);
    }

    public function detail($id) {
        $unit = Unit::where('id', $id)
            ->whereIn(
                'property_id',
                Property::where('owner_user_id', Auth::user()->id)->get()
            )
            ->with('property', 'unit_group')
            ->first();

        return response()->json($unit, 200);
    }

    public function delete($id) {
        $deletedRows = Unit::where('id', $id)
            ->whereIn(
                'property_id',
                Property::where('owner_user_id', Auth::user()->id)->get()
            )
            ->delete();

        if ($deletedRows === 0) abort(400, "You don't have unit with specified id");

        return response()->json([
            "message" => "unit deleted successfully",
        ], 200);
    }
}

?>
