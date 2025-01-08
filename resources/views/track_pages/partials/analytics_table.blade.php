<div class="table-responsive" style="padding: 10px;">
    <table class="table table-striped mb-0">
        <thead>
            <tr>
                <th class="w-20">Page Information</th>
                {{-- <th class="w-5">Source / Medium</th> --}}
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
                    <td class="text-break">
                        <div class="wrap-text">{{ $data['pagePath'] }}</div>
                        {{-- <small class="text-muted wrap-text">{{ $data['pageTitle'] }}</small> --}}
                    </td>
                    {{-- <td class="source-column text-break">
                        @include('track_pages.partials.source-icon', [
                            'source' => $data['sessionSourceMedium'],
                        ])
                        {{ $data['sessionSourceMedium'] }}
                    </td> --}}
                    <td>{{ $data['newUsers'] }}</td>
                    <td>{{ $data['totalUsers'] }}</td>
                    <td>{{ number_format($data['organicGoogleSearchClicks'], 2) }}</td>
                    <td>{{ number_format($data['organicGoogleSearchImpressions'], 2) }}</td>
                    <td>{{ number_format($data['organicGoogleSearchClickThroughRate'], 2) }}</td>
                    <td>{{ number_format($data['organicGoogleSearchAveragePosition'], 2) }}</td>
                    <td>
                        <a href="{{ route('page.show', ['url' => $data['pagePath'], 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-primary">Details</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No data available</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                {{-- <th></th> --}}
                <th>{{ $pageTotals['newUsers'] }}</th>
                <th>{{ $pageTotals['totalUsers'] }}</th>
                <th>{{ number_format($organicTotals['organicGoogleSearchClicks'], 2) }}</th>
                <th>{{ number_format($organicTotals['organicGoogleSearchImpressions'], 2) }}</th>
                <th>{{ number_format($organicTotals['organicGoogleSearchClickThroughRate'], 2) }}</th>
                <th>{{ number_format($organicTotals['organicGoogleSearchAveragePosition'], 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
