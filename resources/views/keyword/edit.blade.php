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
                    <form @if (isset($keyword) && $keyword->keyword) action="{{ route('keywords.update', $keyword) }}" @else action="{{ route('keywords.store') }}" @endif method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="keyword" class="form-label">Keyword Name:</label>
                            <input id="keyword" type="text" name="keyword" class="form-control" placeholder="Enter keyword"
                                @if (isset($keyword) && $keyword->keyword) value="{{ $keyword->keyword }}" @endif>
                            @error('keyword')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="labels" class="form-label">Labels:</label>
                            <select id="field2" name="labels[]" class="form-control" multiple>
                                @foreach($labels as $label)
                                    <option value="{{ $label->id }}"
                                        @if(isset($keyword) && $keyword->labels->contains($label->id)) selected @endif>
                                        {{ $label->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('labels')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="users" class="form-label">Assign to Users:</label>
                            <select id="users" name="users[]" class="form-control" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        @if($keyword->assignedUsers->contains($user->id)) selected @endif>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary" >
                                        {{ __('Back') }}
                                </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('assets/js/custom/multiselect-dropdown.js') }}"></script>
@endpush
