@extends('layouts.admin')

@section('content')
<style>
  .keyword-preview {
    display: inline-block;
    background-color: #f0f0f0;
    padding: 5px 10px;
    border-radius: 20px;
    margin-right: 10px;
    margin-bottom: 10px;
  }
  .keyword-preview .delete {
    color: #ff0000;
    cursor: pointer;
    margin-left: 5px;
  }

  /* Multi-select dropdown styles */
  select {
    width: 100%;
    max-width: 100%;
  }

  .multiselect-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
  }
  .multiselect-dropdown .dropdown-toggle {
    background-color: transparent;
    border: none;
    outline: none;
    box-shadow: none;
  }
  .multiselect-dropdown .dropdown-toggle:after {
    content: "";
    display: inline-block;
    width: 0;
    height: 0;
    margin-left: 0.255em;
    vertical-align: 0.255em;
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-bottom: 0;
    border-left: 0.3em solid transparent;
  }
  .multiselect-dropdown .dropdown-menu {
    max-height: 200px;
    overflow-y: auto;
  }
  .multiselect-dropdown .dropdown-item {
    padding: 0.25rem 1.5rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
  }
  .multiselect-dropdown .dropdown-item:hover,
  .multiselect-dropdown .dropdown-item:focus {
    background-color: #f8f9fa;
  }
  .multiselect-dropdown .form-control {
    border: none;
    box-shadow: none;
  }

  .form-group {
    margin-bottom: 1.5rem;
  }

  .form-control {
    width: 100%;
    box-sizing: border-box;
  }

  /* Modal styles */
  .modal {
    display: none;
    position: fixed;
    z-index: 1;
    padding-top: 100px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
  }

  .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 10px;
  }

  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    margin-left: 430px;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }
</style>

<div class="container py-5">
    @if (session('message'))
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <span class="font-weight-bold">Error:</span> {{ session('message') }}
            </div>
        @else
            <div class="alert alert-success" role="alert">
                <span class="font-weight-bold">Success:</span> {{ session('message') }}
            </div>
        @endif
    @endif

    <div class="p-6 mx-auto bg-blue-200 max-w-7xl rounded-3xl">
        <div class="mb-4 row">
            <div class="col-auto">
                <h3 class="font-semibold">Add Keywords</h3>
            </div>
        </div>

        <form id="keyword-form" action="{{ route('keywords.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="keyword-textarea" class="font-weight-bold">Enter Keywords (comma-separated)</label>
                <input id="keyword-textarea" name="keywords" class="form-control" rows="1"></input>
            </div>
            <div class="form-group">
                <label for="field2" class="font-weight-bold">Select Label</label>
                <div class="select-container">
                    <select name="label[]" id="field2" multiple multiselect-search="true" multiselect-max-items="3">
                        @foreach($labels as $label)
                            <option value="{{ $label->id }}">{{ $label->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="create-label-btn" class="btn btn-secondary">Create Label</button>
            </div>
            @role('Admin|Super Admin')
            <div class="form-group">
                <label for="user-select" class="font-weight-bold">Assign to Users</label>
                <select name="users[]" id="user-select" multiple>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            @endrole
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save Keywords</button>
                <a href="{{ url()->previous() }}" class="btn btn-black">Back</a>
            </div>
            <span class="text-sm text-danger"></span>
        </form>
    </div>
</div>


<!-- Modal -->
<div id="createLabelModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h4 class="font-semibold">Create New Label</h4>
    <form id="create-label-form">
      @csrf
      <div class="form-group">
        <label for="label-name" class="font-weight-bold">Label Name</label>
        <input type="text" id="label-name" name="label-name" class="form-control rounded-pill">
      </div>
      <div class="form-group">
        <span id="label-error" class="text-sm text-danger"></span>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary rounded-pill">Save Label</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/custom/multiselect-dropdown.js') }}"></script>
<script>
  $(document).ready(function() {

    var modal = document.getElementById("createLabelModal");
    var btn = document.getElementById("create-label-btn");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
      modal.style.display = "block";
    }

    span.onclick = function() {
      modal.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }

    $('#create-label-form').submit(function(e) {
      e.preventDefault();
      var labelName = $('#label-name').val().trim();
      if (labelName !== '') {
        $.ajax({
          url: "{{ route('labels.store') }}",
          type: "POST",
          data: {
            name: labelName,
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            $('#field2').append('<option value='+response.label.id+'>' + response.label.name + '</option>');
            document.querySelector('#field2').loadOptions();
            modal.style.display = "none";
            $('#label-name').val('');
            $('#label-error').text('');
          },
          error: function(xhr, status, error) {
            if (xhr.status === 422) {
              var errorData = xhr.responseJSON.errors;
              if (errorData.name) {
                $('#label-error').text(errorData.name[0]);
              }
            } else {
              console.error(error);
            }
          }
        });
      }
    });
  });
</script>
@endpush
