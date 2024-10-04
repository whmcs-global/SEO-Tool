@extends('layouts.admin')

@section('title', 'Cron Logs')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-list-alt"></i> Cron Logs for: {{ $crons->first()->cron_name ?? 'Cron Job' }}</h4>
                </div>
                <div class="card-body p-0">
                    @foreach ($crons as $cron)
                        <div class="p-3 mb-3" style="border-bottom: 1px solid #ddd;">
                            <h5><i class="fas fa-calendar-alt"></i> Cron Name: {{ $cron->cron_name }}</h5>
                            <p><strong><i class="fas fa-clock"></i> Date:</strong> {{ $cron->created_at->format('d M, Y h:i A') }}</p>
                            <p><strong><i class="fas fa-info-circle"></i> Status:</strong>
                                @if ($cron->status == 1)
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Success</span>
                                @elseif($cron->status == 2)
                                    <span class="badge badge-warning"><i class="fas fa-hourglass-half"></i> Running</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Failed</span>
                                @endif
                            </p>

                            <h5 class="mt-4"><i class="fas fa-file-alt"></i> External API Logs</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 15%"><i class="fas fa-network-wired"></i> API Name</th>
                                            <th style="width: 15%"><i class="fas fa-link"></i> Endpoint</th>
                                            {{-- <th style="width: 10%"><i class="fas fa-code"></i> Method</th> --}}
                                            <th style="width: 10%"><i class="fas fa-flag"></i> Status Code</th>
                                            <th style="width: 20%"><i class="fas fa-info-circle"></i> Description</th>
                                            <th style="width: 15%"><i class="fas fa-database"></i> Request Data</th>
                                            <th style="width: 15%"><i class="fas fa-server"></i> Response Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cron->externalApiLogs as $log)
                                            <tr>
                                                <td>{{ $log->api_name }}</td>
                                                <td>
                                                    <a href="{{ $log->endpoint }}" target="_blank">
                                                        {{ \Illuminate\Support\Str::limit($log->endpoint, 30) }}
                                                    </a>
                                                </td>
                                                {{-- <td><span class="badge badge-info">{{ strtoupper($log->method) }}</span></td> --}}
                                                <td><span class="badge badge-dark">{{ $log->status_code }}</span></td>
                                                <td>{{ \Illuminate\Support\Str::limit($log->description, 50) }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#requestDataModal{{ $log->id }}">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <!-- Request Data Modal -->
                                                    <div class="modal fade" id="requestDataModal{{ $log->id }}" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Request Data</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <pre>{{ json_encode($log->request_data, JSON_PRETTY_PRINT) }}</pre>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#responseDataModal{{ $log->id }}">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <!-- Response Data Modal -->
                                                    <div class="modal fade" id="responseDataModal{{ $log->id }}" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Response Data</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <pre>{{ json_encode($log->response_data, JSON_PRETTY_PRINT) }}</pre>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
