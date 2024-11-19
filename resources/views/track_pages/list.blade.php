@extends('layouts.admin')

@section('title')
    Track Pages
@endsection

@section('content')
@if(is_null(auth()->user()->getCurrentProject()->property_id))
<div class="alert alert-danger" role="alert">
    <span class="font-weight-bold"></span>Please Update Your Property Id and and Give Viewer Permission SeoTool service email in analytics Dashboard.<a style="color: black" href="{{ route('admin.websites.edit', auth()->user()->website_id ?? 0) }}"> Click here</a>
</div>
@endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Track Pages</h4>
                    <div id="filterForm" class="col-auto">
                        <form id="analyticsFilterForm" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="daterange" class="form-control" id="dateRangeInput"
                                    placeholder="Select Date Range" value="{{ request()->input('daterange') }}" />

                                <input type="text" name="pagePath" class="form-control ml-2"
                                    placeholder="Filter by Page Path" />

                                <select name="matchType" id="matchType" class="form-control ml-2">
                                    <option value="CONTAINS">Contains</option>
                                    <option value="BEGINS_WITH">Begins with</option>
                                    <option value="ENDS_WITH">Ends with</option>
                                    <option value="EXACT">Exact</option>

                                </select>

                                <button type="button" id="applyFilter" class="btn btn-primary ml-2">Apply</button>
                                <button type="button" id="clearFilter" class="btn btn-secondary ml-2">Clear</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="analyticsReport">
                        <div class="table-responsive" style="padding: 10px;">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th class="w-20">Page Information</th>
                                        <th class="w-5">Source / Medium</th>
                                        <th class="w-5">New Users</th>
                                        <th class="w-5">Total Users</th>
                                        <th class="w-5">Organic Clicks</th>
                                        <th class="w-5">Organic Impressions</th>
                                        <th class="w-5">Organic CTR</th>
                                        <th class="w-5">Organic Avg Position</th>
                                        <th class="w-10">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mergedReport as $data)
                                        <tr>
                                            <td class="text-break">
                                                <div class="wrap-text">{{ $data['pagePath'] }}</div>
                                                <small class="text-muted wrap-text">{{ $data['pageTitle'] }}</small>
                                            </td>
                                            <td class="source-column text-break">
                                                @include('track_pages.partials.source-icon', [
                                                    'source' => $data['sessionSourceMedium'],
                                                ])
                                                {{ $data['sessionSourceMedium'] }}
                                            </td>
                                            <td>{{ $data['newUsers'] }}</td>
                                            <td>{{ $data['totalUsers'] }}</td>
                                            <td>{{ number_format($data['organicGoogleSearchClicks'], 2) }}</td>
                                            <td>{{ number_format($data['organicGoogleSearchImpressions'], 2) }}</td>
                                            <td>{{ number_format($data['organicGoogleSearchClickThroughRate'], 2) }}</td>
                                            <td>{{ number_format($data['organicGoogleSearchAveragePosition'], 2) }}</td>
                                            <td>
                                                <a href="{{ route('page.show', ['url' => $data['pagePath'] ]); }}" class="btn btn-primary">Details</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th></th>
                                        <th>{{ $pageTotals['newUsers'] }}</th>
                                        <th>{{ $pageTotals['totalUsers'] }}</th>
                                        <th>{{ number_format($organicTotals['organicGoogleSearchClicks'], 2) }}</th>
                                        <th>{{ number_format($organicTotals['organicGoogleSearchImpressions'], 2) }}</th>
                                        <th>{{ number_format($organicTotals['organicGoogleSearchClickThroughRate'], 2) }}</th>
                                        <th>{{ number_format($organicTotals['organicGoogleSearchAveragePosition'], 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="tableLoader" class="position-absolute w-100 h-100 d-none"
                    style="background: rgb(240 243 255 / 62%)">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <style>
        .table-container {
            overflow-x: auto;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 2;
        }

        .table tfoot th {
            position: sticky;
            bottom: 0;
            background: #f8f9fa;
            z-index: 2;
        }

        .table .w-25 {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table .w-15 {
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table td.text-break {
            white-space: normal;
            word-wrap: break-word;
        }

        @media (max-width: 767px) {

            .table th,
            .table td {
                padding: 0.5rem;
            }
        }

        .card-body {
            position: relative;
            min-height: 200px;
        }

        #tableLoader {
            transition: opacity 0.2s ease-in-out;
        }

        #tableLoader.fade-out {
            opacity: 0;
        }

        .spinner-border {
            width: 6rem !important;
            height: 6rem !important;

        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            function initializeDataTable() {
                if ($.fn.DataTable.isDataTable('.table')) {
                    $('.table').DataTable().destroy();
                }
                $('.table').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    pageLength: 10,
                    lengthChange: false,
                    info: false,
                });
            }

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
                },
                startDate: moment().subtract(1, 'days'),
                endDate: moment().subtract(1, 'days'),
                maxDate: moment(),
            }).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
            }).on('cancel.daterangepicker', function() {
                $(this).val('');
            });


            function showLoader() {
                $('#tableLoader').removeClass('d-none');
            }

            function hideLoader() {
                $('#tableLoader').addClass('fade-out');
                setTimeout(() => {
                    $('#tableLoader').addClass('d-none').removeClass('fade-out');
                }, 200);
            }

            function loadAnalyticsData() {
                showLoader();
                $.ajax({
                    url: '{{ route('page.list') }}',
                    method: 'GET',
                    data: {
                        daterange: $('input[name="daterange"]').val(),
                        pagePath: $('input[name="pagePath"]').val(),
                        matchType: $('#matchType').val()
                    },
                    success: function(response) {
                        if (response.data == 'false') {
                            var sample = `<div class="table-responsive" style="padding: 10px;">
                                            <table class="table table-striped mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="w-25">Page Path</th>
                                                        <th class="w-25">Page Title</th>
                                                        <th class="w-15">Active Users</th>
                                                        <th class="w-15">New Users</th>
                                                        <th class="w-15">Total Users</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                        <tr>
                                                            <td colspan="5" class="text-center">No data available</td>
                                                        </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        `;
                            $('#analyticsReport').html(sample);
                            hideLoader();
                        } else {
                            $('#analyticsReport').html(response);
                            initializeDataTable();
                        }
                    },
                    error: function() {
                        alert("Error loading data. Please try again.");
                    },
                    complete: function() {
                        hideLoader();
                    }
                });
            }

            $('#applyFilter').on('click', function(e) {
                e.preventDefault();
                loadAnalyticsData();
            });

            $('#clearFilter').on('click', function(e) {
                e.preventDefault();
                $('input[name="daterange"]').val('');
                $('input[name="pagePath"]').val('');
                $('#matchType').val('EXACT');
                loadAnalyticsData();
            });

            initializeDataTable();
        });
    </script>
@endpush
