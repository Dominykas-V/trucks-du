<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Truck;

class TrucksController extends Controller
{
    public function index()
    {
        $trucks = Truck::orderBy('created_at', 'desc')->paginate(10);
        return view('home', compact('trucks'));
    }

    public function validateTruckData(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'unit_number' => 'sometimes|string|max:255|unique:trucks|required',
                'year' => 'integer|required',
                'notes' => 'string|nullable|max:255',
            ]
        );

        if ($request->year < 1900 || $request->year > date("Y") + 5 || $validator->fails()) {
            return 'Form validation error.';
        }
        return 'valid';
    }

    public function create(Request $request)
    {
        $formErrors = $this->validateTruckData($request);
        if ($formErrors == 'valid') {
            $Truck = new Truck();
            $Truck->unit_number = $request->unit_number;
            $Truck->year = $request->year;
            $Truck->notes = $request->notes;
            $Truck->save();
            return redirect()->back()->with('success', 'Truck added successfully.');
        }
        return redirect()->back()->with('error', 'Truck could not be added. ' . $formErrors);
    }
    public function update(Request $request)
    {
        $request->merge(['new_unit_number' => $request->unit_number]);
        $request->request->remove('unit_number');

        $formErrors = $this->validateTruckData($request);
        if ($formErrors == 'valid') {
            $truck = Truck::findOrFail($request->og_unit_number);
            $truck->update([
                'unit_number' => $request->new_unit_number,
                'year' => $request->year,
                'notes' => $request->notes,
            ]);
            return redirect()->back()->with('success', 'Truck successfully updated.');
        }
        return redirect()->back()->with('error', 'Truck could not be updated. ' . $formErrors);
    }
    public function destroy($id)
    {
        $truck = Truck::findOrFail($id);
        $truck->delete();
        return redirect()->back()->with('success', 'Truck ' . $id . ' successfully deleted.');
    }
}
