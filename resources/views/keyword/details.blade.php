@extends('layouts.admin')

@section('title', 'Keyword Tracker')

@section('content')
<div class="card">
    <div class="filter-country-main">
        <div class="mb-4 d-flex justify-content-end align-items-center">
            <div class="mb-2 me-2 mb-md-0 position-relative" style="width:100%">
                <form action="{{ route('keywords.details') }}" method="GET" id="filterForm">
                     <div class="filter-country-inner">
                        <h5>Position Filters for All Countries:</h5>
                        <div class="mb-3 mb-md-0" style="display:flex; align-items:center; gap:10px;">
                            <label for="dateRangeInput" class="form-label">Select Date Range</label>
                            <div>
                                <div class="input-group">
                                    <input type="text"
                                        readonly
                                        name="daterange"
                                        class="form-control"
                                        id="dateRangeInput"
                                        placeholder="Select Date Range"
                                        value="{{ $startDate && $endDate ?  $startDate. ' - ' .$endDate  : '' }}">
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                @role('Admin|Super Admin')
                                <div class="col-auto">
                                    <label for="users" class="font-weight-bold">Filter by Users:</label>
                                </div>
                                <div class="col">
                                    <select name="users[]" id="field1" class="form-control" multiple multiselect-search="true"
                                        multiselect-max-items="3">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ in_array($user->id, $userIds) ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endrole
                                <div class="col-auto">
                                    <label for="labels" class="font-weight-bold">Filter by Labels:</label>
                                </div>
                                <div class="col">
                                    <select name="labels[]" id="field2" class="form-control" multiple multiselect-search="true"
                                        multiselect-max-items="3">
                                        @foreach ($labels as $label)
                                            <option value="{{ $label->id }}"
                                                {{ in_array($label->id, $labelIds) ? 'selected' : '' }}>
                                                {{ $label->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                            <input type="hidden" name="positionFilter" id="positionFilter" value="{{ $positionFilter ?? '' }}">
                            <input type="hidden" name="country" value="{{ $selectedCountry }}">
                        </div>
                     </div>
                </form>
            </div>
        </div>
        <div class="overflow-auto">
        @php
            $ranges = ['all', 'top_5', 'top_10', 'top_50', 'top_100'];
        @endphp
        @foreach ($countries as $country)
            <div class="mb-4 position-filters">
                <h6>
                    <img src="https://flagcdn.com/32x24/{{ strtolower($country->code) }}.png"
                        alt="{{ $country->name }} flag"
                        class="me-1">
                    {{ $country->name }}
                </h6>
                <div class="gap-2 mb-3 d-flex filter-countries-row">
                    @foreach ($ranges as $range)
                        @php
                            if ($range === 'all') {
                                $count = $totalKeywords;
                                $percentage = 100;
                                $difference = 0;
                            } else {
                                $count = $countryRanges[$country->id][$range]['end_count'] ?? 0;
                                $startCount = $countryRanges[$country->id][$range]['start_count'] ?? 0;
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
    <div class="mt-4 table-responsive">
        <table id="keywordTable" class="table table-striped table-bordered keyword-data-countries">
            <thead>
                <tr>
                    <th class="sticky-col">KEYWORDS</th>
                    <th>SEARCH VOL.</th>
                    <th>IMPRESSION</th>
                    <th>COMPETITION</th>
                    @foreach ($allDates as $date)
                        <th colspan="3" class="text-center">{{ $date }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th class="sticky-col"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    @foreach ($allDates as $date)
                        <th>Position</th>
                        <th>Clicks</th>
                        <th>Impressions</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($keywordData as $data)
                    <tr>
                        <td class="sticky-col">
                            <div>
                                <span>{{ $data['keyword'] }}</span>
                                <div>
                                    @foreach ($data['keyword_label'] as $label)
                                        <span class="badge badge-info">{{ $label }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td>{{ $data['search_volume'] }}</td>
                        <td>{{ $data['impression'] }}</td>
                        <td>{{ $data['competition'] }}</td>
                        @foreach ($allDates as $index => $date)
                            @php
                                $positionData = $data['positions'][$date] ?? ['position' => '-', 'clicks' => '-', 'impressions' => '-'];
                                $currentPosition = is_numeric($positionData['position']) ? (int)$positionData['position'] : $positionData['position'];                        $previousPosition = $index > 0 ? $data['positions'][$allDates[$index - 1]]['position'] ?? '-' : '-';
                                $positionChange = is_numeric($currentPosition) && is_numeric($previousPosition)
                                                ? (int)($currentPosition - $previousPosition)
                                                : null;
                            @endphp
                            <td>
                                {{ $currentPosition }}
                                @if (is_numeric($positionChange) && $positionChange != 0)
                                    <small class="{{ $positionChange < 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $positionChange < 0 ? '▲' : '▼' }} {{ abs($positionChange) }}
                                    </small>
                                @endif
                            </td>
                            <td>{{ $positionData['clicks'] }}</td>
                            <td>{{ $positionData['impressions'] }}</td>
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
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
<style>
/* General Styles */
.position-filters {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
    margin-bottom: 20px;
}

.keyword-data-countries .sticky-col {
    min-width: 200px;
    max-width: 250px;
    width: 25%;
    white-space: normal;
    word-wrap: break-word;
    word-break: break-word;
}

.keyword-data-countries .sticky-col > div {
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
}

.keyword-data-countries th,
.keyword-data-countries td {
    min-width: 100px;
}
.filter-country-inner {
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

/* Button Styles */
.position-filter-btn.active, .country_filter .active {
    background-color: #6778f0 !important;
    color: white !important;
}

.position-filter-btn.active {
    background-color: #007bff;
}

/* Country Filter Styles */
.country_filter {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.filter-countries-row {
    display: flex;
    gap: 10px;
    align-items: center;
}

/* Table Styles */
.keyword-data-countries td, .keyword-data-countries th {
    border-color: #cccccc !important;
}

.table.keyword-data-countries:not(.table-sm) thead th {
    border: 1px solid #cccccc;
}

.text-muted.text-hidden {
    visibility: hidden;
}

/* Filter Country Main Styles */
.filter-country-main {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

/* Multiselect Dropdown Styles */
.multiselect-dropdown {
    width: 100%;
    min-height: 46px;
    font-size: 13px;
    color: #323232;
    letter-spacing: 0.2px;
    border: 1px solid #9F9FA0;
    border-radius: 6px;
    padding: 9px 20px;
    background-color: whitesmoke !important;
    max-width: 120px;
}

/* Position Filters Styles */
.position-filters {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
}

.position-filters.mb-4 {
    justify-content: space-between;
    /* flex-wrap: wrap; */
}

.position-filters h6 {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    margin: 0;
    min-width: 150px;
}

.position-filters > div {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    width: 100%;
    flex-wrap: wrap;
}

/* Additional styles for DataTables */
.dataTables_wrapper .dataTables_filter {
    float: right;
    margin-bottom: 10px;
}

.dataTables_wrapper .dataTables_length {
    float: left;
    margin-bottom: 10px;
}

.dataTables_wrapper .dataTables_info {
    clear: both;
    float: left;
    padding-top: 0.755em;
}

.dataTables_wrapper .dataTables_paginate {
    float: right;
    padding-top: 0.25em;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/custom/multiselect-dropdown.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('input[name="daterange"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
        },
        minDate: moment().subtract(90, 'days'),
        maxDate: moment().subtract(1, 'days'),
        ranges: {
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
            'Last 30 Days': [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
            'Last 90 Days': [moment().subtract(90, 'days'), moment().subtract(1, 'days')],
        }
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        // $('#filterForm').submit();
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#filterForm').submit();
    });

    var selectedOption = $('#country').find('option:selected');
    var flagUrl = selectedOption.data('flag');
    $('#country').css('background-image', 'url(' + flagUrl + ')');

    $('#keywordTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50], [10, 25, 50]],
        "order": [],
        "columnDefs": [
            { "orderable": false, "targets": 0 }
        ]
    });
});

function applyFilter(countryId, positionFilter) {
    $('#positionFilter').val(positionFilter);
    $('input[name="country"]').val(countryId);
    $('#filterForm').submit();
}
</script>
@endpush
