@extends('layouts.admin')

@section('title', 'Track Pages')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="card-title"><i class="fas fa-chart-line text-primary me-2"></i>Page Analytics:
                                {{ $url ?? '/' }}</h5>
                            <p class="text-muted" style="margin-left: 14px;"><i class="far fa-calendar-alt me-2"></i>Period: {{ $startDate }} to
                                {{ $endDate }}</p>
                        </div>
                        <div class="d-flex">
                            <div class="summary-card">
                                <h6 class="text-muted">New Users</h6>
                                <h3>{{ number_format($pageTotals['newUsers']) }}</h3>
                            </div>
                            <div class="summary-card">
                                <h6 class="text-muted">Total Users</h6>
                                <h3>{{ number_format($pageTotals['totalUsers']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-traffic-light text-primary me-2"></i>Traffic Sources</h5>
                    <div class="table-container">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th><i class="fas fa-link text-muted me-2"></i>Source / Medium</th>
                                    <th class="text-center"><i class="fas fa-user-plus text-muted me-2"></i>New Users</th>
                                    <th class="text-center"><i class="fas fa-users text-muted me-2"></i>Total Users</th>
                                    <th class="text-center"><i class="fas fa-percentage text-muted me-2"></i>% New Users
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pageReport as $data)
                                    <tr>
                                        <td class="source-column">
                                            @include('track_pages.partials.source-icon', [
                                                'source' => $data['sessionSourceMedium'],
                                            ])
                                            {{ $data['sessionSourceMedium'] }}
                                        </td>
                                        <td class="text-center">{{ number_format($data['newUsers']) }}</td>
                                        <td class="text-center">{{ number_format($data['totalUsers']) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">
                                                {{ $data['totalUsers'] > 0 ? round(($data['newUsers'] / $data['totalUsers']) * 100, 1) : '0' }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light sticky-bottom">
                                <tr>
                                    <td><i class="fas fa-calculator text-primary me-2"></i>Total</td>
                                    <td class="text-center">{{ number_format($pageTotals['newUsers']) }}</td>
                                    <td class="text-center">{{ number_format($pageTotals['totalUsers']) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">
                                            {{ $pageTotals['totalUsers'] > 0 ? round(($pageTotals['newUsers'] / $pageTotals['totalUsers']) * 100, 1) : '0' }}%
                                        </span>
                                    </td>
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
    <style>
        .summary-card {
            border: 1px solid #dee2e6;
            padding: 1rem;
            border-radius: .25rem;
            text-align: center;
            width: 50%;
            margin-left: 1rem;
        }

        .hover-shadow:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .sticky-top,
        .sticky-bottom {
            position: sticky;
            z-index: 2;
            background-color: #f8f9fa;
        }

        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .table th,
        .table td {
            padding: 1rem;
        }

        .table-container::-webkit-scrollbar {
            width: 6px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        /* Card hover effect */
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .sticky-bottom {
            position: sticky;
            bottom: 0;
            z-index: 2;
            background-color: #f8f9fa;
        }

        /* Table styles */
        .table-container {
            border-radius: 0.25rem;
            border: 1px solid #dee2e6;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }

        .table td,
        .table th {
            vertical-align: middle;
            padding: 1rem;
        }

        /* Transition effects */
        .transition {
            transition: all 0.3s ease;
        }

        /* Badge styles */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* Custom scrollbar */
        .table-container::-webkit-scrollbar {
            width: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .main-content {
            padding-top: 35px !important;
        }
    </style>
@endpush
