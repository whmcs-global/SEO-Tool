jQuery(document).ready(function () {
    jQuery.validator.addMethod("regex", function(value, element, regexp) {
        var re = new RegExp(regexp);
        return this.optional(element) || re.test(value);
    }, "Please check your input.");

    $("form#userDetail").validate({
        errorClass: "error-messages",
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 50,
                regex: /^[a-zA-Z ]+$/
            },
            email: {
                required: true,
                email: true,
                regex: /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,10})+$/
            },
            phone_number: {
                required: true,
                regex: /^[\d\(\)\-\s]+$/
            }
        },
        messages: {
            name: {
                required: "Please enter your name",
                minlength: "Name must be 3-50 characters long",
                maxlength: "Name must be 3-50 characters long",
                regex: "Name can contain alphabets and spaces only"
            },
            email: {
                required: "Please enter the email",
                email: "Invalid email address",
                regex: "Invalid email address"
            },
            phone_number: {
                required: "Please enter the phone number",
                regex: "Please enter a valid phone number"
            }
        },
        errorPlacement: function (label, element) {
            if (element.is("textarea")) {
                label.insertAfter(element.next());
            } else if (element.attr("type") == "file") {
                label.insertAfter(jQuery(element).parent());
            } else {
                label.insertAfter(element);
            }
        }
    });

    // Change Password Form Validation
    $("form#changePassword").validate({
        errorClass: "error-messages",
        rules: {
            current_password: {
                required: true
            },
            password: {
                required: true,
                minlength: 8,
                maxlength: 32,
                regex: /^[a-zA-Z0-9!@#$%^&*]+$/
            },
            password_confirmation: {
                required: true,
                equalTo: "#newPassword"
            }
        },
        messages: {
            current_password: {
                required: "Please enter your current password"
            },
            password: {
                required: "Please enter the new password",
                minlength: "Password must be 8-32 characters long",
                maxlength: "Password must be 8-32 characters long",
                regex: "Password can contain [a-zA-Z0-9!@#$%^&*] characters"
            },
            password_confirmation: {
                required: "Please enter the confirm password",
                equalTo: "Confirm password does not match with the new password"
            }
        },
        errorPlacement: function (label, element) {
            label.insertAfter(element.parent().after());
        }
    });
});
