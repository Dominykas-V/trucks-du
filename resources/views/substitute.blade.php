@extends('main')
@section('content')
    <div class="text-end">
        <a href="{{ route('home') }}">Truck crud<i class="bi bi-arrow-right"></i></a>
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
                <th class="">ID</th>
                <th class="">Main Truck</th>
                <th class="">Substitute Truck</th>
                <th class="">Date from</th>
                <th class="">Date to</th>
                <th class="text-center" style="min-width: 160px">Acction</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subunit as $sub)
                <tr>
                    <th scope="row">{{ $sub->id }}</th>
                    <td>{{ $sub->main_truck }}</td>
                    <td>{{ $sub->subunit }}</td>
                    <td>{{ $sub->start_date }}</td>
                    <td>{{ $sub->end_date }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                            data-bs-target="#substituteTruckModal" onclick="setEditFields({{ $sub }})"><i
                                class="bi bi-pencil"></i></button>
                        <form action="{{ route('delete-sub', ['id' => $sub->id]) }}" method="POST" class="d-inline-block">
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
            <div class="col">{!! $subunit->links() !!}</div>
            <div class="col"><button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                    data-bs-target="#substituteTruckModal"><i class="bi bi-plus-circle"></i></button></div>
        </div>
    </div>
    {{-- start substitute truck modal --}}
    <div class="modal fade" id="substituteTruckModal" tabindex="-1" aria-labelledby="substituteTruckModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('post-sub') }}" method="POST" id="modalForm">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Substitute Truck <span id="editing_sub_id_text"></span></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <input type="hidden" id="substitute_id" name="substitute_id">
                            <label for="truck_year" class="form-label">Main truck</label>
                            <select class="form-select" aria-label="Main truck select" id="main_truck" name="main_truck">
                                <option disabled selected value> Select truck</option>
                                @foreach ($trucks as $truck)
                                    <option>{{ $truck->unit_number }}</option>
                                @endforeach
                            </select>
                            <label for="truck_year" class="form-label mt-3">Substitute truck</label>
                            <select class="form-select" aria-label="Substitute truck select" id="subunit" name="subunit">
                                <option disabled selected value> Select truck</option>
                                @foreach ($trucks as $truck)
                                    <option>{{ $truck->unit_number }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger" id="subunit_some_values_error" style="display: none">
                                <p>Can't replace the same truck with the same truck.</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <label for="start_date" class="form-label">Start date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="2023-01-01">
                            </div>
                            <div class="col">
                                <label for="end_date" class="form-label">End date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="2023-01-01">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="save_btn">Save substitute</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- end substitute truck modal --}}
@endsection

@section('css')
    <style></style>
@endsection

@section('scripts')
    <script>
        resetFields();
        //resets input fields every time modal closes
        document.getElementById('substituteTruckModal').addEventListener('hidden.bs.modal', event => {
            resetFields();
        })
        //displays error if the same truck is selected twice.
        $("#main_truck").on("change", function() {
            unitFieldSimilarityCheck();
        });
        $("#subunit").on("change", function() {
            unitFieldSimilarityCheck();
        });

        //displays error if the same truck is selected twice.
        function unitFieldSimilarityCheck() {
            if ($("#main_truck").val() == $("#subunit").val() && $("#main_truck").val() != null && $("#subunit").val() !=
                null) {
                $("#subunit_some_values_error").css("display", "");
                $("#main_truck").addClass("is-invalid");
                $("#subunit").addClass("is-invalid");
                $('#save_btn').prop('disabled', true);
            } else {
                $("#subunit_some_values_error").css("display", "none");
                $("#main_truck").removeClass("is-invalid");
                $("#subunit").removeClass("is-invalid");
                $('#save_btn').prop('disabled', false);
            }
        }

        function setEditFields(subData) {
            resetFields();
            $('#modalForm').attr('action', "{{ route('update-sub') }}");
            $("#editing_sub_id_text").text("| Editing - " + subData.id);
            $("#substitute_id").val(subData.id);
            $("#main_truck").val(subData.main_truck);
            $("#subunit").val(subData.subunit);
            $("#start_date").val(subData.start_date);
            $("#end_date").val(subData.end_date);
        }

        function resetFields() {
            $('#modalForm').attr('action', "{{ route('post-sub') }}");
            $("#editing_sub_id_text").text("");
            $("#substitute_id").val(null);
            $("#main_truck").val(null);
            $("#subunit").val(null);
            $("#start_date").val(getCurentDate());
            $("#end_date").val(getCurentDate());
            unitFieldSimilarityCheck();
        }
        //gets formated current date (inseted if 2023-1-1 => 2023-01-01)
        function getCurentDate() {
            return `${new Date().getFullYear()}-${(new Date().getMonth() + 1 < 10) ? "0" + String(new Date().getMonth() + 1) : new Date().getMonth() + 1}-${(new Date().getDate() < 10) ? "0" + String(new Date().getDate()) : new Date().getDate()}`;
        }
    </script>
@endsection
