@extends('layouts.admin')

@section('title')
Backlinks
@endsection

@section('content')
<section class="section">
<div class="graphs d-flex">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-6">
        <div class="card">
            <div class="card-header d-flex justify-content-center align-items-center">
                <h4>STATUS COUNT</h4>
            </div>
            <div class="card-body">
                <div id="echart_pie" class="chartsh"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-6">
        <div class="authority d-grid">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-center align-items-center">
                        <h4>Total Domain Authority (DA)</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <label><b><h1>{{$domain_authority}}</h1></b></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12 ">
                <div class="card">
                    <div class="card-header d-flex justify-content-center align-items-center">
                        <h4>Total Page Authority (PA)</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <label><b><h1>{{$page_authority}}</h1></b></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-center align-items-center">
            <h4>SELECTED DATE'S AUTHORITY SUMMARY</h4>
        </div>
        <div class="card-body">
            <div class="recent-report__chart">
                <div id="chart1"></div>
            </div>
        </div>
    </div>
</div>

    <x-alert-component/>
    <div class="card">
        <!-- <div class="mb-3">
            <div class="col-auto">
                <a href="{{ route('keywords.refresh') }}" class="btn btn-primary rounded-pill">Refresh Data</a>
            </div>
        </div> -->
        @can('Add backlink')
        <div class="mb-3 row justify-content-end">
            <div class="col-auto">
                <a href="{{ route('backlinks.create') }}" class="btn btn-primary rounded-pill">Add Backlink</a>
            </div>
        </div>
        @endcan
        <div class="card-body">
            @can('Backlink list')
            <div class="table-responsive">
            <table id="backlinksTable" class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Website</th>
                        <th>URL</th>
                        <th>Target Keyword</th>
                        <th>Backlink Source</th>
                        <th>Link Type</th>
                        <th>Status</th>
                        <th>Anchor Text</th>
                        <th>Domain Authority</th>
                        <th>Page Authority</th>
                        <th>Contact Person</th>
                        <th>Notes Comments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                
                    @foreach($backlinks as $backlink)
                    <tr>
                        <td>{{ $backlink->date }}</td>
                        <td>{{ $backlink->website }}</td>
                        <td>{{ $backlink->url }}</td>
                        <td>{{ $backlink->target_keyword }}</td>
                        <td>{{ $backlink->backlink_source}}</td>
                        <td>{{ $backlink->link_type }}</td>
                        <td>{{ $backlink->status}}</td>
                        <td>{{ $backlink->anchor_text }}</td>
                        <td>{{ $backlink->domain_authority }}</td>
                        <td>{{ $backlink->page_authority }}</td>
                        <td>{{ $backlink->contact_person }}</td>
                        <td>{{ $backlink->notes_comments }}</td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                @can('Edit backlink')
                                <a href="{{ route('backlinks.create', $backlink) }}" class="btn btn-secondary rounded-pill mr-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('Delete backlink')
                                <form method="POST" action="{{ route('backlinks.delete', $backlink) }}" class="d-inline mr-2" id="delete-form-{{ $backlink->id }}">
                                    @csrf
                                    @method('delete')
                                    <button type="button" class="btn btn-danger rounded-pill" onclick="confirmDelete({{ $backlink->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @endcan
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
@endpush
@push('scripts')
<script>
    var pie_data = @json($pie_data);
    var pie_names = @json($data_name);
    var graph_Data = @json($values);
  
    console.log(graph_Data);
    </script>
<script src="{{asset('assets/js/apexcharts/apexcharts.min.js')}}"></script>
    <script src="{{ asset('assets/js/chart-apexcharts.js') }}"></script>
    
    <script src="{{asset('assets/js/echart/echarts.js')}}"></script>
    <script src="{{ asset('assets/js/chart-echarts.js') }}"></script>

<script src="{{ asset('assets/js/custom/multiselect-dropdown.js') }}"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#backlinksTable').DataTable({
            responsive: true,
            columnDefs: [
                { targets: -1, orderable: false }
            ]
        });
    });
</script>
@endpush
