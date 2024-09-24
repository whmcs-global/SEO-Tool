@extends('layouts.admin')

@section('title', 'Cron Status')
@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">Cron Status</div>
                <div class="card-body">
                    @if (!$cron_status)
                        <div class="alert alert-danger animate__animated animate__shakeX" role="alert">
                            <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Cron Not Configured!</h4>
                            <p style="margin-left: 15rem;">It seems like the cron job is not configured. Please follow the
                                instructions below to
                                configure the cron job:</p>
                            <hr>
                        </div>
                        <div class="mt-4">
                            <h5>Instructions:</h5>
                            <ol>
                                <li>Open your terminal.</li>
                                <li>Run the following command to edit the crontab:
                                    <pre><code>crontab -e</code></pre>
                                </li>
                                <li>Add the following line to the crontab file:
                                    <pre><code>* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code></pre>
                                </li>
                                <li>Save and close the crontab file.</li>
                            </ol>
                            <a href="https://laravel.com/docs/11.x/scheduling" target="_blank"
                                class="btn btn-info mt-3">Read More</a>
                        </div>
                    @else
                        <div class="alert alert-success animate__animated animate__bounceIn" role="alert">
                            <h4 class="alert-heading"><i class="fas fa-check-circle"></i> Cron Configured!</h4>
                            <p style="margin-left: 15rem;">The cron job is configured and running successfully.</p>
                            <hr>
                            <button class="btn btn-primary" onclick="runCronNow()">Run Now</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Cron List</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped" id="newKeywordsTable">
                            <thead>
                                <tr>
                                    <th>Cron Name</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($crons as $cron)
                                    <tr>
                                        <td>{{ $cron->cron_name }}</td>
                                        <td>{{ $cron->created_at->format('d M, Y h:i A') }}</td>
                                        <td>
                                            @if ($cron->status == 1)
                                                <span class="badge badge-success">Success</span>
                                            @elseif($cron->status == 2)
                                                <span class="badge badge-warning">Running</span>
                                            @else
                                                <span class="badge badge-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.cron.logs', ['id'=>$cron->id])}}" class="btn btn-primary">Logs</a>
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
    <script>
        function runCronNow() {
            fetch('/', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cron job executed successfully.');
                    } else {
                        alert('Failed to execute cron job.');
                    }
                });
        }
    </script>
@endpush
