<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        $property = new Property();
        $property->name = $request->input('name');
        $property->description = $request->input('description');
        $property->owner_user_id = Auth::user()->id;

        $property->save();

        return response()->json($property, 200);
    }

    public function list() {
        $properties = Property::where('owner_user_id', Auth::user()->id)->paginate();

        return response()->json($properties, 200);
    }

    public function detail($id) {
        $property = Property::where('owner_user_id', Auth::user()->id)
            ->where('id', $id)
            ->with('owner')
            ->first();

        return response()->json($property, 200);
    }

    public function delete($id) {
        Property::where('owner_user_id', Auth::user()->id)
            ->where('id', $id)
            ->delete();

        return response()->json([
            "message" => "property deleted successfully",
        ], 200);
    }
}

?>
