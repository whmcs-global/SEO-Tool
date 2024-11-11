<div class="table-responsive" style="padding: 10px;">
    <table class="table table-striped mb-0">
        <thead>
            <tr>
                <th class="w-20">Page Path</th>
                <th class="w-20">Page Title</th>
                <th class="w-5">Active Users</th>
                <th class="w-5">New Users</th>
                <th class="w-5">Total Users</th>
                <th class="w-5">Organic Clicks</th>
                <th class="w-5">Organic Impressions</th>
                <th class="w-5">Organic CTR</th>
                <th class="w-5">Organic Avg Position</th>
                <th class="w-10">Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mergedReport as $data)
                <tr>
                    <td class="text-break">{{ $data['pagePath'] }}</td>
                    <td class="text-break">{{ $data['pageTitle'] }}</td>
                    <td>{{ $data['activeUsers'] }}</td>
                    <td>{{ $data['newUsers'] }}</td>
                    <td>{{ $data['totalUsers'] }}</td>
                    <td>{{ number_format($data['organicGoogleSearchClicks'], 2) }}</td>
                    <td>{{ number_format($data['organicGoogleSearchImpressions'], 2) }}</td>
                    <td>{{ number_format($data['organicGoogleSearchClickThroughRate'], 2) }}</td>
                    <td>{{ number_format($data['organicGoogleSearchAveragePosition'], 2) }}</td>
                    <td>
                        <a href="{{ route('page.show', [
                            'url' => $data['pagePath'],
                            'start_date' => $startDate,
                            'end_date' => $endDate
                        ]); }}"
                           class="btn btn-primary">Details</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No data available</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="w-20">Total</th>
                <th class="w-20"></th>
                <th class="w-5">{{ $pageTotals['activeUsers'] }}</th>
                <th class="w-5">{{ $pageTotals['newUsers'] }}</th>
                <th class="w-5">{{ $pageTotals['totalUsers'] }}</th>
                <th class="w-5">{{ number_format($organicTotals['organicGoogleSearchClicks'], 2) }}</th>
                <th class="w-5">{{ number_format($organicTotals['organicGoogleSearchImpressions'], 2) }}</th>
                <th class="w-5">{{ number_format($organicTotals['organicGoogleSearchClickThroughRate'], 2) }}</th>
                <th class="w-10">{{ number_format($organicTotals['organicGoogleSearchAveragePosition'], 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
