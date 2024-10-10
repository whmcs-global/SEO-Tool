@extends('layouts.admin')

@section('title')
    Dashboard
@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        #keywordPerformanceChart {
            height: 300px !important;
        }

        .position-change {
            display: inline-flex;
            align-items: center;
        }

        .up {
            color: green;
            margin-left: 5px;
        }

        .down {
            color: red;
            margin-left: 5px;
        }

        .same {
            color: gray;
            margin-left: 5px;
        }

        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .thead-dark th {
            background-color: #343a40;
            color: white;
        }

        .dataTables_wrapper.no-footer {
            margin: 10px;
        }

        .metric-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .metric-title {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #343a40;
        }

        .dt-buttons {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="row">
                @foreach ($labels as $label)
                    <div class="col-md-2">
                        <div class="card">
                            <div class="card-body p-2">
                                <h5 class="card-title mb-1">{{ $label->name }}</h5>
                                <p class="card-text mb-0">{{ $label->getKeywordCountAttribute() }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Keyword Performance Overview</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="keywordPerformanceChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="metric-card">
                                <div class="metric-title">Total Keywords</div>
                                <div class="metric-value">{{ $keywordStats['total'] }}</div>
                            </div>
                            {{-- <div class="metric-card">
                                <div class="metric-title">Average Position Change</div>
                                <div class="metric-value">
                                    {{ $keywordStats['avgPositionChange'] > 0 ? '+' : '' }}{{ $keywordStats['avgPositionChange'] }}
                                </div>
                            </div> --}}
                            <div class="metric-card">
                                <div class="metric-title">Top Improved Keyword</div>
                                <div class="metric-value">{{ $keywordStats['topImproved']['keyword'] ?? 'N/A' }}</div>
                                <div>Change: +{{ number_format($keywordStats['topImproved']['change'] ?? 0, 2) }}</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-title">Most Declined Keyword</div>
                                <div class="metric-value">{{ $keywordStats['topDeclined']['keyword'] ?? 'N/A' }}</div>
                                <div>Change: {{ number_format($keywordStats['topDeclined']['change'] ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-12 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4>Today Rank Up Keywords</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="rankUpKeywordsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Keyword</th>
                                    <th colspan="2">Positions</th>
                                    <th>Clicks</th>
                                    <th>Impressions</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>{{ $yesterday }}</th>
                                    <th>{{ $today }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upKeywords as $keywordData)
                                    <tr>
                                        <td>{{ $keywordData['keyword']->keyword }}</td>
                                        <td>{{ number_format($keywordData['previous_position'], 2) }}</td>
                                        <td>
                                            {{ number_format($keywordData['current_position'], 2) }}
                                            @if ($keywordData['current_position'] < $keywordData['previous_position'])
                                                <span class="up" title="Position Improved">&#9650;
                                                    {{ number_format($keywordData['previous_position'] - $keywordData['current_position'], 2) }}</span>
                                            @elseif($keywordData['current_position'] > $keywordData['previous_position'])
                                                <span class="down" title="Position Dropped">&#9660;
                                                    {{ number_format($keywordData['current_position'] - $keywordData['previous_position'], 2) }}</span>
                                            @else
                                                <span class="same" title="No Change">=</span>
                                            @endif
                                        </td>
                                        <td>{{ $keywordData['keyword']->clicks }}</td>
                                        <td>{{ $keywordData['keyword']->impression }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-12 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4>Today Rank Down Keywords</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="rankDownKeywordsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Keyword</th>
                                    <th colspan="2">Positions</th>
                                    <th>Clicks</th>
                                    <th>Impressions</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>{{ $yesterday }}</th>
                                    <th>{{ $today }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($downKeywords as $keywordData)
                                    <tr>
                                        <td>{{ $keywordData['keyword']->keyword }}</td>
                                        <td>{{ number_format($keywordData['previous_position'], 2) }}</td>
                                        <td>
                                            {{ number_format($keywordData['current_position'], 2) }}
                                            @if ($keywordData['current_position'] > $keywordData['previous_position'])
                                                <span class="down" title="Position Dropped">&#9660;
                                                    {{ number_format($keywordData['current_position'] - $keywordData['previous_position'], 2) }}</span>
                                            @elseif($keywordData['current_position'] < $keywordData['previous_position'])
                                                <span class="up" title="Position Improved">&#9650;
                                                    {{ number_format($keywordData['previous_position'] - $keywordData['current_position'], 2) }}</span>
                                            @else
                                                <span class="same" title="No Change">=</span>
                                            @endif
                                        </td>
                                        <td>{{ $keywordData['keyword']->clicks }}</td>
                                        <td>{{ $keywordData['keyword']->impression }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">New Keywords</h4>
                    <div class="d-flex align-items-center">
                        <div class="form-row align-items-center mr-3">
                            <div class="col-auto">
                                <label for="dateFilter" class="font-weight-bold mb-0">Filter by:</label>
                            </div>
                            <div class="col">
                                <select id="dateFilter" class="form-control">
                                    <option value="all" selected>All</option>
                                    <option value="3">Last 3 days</option>
                                    <option value="7">Last 7 days</option>
                                    <option value="15">Last 15 days</option>
                                </select>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Export
                            </button>
                            <div class="dropdown-menu" aria-labelledby="exportDropdown">
                                <a class="dropdown-item" href="#" id="exportCSV">Export to CSV</a>
                                <a class="dropdown-item" href="#" id="exportExcel">Export to MS Excel</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped" id="newKeywordsTable">
                            <thead>
                                <tr>
                                    <th>Keyword</th>
                                    <th>Position</th>
                                    <th>Clicks</th>
                                    <th>Impressions</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($newKeywords as $keyword)
                                    <tr>
                                        <td>{{ $keyword->keyword ?? 'N/A' }}</td>
                                        <td>{{ $keyword->position ?? 'N/A' }}</td>
                                        <td>{{ $keyword->clicks ?? 'N/A' }}</td>
                                        <td>{{ $keyword->impression ?? 'N/A' }}</td>
                                        <td data-order="{{ $keyword->created_at->format('Y-m-d') }}">
                                            {{ $keyword->created_at->format('d-M-Y') }}
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-primary">Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        let newKeywordsTable;
        let selectedDays = 'all';

        function initializeTable() {
            newKeywordsTable = $('#newKeywordsTable').DataTable({
                columnDefs: [{
                    targets: 4,
                    type: 'date'
                }],
                order: [
                    [4, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        text: 'Export to CSV',
                        filename: 'new_keywords_export',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        },
                        action: function (e, dt, button, config) {
                            $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Export to MS Excel',
                        filename: 'new_keywords_export',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        },
                        action: function (e, dt, button, config) {
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                        }
                    }
                ]
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (selectedDays === 'all') {
                    return true;
                }

                let createdAt = new Date(data[4]);
                createdAt.setHours(0, 0, 0, 0);

                let cutoffDate = new Date();
                cutoffDate.setHours(0, 0, 0, 0);
                cutoffDate.setDate(cutoffDate.getDate() - selectedDays + 1);

                return createdAt >= cutoffDate;
            });
        }

        function filterTable() {
            newKeywordsTable.draw();
        }

        initializeTable();

        $('#dateFilter').on('change', function() {
            selectedDays = $(this).val() === 'all' ? 'all' : parseInt($(this).val());
            filterTable();
        });

        $('#exportCSV').on('click', function(e) {
            e.preventDefault();
            newKeywordsTable.button('.buttons-csv').trigger();
        });

        $('#exportExcel').on('click', function(e) {
            e.preventDefault();
            newKeywordsTable.button('.buttons-excel').trigger();
        });

        filterTable();

            // $('#newKeywordsTable').DataTable({
            //     columnDefs: [{
            //         targets: 'Created At',
            //         type: 'date'
            //     }],
            //     order: [
            //         [4, 'desc']
            //     ]
            // });

            $('#rankUpKeywordsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                order: [
                    [1, 'asc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [0, 3, 4]
                }]
            });
            $('#rankDownKeywordsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                order: [
                    [1, 'desc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [0, 3, 4]
                }]
            });

            // Keyword Performance Chart
            var ctx = document.getElementById('keywordPerformanceChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Improved', 'Declined', 'No Change'],
                    datasets: [{
                        label: 'Number of Keywords',
                        data: [{{ $keywordStats['up'] }}, {{ $keywordStats['down'] }},
                            {{ $keywordStats['same'] }}
                        ],
                        backgroundColor: ['rgba(40, 167, 69, 0.6)', 'rgba(220, 53, 69, 0.6)',
                            'rgba(255, 193, 7, 0.6)'
                        ],
                        borderColor: ['rgb(40, 167, 69)', 'rgb(220, 53, 69)', 'rgb(255, 193, 7)'],
                        borderWidth: 1
                    }, {
                        label: 'Average Position',
                        data: [{{ $keywordStats['avgPreviousPosition'] }},
                            {{ $keywordStats['avgCurrentPosition'] }}
                        ],
                        type: 'line',
                        fill: false,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Keywords'
                            }
                        },
                        y1: {
                            position: 'right',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Average Position'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Keyword Performance and Average Position'
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
@endpush
