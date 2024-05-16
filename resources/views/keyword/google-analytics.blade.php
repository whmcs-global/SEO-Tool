@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css">

    <!-- Begin Page Content -->
    <section class="section">
        <div class="flash-message">
            @if (isset($errorMessage))
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{ $errorMessage }}
                </div>
            @endif
        </div>
        <div class="row">
            <!-- Services -->
            <div class="col-xl-12 col-lg-12">
                <div class="mb-4 shadow card">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="w-100">Google Analytics</h4>
                            </div>
                            <div class="col-md-4">
                                    <input type="text" id="daterange" name="daterange" style="width: 172px;"
                                        value="{{ $dateFilter }}"  placeholder="Apply daterange filter"/>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="recent-report__chart">
                            <div id="chart5"></div>
                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Top Queries</th>
                                        <th>Clicks</th>
                                        <th>Impressions</th>
                                        <th>Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($queryData as $key => $row)
                                        <tr>
                                            <td>{{ $row->keys[0] }}</td>
                                            <td>{{ $row->clicks }}</td>
                                            <td>{{ $row->impressions }}</td>
                                            <td>{{ round($row->position) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" align="center" class="hs-danger">No Data found!</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <script>
        $(document).ready(function() {
            new DataTable('#dataTable');
            $(function() {
            var urlParams = new URLSearchParams(window.location.search);
            var dateRangeFilter = urlParams.get('date_filter');
            var start = moment().subtract(29, 'days');
            var end = moment();
            if (dateRangeFilter) {
                var dates = dateRangeFilter.split(' / ');
                start = moment(dates[0], 'YYYY-MM-DD');
                end = moment(dates[1], 'YYYY-MM-DD');
            }
            var currentDate = new Date();
            $('#daterange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    maxDate: currentDate,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
                    }
                },
                function(start, end, label) {
                    var startDate = start.format('YYYY-MM-DD');
                    var endDate = end.format('YYYY-MM-DD');
                    var url = "{{ route('keywords.analytics', $keyword) }}?date_filter=" + startDate + ' / ' + endDate;
                    url = encodeURI(url);
                    window.location.href = url;
                }
            );
        });
        });
        var dateData = {!! json_encode($dateData) !!};
        </script>
    <script src="{{ asset('assets/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/apexcharts/chart-apexcharts.js') }}"></script>
@endsection
