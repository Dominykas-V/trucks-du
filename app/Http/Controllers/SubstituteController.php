<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\TrucksSubunit;
use Illuminate\Http\Request;
use App\Models\Truck;

class SubstituteController extends Controller
{
    public function index()
    {
        $trucks = Truck::all();
        $subunit = TrucksSubunit::orderBy('created_at', 'desc')->paginate(10);
        return view('substitute', compact('trucks', 'subunit'));
    }

    public function validateSubstituteData(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'main_truck' => 'string|exists:trucks,unit_number|required',
                'subunit' => 'string|exists:trucks,unit_number|different:main_truck|required',
                'start_date' => 'date|required',
                'end_date' => 'date|required',
            ]
        );

        if ($request->start_date > $request->end_date || $validator->fails()) {
            return 'Form validation error.';
        }

        // =SOLUTION (80%) ATTEMPTED USING ONLY SQL REQUEST=
        // $substitutedOverlap = TrucksSubunit::where(function ($query) use ($request) {
        //     $query->where('start_date', '<=', $request->end_date)
        //         ->where('end_date', '>=', $request->start_date);
        // })
        //     ->where(function ($query) use ($request) {
        //         $query->where('main_truck', $request->main_truck)
        //             ->orWhere('subunit', $request->subunit)
        //             ->where('main_truck', $request->subunit)
        //             ->orWhere('subunit', $request->main_truck);
        //     })
        //     ->get();

        // if (!$substitutedOverlap->isEmpty() && !$substitutedOverlap->contains('id', $request->substitute_id)) {
        //     return 'Replacement dates of the trucks overlap or substitute truck already replaces another truck.';
        // }

        // =REAL SOLUTION USING SQL REQUEST AND FOR EACH (better debug imo)=
        $substitutedOverlap = TrucksSubunit::where('start_date', '<=', $request->end_date)
            ->where('end_date', '>=', $request->start_date)
            ->get();

        foreach ($substitutedOverlap as $sub) {
            if ($sub->id != $request->substitute_id) {
                if ($sub->main_truck == $request->main_truck) {
                    return $request->main_truck . ' truck is already being replaced by ' . $sub->subunit . ' in this date range.';
                }
                if ($sub->subunit == $request->main_truck) {
                    return 'Truck ' . $request->main_truck . ' already replaces truck ' . $sub->main_truck . ', so it cannot be replaced by truck ' . $request->subunit . '.';
                }
                if ($sub->main_truck == $request->subunit) {
                    return 'Truck ' . $request->subunit . ' is being replaced by ' . $sub->subunit . ', so it cannot be used as a substitute.';
                }
            }
        }

        return 'valid';
    }

    public function create(Request $request)
    {
        $formErrors = $this->validateSubstituteData($request);
        if ($formErrors == 'valid') {
            $TruckSub = new TrucksSubunit();
            $TruckSub->main_truck = $request->main_truck;
            $TruckSub->subunit = $request->subunit;
            $TruckSub->start_date = $request->start_date;
            $TruckSub->end_date = $request->end_date;
            $TruckSub->save();
            return redirect()->back()->with('success', 'Truck substitute added successfully.');
        }
        return redirect()->back()->with('error', 'Truck substitute could not be added. ' . $formErrors);
    }

    public function update(Request $request)
    {
        $formErrors = $this->validateSubstituteData($request);
        if ($formErrors == 'valid') {
            $TruckSub = TrucksSubunit::findOrFail($request->substitute_id);
            $TruckSub->update([
                'main_truck' => $request->main_truck,
                'subunit' => $request->subunit,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            return redirect()->back()->with('success', 'Truck substitute successfully updated.');
        }
        return redirect()->back()->with('error', 'Truck substitute could not be updated. ' . $formErrors);
    }
    public function destroy($id)
    {
        $TruckSub = TrucksSubunit::findOrFail($id);
        $TruckSub->delete();
        return redirect()->back()->with('success', 'Truck substitute successfully removed.');
    }
}
