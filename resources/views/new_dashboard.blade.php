@extends('layouts.admin')

@section('title')
    Dashboard
@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        :root {
            --primary-color: #343a40;
            --secondary-color: #6c757d;
            --background-color: #f8f9fa;
            --hover-background-color: #f5f5f5;
            --border-radius: 8px;
            --box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            --green: green;
            --red: red;
            --gray: gray;
            --improved-bg: #7cd792;
            --declined-bg: #ffa3ab;
        }

        #keywordPerformanceChart {
            height: 300px !important;
        }

        #weeklyPerformanceChart {
            height: 400px;
        }

        .position-change {
            display: inline-flex;
            align-items: center;
        }

        .up {
            color: var(--green);
            margin-left: 5px;
        }

        .down {
            color: var(--red);
            margin-left: 5px;
        }

        .same {
            color: var(--gray);
            margin-left: 5px;
        }

        .table-hover tbody tr:hover {
            background-color: var(--hover-background-color);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .thead-dark th {
            background-color: var(--primary-color);
            color: white;
        }

        .dataTables_wrapper.no-footer {
            margin: 10px;
        }

        .metric-card,
        .metric-card-improved,
        .metric-card-declined {
            background-color: var(--background-color);
            border-radius: var(--border-radius);
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: var(--box-shadow);
            transition: transform 0.2s;
        }

        .metric-card:hover,
        .metric-card-improved:hover,
        .metric-card-declined:hover {
            transform: translateY(-5px);
        }

        .metric-title {
            font-size: 14px;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .metric-card-improved {
            background-color: var(--improved-bg);
        }

        .metric-card-declined {
            background-color: var(--declined-bg);
        }

        .dt-buttons {
            display: none;
        }

        .dataTables_filter {
            margin-bottom: 0.5rem;
        }

        .dataTables_length {
            font-size: 0.875rem;
        }

        .dataTables_paginate {
            font-size: 0.875rem;
        }

        table.dataTable thead th {
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        table.dataTable tbody td {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        #labelsTable tr:hover {
            cursor: pointer;
            background-color: rgba(0, 0, 0, .075);
        }

        .dataTables_length{
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-4 col-lg-8">
            <div class="card shadow border-light">
                <div class="card-header bg-light text-center">
                    <h4 class="card-title">Google Analytics Overview (Only for HostingSeekers)</h4>
                </div>
                <div class="card-body">
                    <div class="container">
                        <div class="row text-center">

                            <div class="col-12 col-md-4">
                                <div class="card" style="margin-bottom: 0px !important">
                                    <div class="m-2">
                                        <h5 class="card-title">Last 28 Days</h5>
                                        <div class="row">
                                            <div class="col-6">
                                                <h6>Total Users</h6>
                                                <h3 class="text-dark">
                                                    {{ number_format($formattedReport['totals']['date_range_1']['totalUsers'] ?? 0) }}
                                                </h3>
                                            </div>
                                            <div class="col-6">
                                                <h6>New Users</h6>
                                                <h3 class="text-success">
                                                    {{ number_format($formattedReport['totals']['date_range_1']['newUsers'] ?? 0) }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card" style="margin-bottom: 0px !important">
                                    <div class="m-2">
                                        <h5 class="card-title">Last 7 Days</h5>
                                        <div class="row">
                                            <div class="col-6">
                                                <h6>Total Users</h6>
                                                <h3 class="text-dark">
                                                    {{ number_format($formattedReport['totals']['date_range_0']['totalUsers'] ?? 0) }}
                                                </h3>
                                            </div>
                                            <div class="col-6">
                                                <h6>New Users</h6>
                                                <h3 class="text-success">
                                                    {{ number_format($formattedReport['totals']['date_range_0']['newUsers'] ?? 0) }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-12 col-md-4">
                                <div class="card" style="margin-bottom: 0px !important">
                                    <div class="m-2">
                                        <h5 class="card-title">Yesterday</h5>
                                        <div class="row">
                                            <div class="col-6">
                                                <h6>Total Users</h6>
                                                <h3 class="text-dark">
                                                    {{ number_format($yesterdayUsers['totalUsers'] ?? 0) }}
                                                </h3>
                                            </div>
                                            <div class="col-6">
                                                <h6>New Users</h6>
                                                <h3 class="text-success">
                                                    {{ number_format($yesterdayUsers['newUsers'] ?? 0) }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <canvas id="analyticsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4 col-lg-4">
            <div class="card shadow border-light mb-4">
                <div class="">
                    <table class="table table-hover" id="labelsTable">
                        <thead>
                            <tr>
                                <th>Label Name</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($labels as $label)
                                <tr>
                                    <td>
                                        <a href="{{ route('keywords.details', ['labels[]' => $label->id]) }}"
                                            class="d-block w-100 text-decoration-none text-reset">
                                            {{ $label->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('keywords.details', ['labels[]' => $label->id]) }}"
                                            class="d-block w-100 text-decoration-none text-reset">
                                            {{ $label->keyword_count }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4>Keyword Performance Overview</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="weeklyPerformanceChart"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-card">
                                <div class="metric-title">Total Keywords</div>
                                <div class="metric-value">{{ $keywordStats['total'] }}</div>
                            </div>

                            <div class="metric-card-improved">
                                <div class="metric-title">Top Improved Keyword</div>
                                <div class="metric-value">{{ $keywordStats['topImproved']['keyword'] ?? 'N/A' }}</div>
                                <div>Change: +{{ number_format($keywordStats['topImproved']['change'] ?? 0, 2) }}</div>
                            </div>
                            <div class="metric-card-declined">
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
                        @can('Export GSC Data')
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
                        @endcan
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
                                            <a href="{{ route('keywords.edit', $keyword->keyword_id) }}"
                                                class="btn btn-primary">Assign Label</a>
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
            // $('#country').change(function() {
            //     var countryId = $(this).val();
            //     $.ajax({
            //         url: '{{ route('countries.set') }}',
            //         type: 'GET',
            //         data: {
            //             country_id: countryId
            //         },
            //         success: function(response) {
            //             location.reload();
            //         },
            //         error: function(xhr, status, error) {
            //             console.error('AJAX Error: ' + status + error);
            //         }
            //     });
            // });

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
                    @can('Export GSC Data')
                        dom: 'Bfrtip',
                        buttons: [{
                                extend: 'csv',
                                text: 'Export to CSV',
                                filename: '{{ $filename }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                },
                                action: function(e, dt, button, config) {
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt,
                                        button, config);
                                }
                            },
                            {
                                extend: 'excel',
                                text: 'Export to MS Excel',
                                filename: '{{ $filename }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                },
                                action: function(e, dt, button, config) {
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt,
                                        button, config);
                                }
                            }
                        ]
                    @endcan
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
            // Weekly Data Line Chart
            var weeklyCtx = document.getElementById('weeklyPerformanceChart').getContext('2d');
            var weeklyLabels = Object.keys(@json($keywordStats['weeklyData']));
            var weeklyUpData = Object.values(@json($keywordStats['weeklyData'])).map(data => data.up);
            var weeklyDownData = Object.values(@json($keywordStats['weeklyData'])).map(data => data.down);
            var weeklySameData = Object.values(@json($keywordStats['weeklyData'])).map(data => data.same);

            var weeklyChart = new Chart(weeklyCtx, {
                type: 'line',
                data: {
                    labels: weeklyLabels,
                    datasets: [{
                            label: 'Keywords Increased',
                            data: weeklyUpData,
                            borderColor: 'rgb(40, 167, 69)',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Keywords Decreased',
                            data: weeklyDownData,
                            borderColor: 'rgb(220, 53, 69)',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Keywords No Change',
                            data: weeklySameData,
                            borderColor: 'rgb(255, 193, 7)',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 20,
                            right: 20,
                            bottom: 20,
                            left: 20
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Keywords',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Keyword Positions Over Past Week',
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            padding: {
                                top: 10,
                                bottom: 30
                            }
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 12
                            },
                            displayColors: false
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    elements: {
                        point: {
                            radius: 4,
                            hoverRadius: 6
                        }
                    }
                }
            });

            const table = $('#labelsTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10],
                "dom": 'rt<"bottom"lp><"clear">',
                "ordering": false,
                "info": false,
                "responsive": true,
                "autoWidth": false,
                "searching": false
            });

            $('#labelsTable').on('mousedown', 'a', function(e) {
                if (e.which === 2) {
                    e.preventDefault();
                    window.open(this.href, '_blank');
                }
            });

            $('#labelsTable').on('click', 'a', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    window.open(this.href, '_blank');
                }
            });
        });
    </script>
    <script>
        const data = @json($formattedReport['results']);
        data.sort((a, b) => new Date(a.date) - new Date(b.date));

        const ctx = document.getElementById('analyticsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric'
                    });
                }),
                datasets: [{
                        label: 'Total Users',
                        data: data.map(item => item.totalUsers),
                        backgroundColor: '#007bff',
                        borderColor: '#007bff',
                        borderWidth: 1,
                        barPercentage: 0.5
                    },
                    {
                        label: 'New Users',
                        data: data.map(item => item.newUsers),
                        backgroundColor: '#28a745',
                        borderColor: '#28a745',
                        borderWidth: 1,
                        barPercentage: 0.5
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        stacked: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    </script>
@endpush
