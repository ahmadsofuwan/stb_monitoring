@extends('layouts.app', ['title' => 'Backbone'])

@section('style')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('wrapper')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Devices</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Devices</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <h6 class="text-uppercase mb-0">Devices</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table-striped table-bordered table">
                            <thead>
                                <tr>
                                    <th><input type="text" class="form-control" placeholder="Search Android Id"></th>
                                    <th><input type="text" class="form-control" placeholder="Search Mac Address"></th>
                                    <th><input type="text" class="form-control" placeholder="Search Status"></th>
                                    <th><input type="text" class="form-control" placeholder="Search Last Active At"></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>Android Id</th>
                                    <th>Mac Address</th>
                                    <th>Status</th>
                                    <th>Last Active At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->
@endsection
@push('modal')
    <div>
        <div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="text-primary mb-0">Devices</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="row g-3" id="form" action="{{ route('devices.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <label for="inputLastName" class="form-label">Script</label>
                                <textarea class="form-control" name="script"
                                    value="{{ old('script') }}"></textarea>
                                @error('script')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-5">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endpush
@section('script')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('devices.index') }}",
                columns: [{
                        data: 'android_id',
                        name: 'android_id'
                    },
                    {
                        data: 'mac_address',
                        name: 'mac_address',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data == 'online') {
                                return `<span class="badge bg-success">Online</span>`;
                            } else {
                                return `<span class="badge bg-danger">Offline</span>`;
                            }
                        }
                    },
                    
                    {
                        data: 'last_active_at',
                        name: 'last_active_at',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                lengthChange: true,
                drawCallback: function() {
                    $('.delete').click(function() {
                        var id = $(this).data('id');
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: "{{ route('devices.destroy', '') }}/" +
                                        id,
                                    type: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        Swal.fire(
                                            'Deleted!',
                                            response.message,
                                            'success'
                                        );
                                        table.ajax.reload();
                                    },
                                    error: function(response) {
                                        Swal.fire({
                                            title: 'Gagal',
                                            text: response.responseJSON.message,
                                            icon: 'error'
                                        });
                                    }
                                });
                            }
                        });
                    });
                    $(".edit").click(function() {
                        var id = $(this).data('id');
                        console.log(id);
                        $('#modalAdd').modal('show');
                        $("#form").attr('action', "{{ route('devices.update', '') }}/" + id);
                        if ($("#form input[name='_method']").length === 0) {
                            $("#form").append(
                                '<input type="hidden" name="_method" value="PUT">');
                        }

                        $.ajax({
                            url: "{{ route('devices.show', '') }}/" + id,
                            type: 'GET',
                            success: function(response) {
                                $.each(response, function(i, val) {
                                    $(`input[name='${i}']`).val(val);
                                    if ($(`select[name='${i}']`).length) {
                                        $(`select[name='${i}']`).val(val)
                                            .change();
                                    }
                                    if ($(`textarea[name='${i}']`).length) {
                                        $(`textarea[name='${i}']`).text(val);
                                    }
                                });
                            }
                        });




                    });
                }
            });

            table.buttons().container()
                .appendTo('#dataTable_wrapper .col-md-6:eq(0)');
            $("#add").click(function() {
                $("#form").attr('action', "{{ route('devices.store') }}");
                $("input[name='_method']").remove();
            })

            $('#dataTable thead input').on('keyup change', function() {
                table.column($(this).parent().index())
                    .search(this.value)
                    .draw();
            });

        });
    </script>
    @if ($errors->any())
        <script>
            $(document).ready(function() {
                $('#modalAdd').modal('show');
            });
        </script>
        @if (session('form_status') == 'edit')
            <script>
                $(document).ready(function() {
                    let id = "{{ session('form_id') }}"
                    $("#form").attr('action', "{{ route('devices.update', '') }}/" + id);
                    if ($("#form input[name='_method']").length === 0) {
                        $("#form").append(
                            '<input type="hidden" name="_method" value="PUT">');
                    }
                });
            </script>
        @endif
    @endif
@endsection
