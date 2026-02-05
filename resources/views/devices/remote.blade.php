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
    .pulse-danger {
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
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
                        <img src="{{ asset('storage/screen_' . $device->id . '.png') }}" id="latestScreenshot" alt="Waiting for Live Feed..." onerror="this.src='https://placehold.co/600x400?text=Waiting+for+Live+Feed...'">
                        
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-danger pulse-danger d-flex align-items-center" id="liveBadge">
                                <i class="bx bxs-circle me-1"></i> LIVE
                            </span>
                        </div>
                    </div>

                    <div class="input-group mb-4">
                        <input type="text" id="textInput" class="form-control" placeholder="Type text to send...">
                        <button class="btn btn-primary" id="sendTextBtn">Send Text</button>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body p-3">
                            <h6 class="mb-3">Push Saved Script</h6>
                            <div class="input-group">
                                <select id="scriptSelect" class="form-select">
                                    <option value="">-- Select Script --</option>
                                    @foreach($scripts as $script)
                                        <option value="{{ encrypt($script->id) }}">{{ $script->name }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-success" id="pushScriptBtn">Push Script</button>
                            </div>
                        </div>
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

                    <!-- Command Monitor Section -->
                    <div class="mt-5">
                        <h6 class="mb-3"><i class="bx bx-terminal me-2"></i> Command Monitor</h6>
                        
                        <div class="card bg-dark text-white mb-3">
                            <div class="card-header py-1 border-secondary">
                                <small class="text-uppercase fw-bold text-success">Current Script (In Cache)</small>
                            </div>
                            <div class="card-body py-2">
                                <code id="currentScript" class="text-success small">Waiting for script...</code>
                            </div>
                        </div>

                        <div class="card bg-dark text-white">
                            <div class="card-header py-1 border-secondary">
                                <small class="text-uppercase fw-bold text-info">Recent History</small>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-dark table-sm mb-0 small">
                                        <thead>
                                            <tr>
                                                <th class="ps-3" style="width: 80px;">Time</th>
                                                <th>Script / Command</th>
                                            </tr>
                                        </thead>
                                        <tbody id="commandHistory">
                                            <tr>
                                                <td colspan="2" class="text-center py-3 text-muted">No history yet</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
        const screenImg = $('#latestScreenshot');
        const screenUrl = "{{ asset('storage/screen_' . $device->id . '.png') }}";

        // Auto refresh screenshot with recursive timeout (more stable than setInterval)
        function updateScreenshot() {
            const timestamp = new Date().getTime();
            const newImg = new Image();
            
            newImg.onload = function() {
                screenImg.attr('src', screenUrl + '?t=' + timestamp);
                // Immediately request next frame for maximum real-time speed
                setTimeout(updateScreenshot, 0);
            };
            
            newImg.onerror = function() {
                // If error occurs (file still being written by server), wait 500ms before retry
                setTimeout(updateScreenshot, 500);
            };
            
            newImg.src = screenUrl + '?t=' + timestamp;
        }

        // Start the loop
        updateScreenshot();

        // Update status and history every 2 seconds
        function updateStatus() {
            $.ajax({
                url: "{{ route('devices.remote-status', encrypt($device->id)) }}",
                type: 'GET',
                success: function(data) {
                    // Update current script
                    if (data.current_script) {
                        $('#currentScript').text(data.current_script);
                    } else {
                        $('#currentScript').html('<span class="text-muted italic">Device is idle (Cache empty)</span>');
                    }

                    // Update history
                    if (data.history && data.history.length > 0) {
                        let html = '';
                        data.history.forEach(item => {
                            html += `<tr>
                                <td class="ps-3 text-info">${item.time}</td>
                                <td><code>${item.command}</code></td>
                            </tr>`;
                        });
                        $('#commandHistory').html(html);
                    }
                }
            });
        }

        setInterval(updateStatus, 2000);
        updateStatus(); // Initial call

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
                    console.log(response.message);
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

        // Push Script Handling
        $('#pushScriptBtn').click(function() {
            const scriptId = $('#scriptSelect').val();
            if (!scriptId) {
                Swal.fire('Warning', 'Please select a script first', 'warning');
                return;
            }

            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: "{{ route('devices.push-script') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    device_id: deviceId,
                    script_id: scriptId
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pushed',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    updateStatus(); // Refresh monitor
                },
                complete: function() {
                    $('#pushScriptBtn').prop('disabled', false).text('Push Script');
                }
            });
        });

    });
</script>
@endsection
