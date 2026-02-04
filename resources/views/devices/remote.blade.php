@extends('layouts.app', ['title' => 'Remote Control'])

@section('style')
<style>
    .remote-container {
        max-width: 600px;
        margin: 0 auto;
    }
    .screenshot-container {
        width: 100%;
        background: #000;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
        position: relative;
    }
    .screenshot-container img {
        width: 100%;
        display: block;
    }
    .d-pad {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-gap: 10px;
        max-width: 240px;
        margin: 0 auto;
    }
    .d-pad .btn {
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        border-radius: 12px;
    }
</style>
@endsection

@section('wrapper')
<div class="page-wrapper">
    <div class="page-content">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Devices</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('devices.index') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Remote Control</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="remote-container">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Remote Control: {{ $device->android_id }}</h5>
                    <hr>

                    <div class="screenshot-container shadow-sm mb-4">
                        @if($latestScreenshot)
                            <img src="{{ asset('screenshots/' . $latestScreenshot->filename) }}" id="latestScreenshot" alt="Latest Screenshot">
                        @else
                            <div class="text-center p-5 text-white">No screenshot available</div>
                        @endif
                        <button class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2" id="refreshScreenshot">
                            <i class="bx bx-refresh"></i> Refresh
                        </button>
                    </div>

                    <div class="input-group mb-4">
                        <input type="text" id="textInput" class="form-control" placeholder="Type text to send...">
                        <button class="btn btn-primary" id="sendTextBtn">Send Text</button>
                    </div>

                    <div class="d-pad mb-4">
                        <div></div>
                        <button class="btn btn-secondary remote-btn" data-key="19"><i class="bx bx-chevron-up"></i></button>
                        <div></div>

                        <button class="btn btn-secondary remote-btn" data-key="21"><i class="bx bx-chevron-left"></i></button>
                        <button class="btn btn-primary remote-btn" data-key="23">OK</button>
                        <button class="btn btn-secondary remote-btn" data-key="22"><i class="bx bx-chevron-right"></i></button>

                        <div></div>
                        <button class="btn btn-secondary remote-btn" data-key="20"><i class="bx bx-chevron-down"></i></button>
                        <div></div>
                    </div>

                    <div class="text-center mt-3">
                        <button class="btn btn-outline-danger remote-btn btn-sm mx-1" data-key="4"><i class="bx bx-arrow-back"></i> Back</button>
                        <button class="btn btn-outline-dark remote-btn btn-sm mx-1" data-key="3"><i class="bx bx-home"></i> Home</button>
                        <button class="btn btn-outline-secondary remote-btn btn-sm mx-1" data-key="82"><i class="bx bx-menu"></i> Menu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        const deviceId = "{{ encrypt($device->id) }}";

        function sendCommand(command, type = 'key') {
            $.ajax({
                url: "{{ route('devices.remote-command') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    device_id: deviceId,
                    command: command,
                    type: type
                },
                success: function(response) {
                    // Success toast or subtle notification?
                    console.log(response.message);
                },
                error: function() {
                    Swal.fire('Error', 'Failed to send command', 'error');
                }
            });
        }

        $('.remote-btn').click(function() {
            const key = $(this).data('key');
            sendCommand(key);
        });

        $('#sendTextBtn').click(function() {
            const text = $('#textInput').val();
            if (text) {
                sendCommand(text, 'text');
                $('#textInput').val('');
            }
        });

        $('#textInput').keypress(function(e) {
            if (e.which == 13) {
                $('#sendTextBtn').click();
            }
        });

        $('#refreshScreenshot').click(function() {
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            
            $.ajax({
                url: "{{ route('devices.screenshot', encrypt($device->id)) }}",
                type: 'GET',
                success: function(response) {
                    Swal.fire('Requested', 'Screenshot request sent. Please wait a few seconds and refresh.', 'success').then(() => {
                        window.location.reload();
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Failed to request screenshot', 'error');
                    $('#refreshScreenshot').prop('disabled', false).html('<i class="bx bx-refresh"></i> Refresh');
                }
            });
        });
    });
</script>
@endsection
