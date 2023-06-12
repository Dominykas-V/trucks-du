@extends('main')
@section('content')
    @if (session()->has('success'))
        <label class="alert alert-success w-100">{{ session('success') }}</label>
    @elseif(session()->has('error'))
        <label class="alert alert-danger w-100">{{ session('error') }}</label>
    @endif
    {{-- Content --}}
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Unit number</th>
                <th scope="col">Year</th>
                <th scope="col">Notes</th>
                <th scope="col">Acction</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trucks as $truck)
                <tr>
                    <th scope="row">{{ $truck->unit_number }}</th>
                    <td>{{ $truck->year }}</td>
                    <td>{{ $truck->notes }}</td>
                    <td>
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                            data-bs-target="#editTruckModal" onclick="setEditFields({{ $truck }})"><i
                                class="bi bi-pencil"></i></button>

                        <form action="{{ route('delete-truck', ['unit_number' => $truck->unit_number]) }}" method="POST"
                            class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="float-end">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
            data-bs-target="#createNewTruckModal"><i class="bi bi-plus-circle"></i></button>
    </div>
    {{-- Create new truck modal --}}
    <div class="modal fade" id="createNewTruckModal" tabindex="-1" aria-labelledby="createNewTruckModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('post-truck') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="createNewTruckModalLabel">Create new Truck</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="unit_number" class="form-label">Unit number*</label>
                            <input type="text" class="form-control" id="unit_number" name="unit_number"
                                aria-describedby="help_unit_number">
                            <div id="help_unit_numbe" class="form-text">Tai sunkvežimio identifikacinis numeriukas,
                                pavyzdžiui A1578, 8050, 147859.</div>
                        </div>
                        <div class="mb-3">
                            <label for="truck_year" class="form-label">Year*</label>
                            <input type="number" class="form-control" id="truck_year" name="year"
                                aria-describedby="help_truck_year">
                            <div class="text-danger" id="unsuported_year_error" style="display: none">
                                <p>Provided invalid year.</p>
                            </div>
                            <div id="help_truck_year" class="form-text">Tai sukvežimio pirmos registracijos metai. Leidžiame
                                vesti reikšmes nuo 1900 iki ne toliau nei +5 metai
                                nuo dabar.</div>
                        </div>
                        <div class="mb-3">
                            <label for="notes_area" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes_area" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add new truck</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- end new truck modal --}}
    {{-- edit truck modal --}}
    <div class="modal fade" id="editTruckModal" tabindex="-1" aria-labelledby="editTruckModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('update-truck') }}" method="POST">
                    @csrf
                    <input type="hidden" id="edit_truck_id" name="truck_id">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editTruckModal">Update Truck</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <h4 class="text-center" id="edit_truck_name">Truck name</h4>
                        </div>
                        <div class="mb-3">
                            <label for="truck_year" class="form-label">Year*</label>
                            <input type="number" class="form-control" id="edit_truck_year" name="year"
                                aria-describedby="help_truck_year">
                            <div class="text-danger" id="edit_unsuported_year_error" style="display: none">
                                <p>Provided invalid year.</p>
                            </div>
                            <div id="help_truck_year" class="form-text">Tai sukvežimio pirmos registracijos metai.
                                Leidžiame
                                vesti reikšmes nuo 1900 iki ne toliau nei +5 metai
                                nuo dabar.</div>
                        </div>
                        <div class="mb-3">
                            <label for="notes_area" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_notes_area" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update truck</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- end edit truck modal --}}
@endsection

@section('css')
    <style>

    </style>
@endsection

@section('scripts')
    <script>

        $("#truck_year").val(new Date().getFullYear());
        // year validation checks && error display | create new
        $("#truck_year").on("change", function() {
            if (isBetween($("#truck_year").val(), 1900, new Date().getFullYear() + 5)) {
                $("#unsuported_year_error").css("display", "none");
                $("#truck_year").removeClass("is-invalid");
            } else {
                $("#unsuported_year_error").css("display", "");
                $("#truck_year").addClass("is-invalid");
            }
        });
        // year validation checks && error display | edit
        $("#edit_truck_year").on("change", function() {
            if (isBetween($("#edit_truck_year").val(), 1900, new Date().getFullYear() + 5)) {
                $("#edit_unsuported_year_error").css("display", "none");
                $("#edit_truck_year").removeClass("is-invalid");
            } else {
                $("#edit_unsuported_year_error").css("display", "");
                $("#edit_truck_year").addClass("is-invalid");
            }
        });

        function isBetween(curr, min, max) {
            if (curr >= min && curr <= max) {
                return true;
            }
            return false;
        }
        // 
        function setEditFields(truck) {
            $("#edit_truck_name").text(truck.unit_number);
            $("#edit_truck_id").val(truck.unit_number);
            $("#edit_truck_year").val(truck.year);
            $("#edit_notes_area").val(truck.notes);
        }
    </script>
@endsection
