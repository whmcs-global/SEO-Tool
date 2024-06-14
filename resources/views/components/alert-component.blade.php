@if (session()->has('status'))
    <div>
        <div class="alert alert-{{ $status }} alert-dismissible fade show" role="alert">
            {!! $message !!}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
@endif