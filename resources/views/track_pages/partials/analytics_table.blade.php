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
        <tbody>
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
