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
    </style>

    <div class="container py-5">
      <div class="p-6 mx-auto bg-blue-200 max-w-7xl rounded-3xl">
        <div class="mb-4 row">
          <div class="col-auto">
            <h3 class="font-semibold">Add Keywords</h3>
          </div>
          <div class="col-auto ml-auto">
            <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill">Home</a>
          </div>
        </div>

        <form id="keyword-form" action="{{ route('keywords.store') }}" method="post">
          @csrf
          <div class="form-group">
            <label for="keyword-textarea" class="font-weight-bold">Enter Keywords (press Enter after each keyword)</label>
            <textarea id="keyword-textarea" name="keyword-textarea" class="form-control rounded-pill" rows="3"></textarea>
          </div>
          <div id="keyword-preview" class="mt-4"></div>
          <button type="submit" class="mt-3 btn btn-primary rounded-pill">Save Keywords</button>
          <span class="text-sm text-danger"></span>
        </form>
      </div>
    </div>

    <script>
      $(document).ready(function() {
        var keywords = [];

        function updatePreview() {
          $('#keyword-preview').html('');
          for (var i = 0; i < keywords.length; i++) {
            $('#keyword-preview').append('<span class="keyword-preview">' + keywords[i] + '<span class="delete" data-index="' + i + '">&#10005;</span></span>');
          }
        }

        $('#keyword-textarea').on('keydown', function(e) {
          if (e.key === 'Enter') {
            var keyword = $(this).val().trim();
            if (keyword !== '') {
              keywords.push(keyword);
              $(this).val('');
              updatePreview();
            }
            e.preventDefault();
          }
        });

        $(document).on('click', '.keyword-preview .delete', function() {
          var index = $(this).data('index');
          keywords.splice(index, 1);
          updatePreview();
        });

        $('#keyword-form').submit(function(e) {
          e.preventDefault();
          var keywordTextarea = $('#keyword-textarea').val().trim();
          if (keywordTextarea !== '') {
            keywords = keywords.concat(keywordTextarea.split('\n'));
          }
          var csrfToken = $('meta[name="csrf-token"]').attr('content');
          var formData = { keywords: keywords, _token: csrfToken };
          $.ajax({
            url: "{{ route('keywords.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
              window.location.href = "{{ route('dashboard') }}";
            },
            error: function(xhr, status, error) {
              if (xhr.status === 400) {
                var errorData = xhr.responseJSON.error;
                var errorMessages = '';
                for (var field in errorData) {
                  errorMessages += errorData[field].join('<br>');
                }
                $('#keyword-form span.text-danger').html(errorMessages);
              } else {
                console.error(error);
              }
            }
          });
        });
      });
    </script>
@endsection
