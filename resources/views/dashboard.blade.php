@extends('layouts.admin')

@section('title')
    Keyword Tracker
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        .multiselect-dropdown {
            width: 100%;
            min-height: 46px;
            font-size: 15px !important;
            color: #323232;
            letter-spacing: 0.2px;
            border: 1px solid #9F9FA0;
            border-radius: 6px;
            background-color: white !important;
            max-width: 120px;
        }

        #keywordsTable th,
        #keywordsTable td {
            min-width: 100px;
        }

        #keywordsTable th:first-child,
        #keywordsTable td:first-child {
            min-width: 200px;
            max-width: 250px;
            width: 20%;
            white-space: normal;
            word-wrap: break-word;
            word-break: break-word;
        }

        #keywordsTable td:first-child>div {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #keywordsTable td:first-child .badge {
            display: inline-block;
            margin: 2px;
        }
    </style>
@endpush

@section('content')
    <section class="section">
        @if (session('message'))
            <div class="alert alert-success" role="alert">
                <span class="font-weight-bold"></span> {{ session('message') }}
            </div>
        @endif
        <div class="mb-4">
            <div class="card">
                {{-- @role('User') --}}
                <div style="padding: 10px;">
                    <form method="GET" class="row g-3 align-items-center">
                        <div class="col-auto">
                            <select name="keyword-type" class="form-control" id="keyword-type">
                                <option value="all" {{ request()->input('keyword-type') == 'all' ? 'selected' : '' }}>All
                                </option>
                                <option value="only-me"
                                    {{ request()->input('keyword-type') == 'only-me' ? 'selected' : '' }}>Only Me</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label for="labels" class="font-weight-bold">Filter by Labels:</label>
                        </div>
                        <div class="col-auto">
                            <select name="labels[]" id="field2" class="form-control" multiple multiselect-search="true"
                                multiselect-max-items="3">
                                @foreach ($labels as $label)
                                    <option value="{{ $label->id }}" {{ in_array($label->id, $labelIds) ? 'selected' : '' }}>
                                        {{ $label->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>
                </div>
                {{-- @endrole --}}
                @can('Add keyword')
                    <div class="mb-3 row justify-content-end">
                        <div class="col-auto" style="margin-right: 10px;">
                            <a href="{{ route('keywords.create') }}" class="btn btn-primary rounded-pill">Add Keyword</a>
                        </div>
                    </div>
                @endcan

                <div class="card-body">
                    @can('Keyword list')
                        <div class="table-responsive">
                            <table id="keywordsTable" class="table table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Keyword</th>
                                        @role('Admin|Super Admin')
                                            <th>Created By</th>
                                        @endrole
                                        <th>Position</th>
                                        <th>Search Volume</th>
                                        <th>Clicks</th>
                                        <th>Impressions</th>
                                        <th>Competition</th>
                                        <th>Bid rate (Low Range)</th>
                                        <th>Bid rate (High Range)</th>
                                        <th>Created At</th>
                                        @canany(['Edit keyword', 'Delete keyword'])
                                            <th>Actions</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($allKeywords as $keyword)
                                        @foreach ($keyword->keywordData as $data)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <span>{{ $keyword->keyword }}</span>
                                                        <div>
                                                            @foreach ($keyword->labels as $label)
                                                                <span class="badge badge-info">{{ $label->name }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </td>
                                                @role('Admin|Super Admin')
                                                    <td>{{ $keyword->user->name ?? 'N/A' }}</td>
                                                @endrole
                                                <td>{{ $data->position }}</td>
                                                <td>{{ $data->search_volume }}</td>
                                                <td>{{ $data->clicks }}</td>
                                                <td>{{ $data->impression }}</td>
                                                <td>{{ $data->competition }}</td>
                                                <td>{{ round($data->bid_rate_low, 2) }}</td>
                                                <td>{{ round($data->bid_rate_high, 2) }}</td>
                                                <td data-order="{{ $keyword->created_at->format('Y-m-d') }}">
                                                    {{ $keyword->created_at->format('d-M-Y') }}
                                                </td>
                                                @canany(['Edit keyword', 'Delete keyword'])
                                                    <td class="text-right">
                                                        <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                                            @can('Edit keyword')
                                                                <a href="{{ route('keywords.edit', $keyword) }}" class="mr-2 btn btn-secondary rounded-pill">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            @endcan
                                                            @can('Delete keyword')
                                                                <form method="POST" action="{{ route('keywords.destroy', $keyword) }}" class="mr-2 d-inline" id="delete-form-{{ $keyword->id }}">
                                                                    @csrf
                                                                    @method('delete')
                                                                    <button type="button" class="btn btn-danger rounded-pill" onclick="confirmDelete({{ $keyword->id }})">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                @endcanany
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No keywords found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">You do not have permission to view keywords.</p>
                    @endcan
                </div>
            </div>

    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/custom/multiselect-dropdown.js') }}"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#keywordsTable').DataTable({
                columnDefs: [{
                    targets: 'Created At',
                    type: 'date'
                }],
            });
        });

        function confirmDelete(id) {
            swal({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
