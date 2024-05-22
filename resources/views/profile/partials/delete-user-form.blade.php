<section class="space-y-6">
  <header>
    <h2 class="text-lg font-medium text-dark">{{ __('Delete Account') }}</h2>
    <p class="mt-1 text-sm text-secondary text-danger">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
  </header>
  <form id="deleteAccountForm" method="post" action="{{ route('profile.destroy') }}" class="space-y-6">
    @csrf
    @method('delete')
    <button type="button" class="btn btn-danger" id="delete">{{ __('Delete Account') }}</button>
  </form>
  <script>
  $("#delete").click(function() {
    swal({
      title: 'Are you sure?',
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
        $("#deleteAccountForm").submit();
      }
    });
  });
</script>
</section>