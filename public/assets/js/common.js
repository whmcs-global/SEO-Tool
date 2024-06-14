function validateForm(rules, messages, formId) {
    $('#' + formId).validate({
        rules: rules,
        messages: messages
    });
}


function confirmDelete(id) {
    swal({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}