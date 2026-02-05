@extends('layouts.app', ['title' => 'Remote Control'])

@section('style')
<style>
    :root {
        --remote-bg: #1e1e2d;
        --remote-btn-secondary: #2c2c3e;
        --remote-btn-primary: #3b3b54;
        --remote-accent: #008cff;
    }
    .remote-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .remote-layout {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    @media (min-width: 992px) {
        .remote-layout {
            flex-direction: row;
            align-items: flex-start;
        }
        .remote-visuals {
            flex: 1;
            position: sticky;
            top: 20px;
        }
        .remote-controls-panel {
            width: 400px;
        }
    }
    .remote-card {
        background: var(--remote-bg);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        height: 100%;
    }
    .screenshot-container {
        width: 100%;
        background: #000;
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .screenshot-container img {
        width: 100%;
        display: block;
        min-height: 200px;
    }
    .top-controls {
        position: absolute;
        top: 12px;
        right: 12px;
        display: flex;
        gap: 8px;
        z-index: 10;
    }
    .btn-stop-mini {
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 8px;
        background: rgba(220, 53, 69, 0.85);
        backdrop-filter: blur(4px);
        border: none;
        color: white;
        transition: all 0.2s;
    }
    .btn-stop-mini:hover {
        background: #dc3545;
        transform: translateY(-1px);
    }
    .d-pad-container {
        background: rgba(255,255,255,0.03);
        padding: 30px;
        border-radius: 100%;
        width: 260px;
        height: 260px;
        margin: 0 auto 30px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .d-pad-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-gap: 12px;
        z-index: 2;
    }
    .remote-btn-circle {
        width: 65px;
        height: 65px;
        border-radius: 50% !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: var(--remote-btn-secondary);
        border: 1px solid rgba(255,255,255,0.05);
        color: #fff;
        transition: all 0.2s;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .remote-btn-circle:hover {
        background: var(--remote-btn-primary);
        color: var(--remote-accent);
        transform: scale(1.05);
    }
    .remote-btn-circle.center-ok {
        background: var(--remote-accent);
        font-weight: bold;
        font-size: 16px;
    }
    .remote-btn-circle.center-ok:hover {
        background: #0077d9;
        color: #fff;
    }
    .nav-buttons .btn {
        background: var(--remote-btn-secondary);
        border: none;
        color: #ccc;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13px;
    }
    .nav-buttons .btn:hover {
        background: var(--remote-btn-primary);
        color: #fff;
    }
    .pulse-danger {
        animation: pulse-red 2s infinite;
        font-size: 10px;
        padding: 5px 10px;
    }
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
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
            <div class="remote-layout">
                <!-- Left Column: Visuals -->
                <div class="remote-visuals">
                    <div class="remote-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="mb-0 text-white"><i class="bx bx-broadcast me-2"></i> Live Stream</h6>
                                <span class="badge bg-dark border border-secondary text-secondary small">{{ $device->android_id }}</span>
                            </div>

                            <div class="screenshot-container">
                                <img src="{{ asset('storage/screen_' . $device->id . '.png') }}" id="latestScreenshot" alt="Waiting for Live Feed..." onerror="this.src='https://placehold.co/600x400?text=Waiting+for+Live+Feed...'">
                                
                                <div class="top-controls">
                                    <span class="badge bg-danger pulse-danger d-flex align-items-center" id="liveBadge">
                                        <i class="bx bxs-circle me-1"></i> LIVE
                                    </span>
                                    <button class="btn btn-stop-mini" id="stopRemoteBtn">
                                        <i class="bx bx-power-off"></i> STOP
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Controls -->
                <div class="remote-controls-panel">
                    <div class="remote-card">
                        <div class="card-body p-4">
                            <div class="input-group mb-4">
                                <input type="text" id="textInput" class="form-control bg-dark border-secondary text-white" placeholder="Type text...">
                                <button class="btn btn-primary" id="sendTextBtn"><i class="bx bx-send"></i></button>
                            </div>

                            <div class="d-pad-container">
                                <div class="d-pad-grid">
                                    <div></div>
                                    <button class="btn remote-btn-circle remote-btn" data-key="19"><i class="bx bx-chevron-up"></i></button>
                                    <div></div>

                                    <button class="btn remote-btn-circle remote-btn" data-key="21"><i class="bx bx-chevron-left"></i></button>
                                    <button class="btn remote-btn-circle center-ok remote-btn" data-key="23">OK</button>
                                    <button class="btn remote-btn-circle remote-btn" data-key="22"><i class="bx bx-chevron-right"></i></button>

                                    <div></div>
                                    <button class="btn remote-btn-circle remote-btn" data-key="20"><i class="bx bx-chevron-down"></i></button>
                                    <div></div>
                                </div>
                            </div>

                            <div class="nav-buttons d-flex justify-content-center gap-2 mb-4">
                                <button class="btn remote-btn" data-key="4"><i class="bx bx-arrow-back"></i> Back</button>
                                <button class="btn remote-btn" data-key="3"><i class="bx bx-home"></i> Home</button>
                                <button class="btn remote-btn" data-key="82"><i class="bx bx-menu"></i> Menu</button>
                            </div>

                            <div class="card bg-dark border-secondary mb-4">
                                <div class="card-body p-3">
                                    <label class="text-secondary small mb-2 text-uppercase">Push Saved Script</label>
                                    <div class="input-group input-group-sm">
                                        <select id="scriptSelect" class="form-select bg-dark border-secondary text-white">
                                            <option value="">Select script...</option>
                                            @foreach($scripts as $script)
                                                <option value="{{ encrypt($script->id) }}">{{ $script->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-success" id="pushScriptBtn">Push</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Command Monitor Section -->
                            <div class="">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                     <small class="text-secondary text-uppercase fw-bold"><i class="bx bx-terminal me-1"></i> Monitor</small>
                                     <small id="currentScript" class="text-success x-small"></small>
                                </div>
                                
                                <div class="bg-black p-0 rounded-3 border border-secondary overflow-hidden">
                                    <div class="table-responsive" style="max-height: 200px;">
                                        <table class="table table-dark table-sm mb-0">
                                            <tbody id="commandHistory" class="small">
                                                <tr><td class="text-center py-3 text-muted">No commands sent</td></tr>
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

        // Stop Remote Handling
        $('#stopRemoteBtn').click(function() {
            Swal.fire({
                title: 'Stop Remote?',
                text: "This will signal the device to stop receiving commands.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Stop it!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('devices.stop-remote') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            device_id: deviceId
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Stopped',
                                text: 'Remote signal stopped. Redirecting...',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('devices.index') }}";
                            });
                        }
                    });
                }
            });
        });

    });
</script>
@endsection
