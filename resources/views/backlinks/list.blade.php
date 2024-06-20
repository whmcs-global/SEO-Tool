@extends('layouts.admin')

@section('title')
    Backlinks
@endsection

@section('content')
    <section class="section">
        <div class="graphs d-flex">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-6">
                <div class="authority d-grid">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-center align-items-center">
                                <h4>Total Active Links</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <label><b><h1>{{ $activelinks }}</h1></b></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-center align-items-center">
                                <h4>Total Inactive Links</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <label><b><h1>{{ $inactivelinks }}</h1></b></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-6">
            <div class="authority d-grid">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-center align-items-center">
                                <h4>Total Declined Links</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <label><b><h1>{{ $declinedlinks }}</h1></b></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-center align-items-center">
                                <h4>Total Pending Links</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <label><b><h1>{{ $pendinglinks }}</h1></b></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-alert-component />

        <div class="card">
        @can('Add backlink')
            <div class="row mb-3 justify-content-between align-items-center">
                <div class="col-auto">
                    <button id="filterButton" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filters
                    </button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('backlinks.create') }}" class="btn btn-primary rounded-pill">
                        Add Backlink
                    </a>
                </div>
            </div>
            <div id="filterForm" class="mb-3 card card-body border-primary" style="display: none;">
                <form method="GET" action="{{ route('backlinks.index') }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="form-floating">
                                <select name="user" class="form-control" id="userSelect">
                                    <option value="">Created By</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ $request->input('user') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="form-floating">
                                <select name="link_type" class="form-control" id="linkTypeSelect">
                                    <option value="">Select Link Type</option>
                                    <option value="Guest Post" {{ $request->input('link_type') == 'Guest Post' ? 'selected' : '' }}>
                                        Guest Post
                                    </option>
                                    <option value="Infographics" {{ $request->input('link_type') == 'Infographics' ? 'selected' : '' }}>
                                        Infographics
                                    </option>
                                    <option value="Sponsored Content" {{ $request->input('link_type') == 'Sponsored Content' ? 'selected' : '' }}>
                                        Sponsored Content
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="form-floating">
                                <select name="status" class="form-control" id="statusSelect">
                                    <option value="">Select Status</option>
                                    <option value="Active" {{ $request->input('status') == 'Active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="Inactive" {{ $request->input('status') == 'Inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                    <option value="Pending" {{ $request->input('status') == 'Pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="Declined" {{ $request->input('status') == 'Declined' ? 'selected' : '' }}>
                                        Declined
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="form-floating">
                                <input type="text" name="daterange" class="form-control" id="dateRangeInput" placeholder="Select Date Range"
                                    value="{{ $request->input('daterange') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                            <a href="{{ route('backlinks.index') }}" class="btn btn-secondary">Clear Filters</a>
                        </div>
                    </div>
                </form>
            </div>
        @endcan
            <div class="card-body">
                @can('Backlink list')
                    <div class="table-responsive">
                    <table id="backlinksTable" class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                @role('Admin|Super Admin')
                                    <th>Added By</th>
                                @endrole
                                <th>Website</th>
                                <th>URL</th>
                                <th>Target Keyword</th>
                                <th>Backlink Source</th>
                                <th>Link Type</th>
                                <th>Status</th>
                                <th>Anchor Text</th>
                                <th>Domain Authority</th>
                                <th>Page Authority</th>
                                <th>Contact Person</th>
                                <th>Notes Comments</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($backlinks as $backlink)
                                <tr>
                                    @role('Admin|Super Admin')
                                        <td>{{ $backlink->created_by }}</td>
                                    @endrole
                                    <td>{{ $backlink->website }}</td>
                                    <td>
                                        <a href="{{ $backlink->url }}" target="_blank">
                                            {{ Str::limit($backlink->url, 20, '...') }}
                                        </a>
                                    </td>
                                    <td>{{ $backlink->target_keyword }}</td>
                                    <td>{{ $backlink->backlink_source }}</td>
                                    <td>{{ $backlink->link_type }}</td>
                                    <td>{{ $backlink->status }}</td>
                                    <td>{{ $backlink->anchor_text }}</td>
                                    <td>{{ $backlink->domain_authority }}</td>
                                    <td>{{ $backlink->page_authority }}</td>
                                    <td>{{ $backlink->contact_person }}</td>
                                    <td>{{ $backlink->notes_comments }}</td>
                                    <td>{{ $backlink->created_at->format('d-m-Y') }}</td>
                                    <td class="text-right">
                                        <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                            @can('Edit backlink')
                                                <a href="{{ route('backlinks.create', $backlink) }}" class="btn btn-secondary rounded-pill mr-2">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('Delete backlink')
                                                <form method="POST" action="{{ route('backlinks.delete', $backlink) }}" class="d-inline mr-2" id="delete-form-{{ $backlink->id }}">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-danger rounded-pill" onclick="confirmDelete({{ $backlink->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @endcan
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#backlinksTable').DataTable({
                responsive: true,
                columnDefs: [{
                    targets: -1,
                    orderable: false
                }]
            });

            $('#filterButton').on('click', function() {
                $('#filterForm').toggle();
            });

            $('#clearFilters').on('click', function() {
                $('select[name="user"]').val('');
                $('select[name="link_type"]').val('');
                $('select[name="status"]').val('');
                $('input[name="daterange"]').val('');
            });

            $('input[name="daterange"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    }
            });

            $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            });

            $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endpush
