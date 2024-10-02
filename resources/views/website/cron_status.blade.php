@extends('layouts.admin')

@section('title', 'Cron Status')

@section('content')
    <div class="container-fluid">
        <!-- Cron Status Card -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-gradient-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-clock mr-2"></i>Cron Status</h3>
                    </div>
                    <div class="card-body">
                        @if (!$cron_status)
                            <div class="alert alert-danger animate__animated animate__shakeX" role="alert">
                                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>Cron Not
                                    Configured!</h4>
                                <p class="ml-4">It seems like the cron job is not configured. Please follow the
                                    instructions below to configure the cron job:</p>
                                <hr>
                                <div class="mt-4">
                                    <h5><i class="fas fa-list-ol mr-2"></i>Instructions:</h5>
                                    <ol class="list-group list-group-numbered mt-3">
                                        <li class="list-group-item">Open your terminal.</li>
                                        <li class="list-group-item">Run the following command to edit the crontab:
                                            <pre><code class="bg-light p-2 mt-2 rounded">crontab -e</code></pre>
                                        </li>
                                        <li class="list-group-item">Add the following line to the crontab file:
                                            <pre><code class="bg-light p-2 mt-2 rounded">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code></pre>
                                        </li>
                                        <li class="list-group-item">Save and close the crontab file.</li>
                                    </ol>
                                    <a href="https://laravel.com/docs/11.x/scheduling" target="_blank"
                                        class="btn btn-info mt-3">
                                        <i class="fas fa-book-open mr-2"></i>Read More
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success animate__animated animate__bounceIn" role="alert">
                                <h4 class="alert-heading"><i class="fas fa-check-circle mr-2"></i>Cron Configured!</h4>
                                <p class="ml-4">The cron job is configured and running successfully.</p>
                                <hr>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <p><i class="far fa-clock mr-2"></i>Last run time:
                                            {{ $lastRunTime->format('d M, Y h:i A') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><i class="fas fa-history mr-2"></i>Last run was {{ $hoursAgo }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Cron List Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg">
                    <div class="card-header bg-gradient-secondary text-white">
                        <h4 class="mb-0"><i class="fas fa-list mr-2"></i>Cron List</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="cronTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th><i class="fas fa-tag mr-2"></i>Cron Name</th>
                                        <th><i class="far fa-calendar-alt mr-2"></i>Date</th>
                                        <th><i class="fas fa-info-circle mr-2"></i>Status</th>
                                        <th><i class="fas fa-cogs mr-2"></i>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($crons as $cron)
                                    <tr>
                                        <td>{{ $cron->cron_name }}</td>
                                        <td>{{ $cron->created_at->format('d M, Y h:i A') }}</td>
                                        <td>
                                            @if ($cron->status == 1)
                                                <span class="badge badge-success" data-toggle="tooltip" title="Cron executed successfully">
                                                    <i class="fas fa-check mr-1"></i>Success
                                                </span>
                                            @elseif($cron->status == 2)
                                                <span class="badge badge-warning" data-toggle="tooltip" title="Cron is currently running">
                                                    <i class="fas fa-spinner fa-spin mr-1"></i>Running
                                                </span>
                                            @else
                                                <span class="badge badge-danger" data-toggle="tooltip" title="Cron failed to execute">
                                                    <i class="fas fa-times mr-1"></i>Failed
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.cron.logs', ['id' => $cron->id]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-file-alt mr-1"></i>Logs
                                            </a>
                                            @if ($cron->status == 0 && $cron->created_at->isToday())
                                                <button class="btn btn-danger btn-sm retry-cron" data-id="{{ $cron->id }}">
                                                    <i class="fas fa-redo mr-1"></i>Retry
                                                </button>
                                            @endif
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
    </div>

@endsection

@push('scripts')
    <script>
    $(document).ready(function() {
        $('.retry-cron').click(function() {
            var cronId = $(this).data('id');
            var button = $(this);
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin mr-1"></i>Retrying');

            $.ajax({
                url: '{{ route('admin.cron.retry', '') }}/' + cronId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        button.closest('tr').find('.badge').removeClass('badge-danger').addClass('badge-warning')
                            .html('<i class="fas fa-spinner fa-spin mr-1"></i>Running');
                        button.remove();
                    } else {
                        button.prop('disabled', false);
                        button.html('<i class="fas fa-redo mr-1"></i>Retry');
                        alert('Failed to retry cron job.');
                    }
                },
                error: function() {
                    button.prop('disabled', false);
                    button.html('<i class="fas fa-redo mr-1"></i>Retry');
                    alert('Failed to retry cron job.');
                }
            });
        });
    });
    </script>
@endpush
