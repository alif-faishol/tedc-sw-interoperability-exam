<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\UnitGroup;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class UnitGroupController extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'required|string',
            'hourly_price' => 'required|integer',
            'property_id' => 'required|exists:properties,id',
            'available_to_public' => 'boolean',
            'unitIds.*' => 'exists:units,id'
        ]);

        if (!Property::where('owner_user_id', Auth::user()->id)
            ->where('id', $request->input('property_id'))
            ->first()) {
            abort(403, 'You can only add unit group to your property');
        }

        foreach ($request->input('unitIds') as $unitId) {
            if (!Unit::where('id', $unitId)->where('property_id', $request->input('property_id'))->first()) {
                abort(403, 'You can only add unit from same property as unit group');
            }
        }

        $unitGroup = new UnitGroup();
        $unitGroup->name = $request->input('name');
        $unitGroup->description = $request->input('description');
        $unitGroup->hourly_price = $request->input('hourly_price');
        $unitGroup->property_id = $request->input('property_id');
        if ($availableToPublic = $request->input('available_to_public')) {
            $unitGroup->available_to_public = $availableToPublic;
        }

        $unitGroup->save();

        foreach ($request->input('unitIds') as $unitId) {
            $unit = Unit::find($unitId);
            $unit->unit_group_id = $unitGroup->id;
            $unit->save();
        }

        return response()->json($unitGroup, 200);
    }

    public function list() {
        $unitGroups = UnitGroup::whereIn(
            'property_id',
            Property::where('owner_user_id', Auth::user()->id)->get()
        )
            ->with(['units', 'property'])
            ->paginate();

        return response()->json($unitGroups, 200);
    }

    public function detail($id) {
        $unitGroup = UnitGroup::where('id', $id)
            ->whereIn(
                'property_id',
                Property::where('owner_user_id', Auth::user()->id)->get()
            )
            ->with('property', 'units')
            ->first();

        return response()->json($unitGroup, 200);
    }

    public function delete($id) {
        $deletedRows = UnitGroup::where('id', $id)
            ->whereIn(
                'property_id',
                Property::where('owner_user_id', Auth::user()->id)->get()
            )
            ->delete();

        if ($deletedRows === 0) abort(400, "You don't have unit with specified id");

        return response()->json([
            "message" => "unit group deleted successfully",
        ], 200);
    }
}

?>
