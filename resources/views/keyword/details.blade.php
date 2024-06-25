@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="mb-4 d-flex justify-content">
        <div class="form-row align-items-center">
            <div class="col">
                <select name="country" id="country" class="form-control">
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" {{ $country->id == $selectedCountry ? 'selected' : '' }}>
                            <img src="https://flagcdn.com/32x24/{{ strtolower($country->code) }}.png" 
                                 alt="{{ $country->name }} flag" 
                                 class="me-1">
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="me-2 mb-2 mb-md-0">
            <form action="{{ route('keywords.details') }}" method="GET">
                <input type="text" readonly name="daterange" class="form-control" id="dateRangeInput" placeholder="Select Date Range" value="{{ $startDate && $endDate ? $startDate . ' - ' . $endDate : '' }}" />
            </form>
        </div>
    </div>

    <h5>Position Filters for All Countries:</h5>
    <div class="overflow-auto">
        @foreach ($countries as $country)
            <h6>
                <img src="https://flagcdn.com/32x24/{{ strtolower($country->code) }}.png" 
                     alt="{{ $country->name }} flag" 
                     class="me-1">
                {{ $country->name }}
            </h6>
            <div class="d-flex gap-2 mb-3">
                <button class="position-filter-btn btn btn-light border flex-shrink-0">
                    <span class="small">ALL</span>
                    <strong>{{ $countryRanges[$country->id]['top_100']['count'] }}</strong>
                </button>
                @foreach (['top_1', 'top_3', 'top_5', 'top_10', 'top_30', 'top_100'] as $range)
                    <button class="position-filter-btn btn btn-light border flex-shrink-0">
                        <span class="small">{{ strtoupper(str_replace('_', ' ', $range)) }}</span>
                        <strong>{{ $countryRanges[$country->id][$range]['count'] }}</strong>
                        <small class="text-muted">{{ number_format($countryRanges[$country->id][$range]['percentage'], 2) }}%</small>
                    </button>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Keyword Table -->
    <h5>
        <img src="https://flagcdn.com/32x24/{{ strtolower($countries->firstWhere('id', $selectedCountry)->code) }}.png" 
             alt="{{ $countries->firstWhere('id', $selectedCountry)->name }} flag" 
             class="me-1">
        Keyword Data for {{ $countries->firstWhere('id', $selectedCountry)->name }}:
    </h5>
    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered">
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
                    @foreach ($allDates as $date)
                        <td>{{ round(floatval($data['positions'][$date] ?? '-')) }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    .sticky-col {
        position: sticky;
        left: 0;
        background-color: #fff;
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
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        }
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        $(this).closest('form').submit();
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $(this).closest('form').submit();
    });

    $('#country').change(function() {
        var countryId = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var flagUrl = selectedOption.find('img').attr('src');
        $(this).css('background-image', 'url(' + flagUrl + ')');
        $.ajax({
            url: '{{ route('countries.set') }}',
            type: 'GET',
            data: { country_id: countryId },
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
            }
        });
    });

    var selectedOption = $('#country').find('option:selected');
    var flagUrl = selectedOption.find('img').attr('src');
    $('#country').css('background-image', 'url(' + flagUrl + ')');
});
</script>
@endpush