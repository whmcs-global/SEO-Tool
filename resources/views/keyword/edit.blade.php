@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="shadow-lg card bg-light rounded-3">
        <div class="text-white card-header bg-primary">
            <h3 class="mb-0">Keyword Management</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form @if (isset($keyword) && $keyword->keyword) action="{{ route('keywords.update', $keyword) }}"
                        @else action="{{ route('keywords.store') }}" @endif method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="keyword" class="form-label">Keyword Name:</label>
                            <input id="keyword" type="text" name="keyword" class="form-control" placeholder="Enter keyword"
                                @if (isset($keyword) && $keyword->keyword) value="{{ $keyword->keyword }}" @endif>
                            @error('keyword')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="text-white card-footer">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection
