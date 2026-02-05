@extends("layouts.app")

@section("style")
<link href="{{ asset('assets/plugins/Drag-And-Drop/dist/imageuploadify.min.css') }}" rel="stylesheet" />
<style>
    .file-icon {
        font-size: 2rem;
        margin-right: 1rem;
    }
    .public-link {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: inline-block;
        vertical-align: middle;
    }
    .copy-btn {
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 4px;
        background: #f1f1f1;
        border: 1px solid #ddd;
    }
    .copy-btn:hover {
        background: #e1e1e1;
    }
</style>
@endsection

@section("wrapper")
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">File Share</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Public Sharing</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="row">
            <div class="col-xl-9 mx-auto">
                <h6 class="mb-0 text-uppercase">Upload New File</h6>
                <hr/>
                <div class="card">
                    <div class="card-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <input id="fileInput" type="file" name="file" accept="*">
                            <div class="mt-3 text-end">
                                <button type="submit" class="btn btn-primary px-5">Upload File</button>
                            </div>
                        </form>
                    </div>
                </div>

                <h6 class="mb-0 text-uppercase mt-4">Public Shared Files</h6>
                <hr/>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Size</th>
                                        <th>Public Link</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="fileTableBody">
                                    @forelse($files as $file)
                                    <tr id="file-row-{{ $file->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="file-icon">
                                                    @if(Str::contains($file->mime_type, 'image'))
                                                        <i class='bx bxs-image text-primary'></i>
                                                    @elseif(Str::contains($file->mime_type, 'pdf'))
                                                        <i class='bx bxs-file-pdf text-danger'></i>
                                                    @elseif(Str::contains($file->mime_type, 'zip') || Str::contains($file->mime_type, 'rar'))
                                                        <i class='bx bxs-file-archive text-warning'></i>
                                                    @else
                                                        <i class='bx bxs-file text-secondary'></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $file->original_name }}</h6>
                                                    <small class="text-muted">{{ $file->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($file->size / 1024, 2) }} KB</td>
                                        <td>
                                            <span class="public-link" id="link-{{ $file->id }}">{{ $file->public_url }}</span>
                                            <button class="copy-btn ms-2" onclick="copyToClipboard('{{ $file->public_url }}')">
                                                <i class='bx bx-copy'></i>
                                            </button>
                                        </td>
                                        <td>
                                            <div class="d-flex order-actions">
                                                <a href="{{ route('file-share.show', $file->slug) }}" target="_blank" class=""><i class='bx bx-show'></i></a>
                                                <a href="{{ route('file-share.download', $file->slug) }}" class="ms-3"><i class='bx bx-download'></i></a>
                                                <a href="javascript:;" onclick="deleteFile({{ $file->id }})" class="ms-3 text-danger"><i class='bx bxs-trash'></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No files shared yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section("script")
<script src="{{ asset('assets/plugins/Drag-And-Drop/dist/imageuploadify.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('input[type="file"]').imageuploadify();
    });

    function copyToClipboard(text) {
        const el = document.createElement('textarea');
        el.value = text;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Link copied to clipboard',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function deleteFile(id) {
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
                    url: `/file-share/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`#file-row-${id}`).remove();
                            Swal.fire('Deleted!', 'Your file has been deleted.', 'success');
                        }
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            
            let formData = new FormData(this);
            let fileInput = $('#fileInput')[0];
            
            if (fileInput.files.length === 0) {
                Swal.fire('Error', 'Please select a file first', 'error');
                return;
            }

            Swal.fire({
                title: 'Uploading...',
                text: 'Please wait while we upload your file',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("file-share.upload") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = xhr.responseJSON.message || 'Something went wrong';
                    if (errors) {
                        errorMessage = Object.values(errors).flat().join('<br>');
                    }
                    Swal.fire('Error', errorMessage, 'error');
                }
            });
        });
    });
</script>
@endsection
