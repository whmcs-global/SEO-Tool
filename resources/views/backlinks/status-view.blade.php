@extends('layouts.admin')

@section('title')
    Backlinks
@endsection

@section('content')
    <section class="section">
        <x-alert-component />

        <div class="card">
            <div class="card-header">
                <h4>{{ ucfirst($approvalStatus) }} Backlinks</h4>
            </div>
            <div class="card-body">
                @can('Backlink list')
                    <div class="table-responsive">
                        <table id="backlinksTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>URL</th>
                                    <th>Target Keyword</th>
                                    <th>DA</th>
                                    <th>PA</th>
                                    <th>Contact Person</th>
                                    <th>Notes Comments</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($backlinks as $backlink)
                                    <tr data-id="{{ $backlink->id }}">
                                        <td>{{ Str::limit($backlink->url, 20, '...') }}</td>
                                        <td>{{ $backlink->keyword_value }}</td>
                                        <td>{{ $backlink->domain_authority }}</td>
                                        <td>{{ $backlink->page_authority }}</td>
                                        <td>{{ $backlink->contact_person }}</td>
                                        <td>{{ $backlink->notes_comments }}</td>
                                        <td>{{ $backlink->created_at->format('d-m-Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Modal -->
        <div id="backlinkModal" class="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Backlink Details</h5>
                        <button type="button" class="close" id="closeModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Backlink details will be dynamically inserted here -->
                    </div>
                    <div class="modal-footer">
                        <textarea id="approval-reason" class="form-control" placeholder="Add comments or reasons"></textarea>
                        <button type="button" class="btn btn-success" id="approve-backlink">
                            <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-danger" id="reject-backlink">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
        }
        .modal-dialog {
            background-color: white;
            border-radius: 5px;
            max-width: 500px;
            width: 100%;
        }
        .modal-content {
            border: none;
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 15px;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 15px;
            display: flex;
            justify-content: flex-end;
        }
        .modal-footer .btn {
            margin-left: 10px;
        }
        #approval-reason {
            width: 100%;
            margin-bottom: 15px;
        }
        .close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }
        #backlinksTable tbody tr {
            cursor: pointer;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            var backlinkId;
            $('#backlinksTable').DataTable({
                responsive: true,
                columnDefs: [{
                    targets: -1,
                    orderable: false
                }]
            });

            $('#backlinksTable tbody').on('click', 'tr', function() {
                backlinkId = $(this).data('id');

                $.ajax({
                    url: '/backlinks/approve/' + backlinkId,
                    method: 'GET',
                    success: function(data) {
                        $('#backlinkModal .modal-body').html(`
                            <p><strong>URL:</strong> ${data.backlink.url}</p>
                            <p><strong>Target Keyword:</strong> ${data.backlink.keyword_value}</p>
                            <p><strong>Source:</strong> ${data.backlink.backlink_source}</p>
                            <p><strong>Link Type:</strong> ${data.backlink.link_type}</p>
                            <p><strong>Spam Score:</strong> ${data.backlink.spam_score}</p>
                            <p><strong>Status:</strong> ${data.backlink.status}</p>
                            <p><strong>Domain Authority:</strong> ${data.backlink.domain_authority}</p>
                            <p><strong>Page Authority:</strong> ${data.backlink.page_authority}</p>
                            <p><strong>Contact Person:</strong> ${data.backlink.contact_person}</p>
                            <p><strong>Notes/Comments:</strong> ${data.backlink.notes_comments || 'N/A'}</p>
                            <p><strong>Created At:</strong> ${new Date(data.backlink.created_at).toLocaleDateString()}</p>
                        `);
                        $('#backlinkModal').css('display', 'flex');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            });

            function updateBacklinkStatus(backlinkId, status) {
                var reason = $('#approval-reason').val();

                $.ajax({
                    url: '/backlinks/approve/' + backlinkId,
                    method: 'POST',
                    data: {
                        status: status,
                        reason: reason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        $('#backlinkModal').hide();
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        $('#backlinkModal').hide();
                    }
                });
            }

            $('#approve-backlink').on('click', function() {
                updateBacklinkStatus(backlinkId, 'approved');
            });

            $('#reject-backlink').on('click', function() {
                updateBacklinkStatus(backlinkId, 'rejected');
            });

            // Close modal
            $('#closeModal, #closeModalFooter').on('click', function() {
                $('#backlinkModal').hide();
            });

            // Close modal if clicked outside
            $(window).on('click', function(event) {
                if ($(event.target).is('#backlinkModal')) {
                    $('#backlinkModal').hide();
                }
            });
        });
    </script>
@endpush
