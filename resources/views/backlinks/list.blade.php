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
                                <h4>Total Links</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <label><b>
                                            <h1>{{ $totallinks }}</h1>
                                        </b></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-center align-items-center">
                                <h4>Total Unique Domains</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <label><b>
                                            <h1>{{ $uniqueDomainCount }}</h1>
                                        </b></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-12">
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
                                            </div> -->
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-6">
                <div class="authority d-grid">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-center align-items-center">
                                <h4>Total Active Links</h4>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <label><b>
                                            <h1>{{ $activelinks }}</h1>
                                        </b></label>
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
                                    <label><b>
                                            <h1>{{ $pendinglinks }}</h1>
                                        </b></label>
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
                <div id="filterForm" class="mb-3">
                    <form method="GET" action="{{ route('backlinks.index') }}" class="filter-form-box" style="gap:10px;">
                        <div class="me-2 mb-2 mb-md-0" style="flex: 1;">
                            <select name="user" class="form-control" id="userSelect">
                                <option value="">Created By</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ $request->input('user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="me-2 mb-2 mb-md-0" style="flex: 1;">
                            <select name="link_type" class="form-control" id="linkTypeSelect">
                                <option value="">Select Link Type</option>
                                <option value="Guest Post" {{ $request->input('link_type') == 'Guest Post' ? 'selected' : '' }}>
                                    Guest Post
                                </option>
                                <option value="Infographics"
                                    {{ $request->input('link_type') == 'Infographics' ? 'selected' : '' }}>
                                    Infographics
                                </option>
                                <option value="Sponsored Content"
                                    {{ $request->input('link_type') == 'Sponsored Content' ? 'selected' : '' }}>
                                    Sponsored Content
                                </option>
                                <option value="Social Bookmarking"
                                    {{ $request->input('link_type') == 'Social Bookmarking' ? 'selected' : '' }}>
                                    Social Bookmarking
                                </option>
                                <option value="Infographic Submission"
                                    {{ $request->input('link_type') == 'Infographic Submission' ? 'selected' : '' }}>
                                    Infographic Submission
                                </option>
                                <option value="Business Listing"
                                    {{ $request->input('link_type') == 'Business Listing' ? 'selected' : '' }}>
                                    Business Listing
                                </option>
                                <option value="Web 2.0" {{ $request->input('link_type') == 'Web 2.0' ? 'selected' : '' }}>
                                    Web 2.0
                                </option>
                                <option value="PPT Submission"
                                    {{ $request->input('link_type') == 'PPT Submission' ? 'selected' : '' }}>
                                    PPT Submission
                                </option>
                                <option value="Podcast Submission"
                                    {{ $request->input('link_type') == 'Podcast Submission' ? 'selected' : '' }}>
                                    Podcast Submission
                                </option>
                                <option value="PDF Submission"
                                    {{ $request->input('link_type') == 'PDF Submission' ? 'selected' : '' }}>
                                    PDF Submission
                                </option>
                                <option value="Article Submission"
                                    {{ $request->input('link_type') == 'Article Submission' ? 'selected' : '' }}>
                                    Article Submission
                                </option>
                                <option value="Quora" {{ $request->input('link_type') == 'Quora' ? 'selected' : '' }}>
                                    Quora
                                </option>
                                <option value="Forum Submission"
                                    {{ $request->input('link_type') == 'Forum Submission' ? 'selected' : '' }}>
                                    Forum Submission
                                </option>
                                <option value="PR Submission"
                                    {{ $request->input('link_type') == 'PR Submission' ? 'selected' : '' }}>
                                    PR Submission
                                </option>
                                <option value="Brand Mentions"
                                    {{ $request->input('link_type') == 'Brand Mentions' ? 'selected' : '' }}>
                                    Brand Mentions
                                </option>
                                <option value="Guest Post Submission"
                                    {{ $request->input('link_type') == 'Guest Post Submission' ? 'selected' : '' }}>
                                    Guest Post Submission
                                </option>
                                <option value="Broken Link Building"
                                    {{ $request->input('link_type') == 'Broken Link Building' ? 'selected' : '' }}>
                                    Broken Link Building
                                </option>
                                <option value="Profile Creation"
                                    {{ $request->input('link_type') == 'Profile Creation' ? 'selected' : '' }}>
                                    Profile Creation
                                </option>
                                <option value="Coupon Submission"
                                    {{ $request->input('link_type') == 'Coupon Submission' ? 'selected' : '' }}>
                                    Coupon Submission
                                </option>

                            </select>
                        </div>
                        <div class="me-2 mb-2 mb-md-0" style="flex: 1;">
                            <select name="status" class="form-control" id="statusSelect">
                                <option value="">Select Status</option>
                                <option value="Active" {{ $request->input('status') == 'Active' ? 'selected' : '' }}>Active
                                </option>
                                {{-- <option value="Inactive" {{ $request->input('status') == 'Inactive' ? 'selected' : '' }}>
                                    Inactive</option> --}}
                                <option value="Pending" {{ $request->input('status') == 'Pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="Declined" {{ $request->input('status') == 'Declined' ? 'selected' : '' }}>
                                    Rejected</option>
                            </select>
                        </div>
                        {{-- <div class="me-2 mb-2 mb-md-0" style="flex: 1;">
                            <select name="status" class="form-control" id="statusSelect">
                                <option value="">Status</option>
                                <option value="Approved" {{ $request->input('status') == 'Active' ? 'selected' : '' }}>Approved
                                </option>
                                <option value="Pending" {{ $request->input('status') == 'Pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="Rejected" {{ $request->input('status') == 'Declined' ? 'selected' : '' }}>
                                    Rejected</option>
                            </select>
                        </div> --}}
                        <div class="me-2 mb-2 mb-md-0" style="flex: 1;">
                            <input type="text" name="daterange" class="form-control" id="dateRangeInput"
                                placeholder="Select Date Range" value="{{ $request->input('daterange') }}" />
                        </div>
                        <div class="d-flex" style="gap:10px;">
                            <button type="submit" class="btn btn-primary me-2">Apply</button>
                            <a href="{{ route('backlinks.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>
                </div>
                <div class="row mb-3 justify-content-between align-items-center" style="padding: 0px 10px;">
                    <!-- <div class="col-auto">
                                                                    <button id="filterButton" class="btn btn-secondary">
                                                                        <i class="fas fa-filter"></i> Filters
                                                                    </button>
                                                                </div> -->
                    <div class="col-auto">
                        <a href="{{ route('backlinks.create') }}" class="btn btn-primary rounded-pill">
                            Add Backlink
                        </a>
                    </div>
                </div>
            @endcan
            <div class="card-body">
                @can('Backlink list')
                    <div class="table-responsive">
                        <table id="backlinksTable" class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    {{-- @role('Admin|Super Admin')
                                    <th>Added By</th>
                                @endrole --}}
                                    {{-- <th>Website</th> --}}
                                    <th>URL</th>
                                    <th>Target Keyword</th>
                                    <th>Source</th>
                                    {{-- <th>Backlink Source</th> --}}
                                    <th>Link Type</th>
                                    {{-- <th>Spam Score (SS)</th> --}}
                                    <th>SS</th>
                                    {{-- @role('Admin|Super Admin')
                                        <th>Status</th>
                                    @endrole --}}
                                    <th>Status</th>
                                    {{-- <th>Anchor Text</th> --}}
                                    <th>DA</th>
                                    {{-- <th>Domain Authority</th> --}}
                                    <th>PA</th>
                                    {{-- <th>Page Authority</th> --}}
                                    <th>Contact Person</th>
                                    {{-- <th>Notes Comments</th> --}}
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($backlinks as $backlink)
                                    <tr>
                                        {{-- @role('Admin|Super Admin')
                                        <td>
                                            {{ $backlink->created_by }}
                                            <br>
                                            <small class="text-muted">{{ $backlink->email }}</small>
                                        </td>
                                    @endrole
                                    <td>{{ $backlink->website }}</td> --}}
                                        <td>
                                            <a href="{{ $backlink->url }}" target="_blank">
                                                {{ Str::limit($backlink->url, 20, '...') }}
                                            </a>
                                        </td>
                                        <td>{{ $backlink->keyword_value }}</td>
                                        <td>{{ $backlink->backlink_source }}</td>
                                        <td>{{ $backlink->link_type }}</td>
                                        <td>{{ $backlink->spam_score }}</td>
                                        {{-- @role('Admin|Super Admin')
                                            <td>{{ $backlink->status }}</td>
                                        @endrole --}}
                                        <td>
                                            @if ($backlink->status == 'Active')
                                                <span class="badge bg-success" data-toggle="tooltip" title="{{ $backlink->notes_comments }}">{{ ucfirst($backlink->status) }}</span>
                                            @elseif ($backlink->status == 'Pending')
                                                <span class="badge bg-warning" data-toggle="tooltip" title="{{ $backlink->notes_comments }}">{{ ucfirst($backlink->status) }}</span>
                                            @elseif ($backlink->status == 'Declined')
                                                <span class="badge bg-danger" data-toggle="tooltip" title="{{ $backlink->notes_comments ?? 'Reason not provided' }}">Rejected</span>
                                            @else
                                                <span class="badge bg-info" data-toggle="tooltip" title="{{ $backlink->notes_comments }}">{{ ucfirst($backlink->status) }}</span>
                                            @endif
                                        </td>
                                        {{-- <td>{{ $backlink->anchor_text }}</td> --}}
                                        <td>{{ $backlink->domain_authority }}</td>
                                        <td>{{ $backlink->page_authority }}</td>
                                        <td>{{ $backlink->contact_person }}</td>
                                        {{-- <td>{{ $backlink->notes_comments }}</td> --}}
                                        <td data-order="{{ $backlink->created_at->format('Y-m-d') }}">
                                            {{ $backlink->created_at->format('d-M-Y') }}
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                                @can('Edit backlink')
                                                    <a href="{{ route('backlinks.create', $backlink) }}"
                                                        class="btn btn-secondary rounded-pill mr-2">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('Delete backlink')
                                                    <form method="POST" action="{{ route('backlinks.delete', $backlink) }}"
                                                        class="d-inline mr-2" id="delete-form-{{ $backlink->id }}">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="button" class="btn btn-danger rounded-pill"
                                                            onclick="confirmDelete({{ $backlink->id }})">
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
    <style>
        .filter-form-box {
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
    </style>
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
                    },
                    {
                        targets: 'Created At',
                        type: 'date'
                    }
                ],
                order: [
                    [5, 'asc']
                ]
            });

            // $('#filterButton').on('click', function() {
            //     $('#filterForm').toggle();
            // });

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
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                    'MM/DD/YYYY'));
            });

            $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endpush
