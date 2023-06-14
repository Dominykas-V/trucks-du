@extends('main')
@section('content')
    <div class="text-end">
        <a href="{{ route('substitute') }}">Truck substitute<i class="bi bi-arrow-right"></i></a>
    </div>
    @if (session()->has('success'))
        <label class="alert alert-success w-100">{{ session('success') }}</label>
    @elseif(session()->has('error'))
        <label class="alert alert-danger w-100">{{ session('error') }}</label>
    @endif
    {{-- Content --}}
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Unit number</th>
                <th>Year</th>
                <th>Notes</th>
                <th class="text-center" style="min-width: 160px">Acction</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trucks as $truck)
                <tr>
                    <th scope="row">{{ $truck->unit_number }}</th>
                    <td>{{ $truck->year }}</td>
                    <td>{{ $truck->notes }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                            data-bs-target="#truckModal" onclick="setEditFields({{ $truck }})"><i
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
        <div class="row">
            <div class="col">{!! $trucks->links() !!}</div>
            <div class="col"><button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                    data-bs-target="#truckModal"><i class="bi bi-plus-circle"></i></button></div>
        </div>
    </div>
    {{-- Create new truck modal --}}
    <div class="modal fade" id="truckModal" tabindex="-1" aria-labelledby="truckModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('post-truck') }}" method="POST" id="modalForm">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="truckModalLabel">Create new Truck</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="unit_number" class="form-label">Unit number*</label>
                            <input type="text" class="form-control" id="unit_number" name="unit_number"
                                aria-describedby="help_unit_number">
                            <input type="hidden" id="og_unit_number" name="og_unit_number">
                            <div id="help_unit_numbe" class="form-text">This is the identification number of the truck, for
                                example A1578, 8050, 147859.</div>
                        </div>
                        <div class="mb-3">
                            <label for="truck_year" class="form-label">Year*</label>
                            <input type="number" class="form-control" id="truck_year" name="year"
                                aria-describedby="help_truck_year">
                            <div class="text-danger" id="unsuported_year_error" style="display: none">
                                <p>The provided year is out of range.</p>
                            </div>
                            <div id="help_truck_year" class="form-text">This is the year of the truck's first registration.
                                Values from 1900 to no more than +5 years from current date are allowed.</div>
                        </div>
                        <div class="mb-3">
                            <label for="notes_area" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes_area" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="save_btn">Save truck</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- end new truck modal --}}
@endsection

@section('css')
    <style>

    </style>
@endsection

@section('scripts')
    <script>
        resetFields();
        //resets input fields every time modal closes
        document.getElementById('truckModal').addEventListener('hidden.bs.modal', event => {
            resetFields();
        })
        // triggers checkFormErrors on year number input change
        $("#truck_year").on("change", function() {
            checkFormErrors();
        });
        // triggers checkFormErrors on unit number input change
        $("#unit_number").on("change", function() {
            checkFormErrors();
        });

        //displayes error msg if out of range year is selected or no unit name provided, disables/enables save button
        function checkFormErrors(editMode = false) {
            var truckYear = $("#truck_year").val();
            var unitNumber = $("#unit_number").val();

            var yearValid = isBetween(truckYear, 1900, new Date().getFullYear() + 5);
            var unitNumberValid = unitNumber !== '';

            if (yearValid) {
                $("#unsuported_year_error").css("display", "none");
                $("#truck_year").removeClass("is-invalid");
            } else {
                $("#unsuported_year_error").css("display", "");
                $("#truck_year").addClass("is-invalid");
            }

            if (unitNumberValid || editMode) {
                $("#unit_number").removeClass("is-invalid");
            } else {
                $("#unit_number").addClass("is-invalid");
            }

            var saveButtonDisabled = !(yearValid && (unitNumberValid || editMode));
            $('#save_btn').prop('disabled', saveButtonDisabled);
        }

        function isBetween(curr, min, max) {
            if (curr >= min && curr <= max) {
                return true;
            }
            return false;
        }

        function setEditFields(truck) {
            resetFields(true);
            $('#modalForm').attr('action', "{{ route('update-truck') }}");
            $("#truck_name").text('Truck | Editing - ' + truck.unit_number);
            $("#unit_number").val(truck.unit_number);
            $("#og_unit_number").val(truck.unit_number);
            $("#truck_year").val(truck.year);
            $("#notes_area").val(truck.notes);
        }

        function resetFields() {
            $('#modalForm').attr('action', "{{ route('post-truck') }}");
            $("#truck_name").text('Create new truck.');
            $("#unit_number").val(null);
            $("#og_unit_number").val(null);
            $("#truck_year").val(new Date().getFullYear());
            $("#notes_area").val(null);
            checkFormErrors(true);
        }
    </script>
@endsection
