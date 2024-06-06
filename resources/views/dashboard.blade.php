@extends('layouts.admin')

@section('title')
Keyword Tracker
@endsection

@section('content')
<section class="section">
    @if(session('message'))
    <div class="alert alert-success" role="alert">
        <span class="font-weight-bold">Success alert!</span> {{ session('message') }}
    </div>
    @endif
    <div class="mt-5">
        <div id="keywordsChart"></div>
    </div>
    <div class="card">
        @can('Add keyword')
        <div class="mb-3 row justify-content-end">
            <div class="col-auto">
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
                            <th>Position</th>
                            <th>S. Volume</th>
                            <th>Click</th>
                            <th>Impressions</th>
                            <th>Competition</th>
                            <th>Bid rate (Low Range)</th>
                            <th>Bid rate (High Range)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($keywords as $keyword)
                        <tr>
                            <td>{{ $keyword->keyword }}</td>
                            <td>{{ $keyword->position }}</td>
                            <td>{{ $keyword->avgMonthlySearches }}</td>
                            <td>{{ $keyword->clicks }}</td>
                            <td>{{ $keyword->impressions }}</td>
                            <td>{{ $keyword->competition}}</td>
                            <td>{{ round($keyword->lowTopOfPageBidRupees,2)}}</td>
                            <td>{{ round($keyword->highTopOfPageBidRupees,2)}}</td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                    <!-- <a href="{{ route('keywords.analytics', $keyword) }}" class="btn btn-primary rounded-pill mr-2">
                                        <i class="fas fa-chart-line"></i> Analytics
                                    </a> -->
                                    @can('Edit keyword')
                                    <a href="{{ route('keywords.edit', $keyword) }}" class="btn btn-secondary rounded-pill mr-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('Delete keyword')
                                    <form method="POST" action="{{ route('keywords.destroy', $keyword) }}" class="d-inline mr-2" id="delete-form-{{ $keyword->id }}">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-danger rounded-pill" onclick="confirmDelete({{ $keyword->id }})">
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
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#keywordsTable').DataTable({
            responsive: true,
            columnDefs: [
                { targets: -1, orderable: false }
            ]
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var data = [
        {{ $ranges['1-10'] }},
        {{ $ranges['11-20'] }},
        {{ $ranges['21-30'] }},
        {{ $ranges['31-40'] }},
        {{ $ranges['41-50'] }}
    ];

    var maxValue = Math.max(...data);

    var options = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '14px',
                colors: ['#000']
            },
            formatter: function (val) {
                return val.toFixed(0);
            }
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        series: [{
            name: 'No of Keywords',
            data: data
        }],
        xaxis: {
            categories: ['1-10', '11-20', '21-30', '31-40', '41-50'],
            title: {
                text: 'Position Ranges'
            }
        },
        yaxis: {
            title: {
                text: 'No of Keywords'
            },
            min: 0,
            max: maxValue + 2,
            labels: {
                formatter: function (val) {
                    return val.toFixed(0);
                }
            }
        },
        fill: { opacity: 1 },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val;
                }
            }
        },
        title: {
            text: 'Keywords Position Distribution',
            align: 'center',
            style: {
                fontSize: '24px',
                fontWeight: 'bold',
                color: '#263238'
            }
        },
        colors: ['#1E90FF']
    };

    var chart = new ApexCharts(document.querySelector("#keywordsChart"), options);
    chart.render();
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
