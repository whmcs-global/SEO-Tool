@extends('layouts.admin')

@section('title')
    Track Pages
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Track Pages</h4>
                    <div id="filterForm" class="col-auto">
                        <form method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="daterange" class="form-control" id="dateRangeInput"
                                    placeholder="Select Date Range" value="{{ request()->input('daterange') }}" />
                                <div class="">
                                    <button type="submit" class="btn btn-primary">Apply</button>
                                    <a href="{{ route('page.list') }}" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive" style="padding: 10px;">
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
                            <tbody class="table-body">
                                @forelse($report as $data)
                                    <tr>
                                        <td class="text-break">{{ $data['pagePath'] }}</td>
                                        <td class="text-break">{{ $data['pageTitle'] }}</td>
                                        <td>{{ $data['activeUsers'] }}</td>
                                        <td>{{ $data['newUsers'] }}</td>
                                        <td>{{ $data['totalUsers'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="w-25">Total</th>
                                    <th class="w-25"></th>
                                    <th class="w-15">{{ $totals['activeUsers'] }}</th>
                                    <th class="w-15">{{ $totals['newUsers'] }}</th>
                                    <th class="w-15">{{ $totals['totalUsers'] }}</th>
                                </tr>
                            </tfoot>
                        </table>
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
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 10,
                lengthChange: false,
                info: false,
            });
        });
        $(function() {
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
                startDate: moment().subtract(6, 'days'),
                endDate: moment()
            });

            var initialDateRange = $('input[name="daterange"]').val();
            if (initialDateRange) {
                var dates = initialDateRange.split(' - ');
                $('input[name="daterange"]').data('daterangepicker').setStartDate(moment(dates[0]));
                $('input[name="daterange"]').data('daterangepicker').setEndDate(moment(dates[1]));
            }

            $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
            });

            $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endpush
