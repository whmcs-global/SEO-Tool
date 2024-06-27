@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="filter-country-main">
        <div class="mb-4 d-flex justify-content-end align-items-center">
            <div class="me-2 mb-2 mb-md-0 position-relative" style="width:100%">
                <form action="{{ route('keywords.details') }}" method="GET" id="filterForm">
                     <div class="filter-country-inner">
                        <h5>Position Filters for All Countries:</h5>
                        <h6 style="margin-left: 360px;">select date range</h6>
                        <input type="text" readonly name="daterange" class="form-control" id="dateRangeInput" placeholder="Select Date Range" value="{{ $startDate && $endDate ?  $startDate. ' - ' .$endDate  : '' }}"  style="width: 250px !important; "/>
                    </div>
                    <input type="hidden" name="positionFilter" id="positionFilter" value="{{ $positionFilter ?? '' }}">
                    <input type="hidden" name="country" value="{{ $selectedCountry }}">
                </form>
            </div>
        </div>

        <div class="overflow-auto">
        @php
    $ranges = ['all', 'top_1', 'top_3', 'top_5', 'top_10', 'top_30', 'top_100'];
@endphp

@foreach ($countries as $country)
    <div class="position-filters mb-4">
        <h6>
            <img src="https://flagcdn.com/32x24/{{ strtolower($country->code) }}.png"
                 alt="{{ $country->name }} flag"
                 class="me-1">
            {{ $country->name }}
        </h6>
        <div class="d-flex gap-2 mb-3 filter-countries-row">
            @foreach ($ranges as $range)
                @php
                    if ($range === 'all') {
                        $count = $totalKeywords;
                        $percentage = 100;
                        $difference = 0;
                    } else {
                        $count = $countryRanges[$country->id][$range]['end_count'];
                        $startCount = $countryRanges[$country->id][$range]['start_count'];
                        $difference = $count - $startCount;
                        $percentage = $totalKeywords > 0 ? ($count / $totalKeywords * 100) : 0;
                    }
                @endphp
                <div class="country_filter">
                    <small class="text-muted">
                        {{ $range === 'top_100' ? '>' : '' }}{{ number_format($percentage, 0) }}%
                    </small>
                    <button type="button" class="position-filter-btn btn btn-light border flex-shrink-0 {{ $positionFilter == $range && $country->id == $selectedCountry ? 'active' : '' }}" onclick="applyFilter('{{ $country->id }}', '{{ $range }}')">
                        <span class="small">{{ strtoupper(str_replace('_', ' ', $range)) }}</span>
                    </button>
                    <strong>
                        {{ $count }}
                        @if ($difference > 0)
                            <span class="text-success">▲ {{ $difference }}</span>
                        @elseif ($difference < 0)
                            <span class="text-danger">▼ {{ abs($difference) }}</span>
                        @endif
                    </strong>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
        </div>
    </div>

    <div class="filter-country-main">
    <!-- Keyword Table -->
    <h5>
        <img src="https://flagcdn.com/32x24/{{ strtolower($countries->firstWhere('id', $selectedCountry)->code) }}.png"
             alt="{{ $countries->firstWhere('id', $selectedCountry)->name }} flag"
             class="me-1">
        Keyword Data for {{ $countries->firstWhere('id', $selectedCountry)->name }}:
    </h5>
    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered keyword-data-countries">
            <thead>
                <tr>
                    <th class="sticky-col">KEYWORDS</th>
                    <th>SEARCH VOL.</th>
                    <th>IMPRESSION</th>
                    <th>COMPETITION</th>
                    @foreach ($allDates as $date)
                        <th>{{ $date }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($keywordData as $data)
                <tr>
                    <td class="sticky-col">{{ $data['keyword'] }}</td>
                    <td>{{ $data['search_volume'] }}</td>
                    <td>{{ $data['impression'] }}</td>
                    <td>{{ $data['competition'] }}</td>
                    @foreach ($allDates as $index => $date)
                    <td>
                        @php
                            $currentPosition = $data['positions'][$date] ?? '-';
                            $previousPosition = $index > 0 ? $data['positions'][$allDates[$index - 1]] ?? '-' : '-';
                            $positionChange = is_numeric($currentPosition) && is_numeric($previousPosition)
                                                ? (int)($currentPosition - $previousPosition)
                                                : null;
                        @endphp
                        {{ is_numeric($currentPosition) ? (int)$currentPosition : $currentPosition }}
                        @if (is_numeric($positionChange) && $positionChange != 0)
                            <small class="{{ $positionChange < 0 ? 'text-success' : 'text-danger' }}">
                                {{ $positionChange < 0 ? '▲' : '▼' }} {{ abs($positionChange) }}
                            </small>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
        </div>
</div>
@endsection

@push('styles')
<style>
.filter-country-inner{
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.filter-country-inner h5 {
    margin-bottom: 0;
}
    .sticky-col {
        position: sticky;
        left: 0;
        background-color: #fff !important;
        z-index: 1;
    }
    .table-responsive {
        overflow-x: auto;
        max-width: 100%;
    }
    #country {
        padding-left: 30px;
        background-repeat: no-repeat;
        background-position: 5px center;
        background-size: 20px;
    }

    .position-filter-btn.active {
        background-color: #007bff;
        color: white;
    }
    .filter-countries-row {
        gap: 10px;
    }
    .country_filter {
    display: flex;
    flex-direction: column;
    align-items: center;
}
.keyword-data-countries td, .keyword-data-countries th {
    border-color: #cccccc !important;
}
.table.keyword-data-countries:not(.table-sm) thead th {
    border: 1px solid #cccccc;
}
.text-muted.text-hidden {
    visibility: hidden;
}
.country_filter .active {
    color: #ffffff !important;
    background-color: #6778f0 !important;
}
.filter-country-main {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(document).ready(function() {
    $('input[name="daterange"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
        },
        maxDate: moment(), //.subtract(3, 'days')
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        }
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        $('#filterForm').submit();
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#filterForm').submit();
    });

    var selectedOption = $('#country').find('option:selected');
    var flagUrl = selectedOption.data('flag');
    $('#country').css('background-image', 'url(' + flagUrl + ')');
});

function applyFilter(countryId, filter) {
    $('input[name="country"]').val(countryId);
    $('input[name="positionFilter"]').val(filter);
    $('#filterForm').submit();
}

</script>
@endpush