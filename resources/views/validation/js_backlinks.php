<script>
$(document).ready(function () {
    var rules = {
        website: {
            required: true
        },
        keyword_id: {
            required: true
        },
        url: {
            required: true,
            url: true
        },
        backlink_source: {
            required: true
        },
        link_type: {
            required: true
        },
        anchor_text: {
            required: true
        },
        domain_authority: {
            required: true
        },
        page_authority: {
            required: true
        },
        contact_person: {
            required: true
        },
        status: {
            required: true
        }
    };

    var messages = {
        website: {
            required: "Please enter a website"
        },
        url: {
            required: "Please enter a URL",
            url: "Please enter a valid URL format"
        },
        keyword_id: {
            required: "Please select keyword"
        },
        backlink_source: {
            required: "Please enter a backlink source"
        },
        link_type: {
            required: "Please select a link type"
        },
        anchor_text: {
            required: "Please enter an anchor text"
        },
        domain_authority: {
            required: "Please enter a domain authority"
        },
        page_authority: {
            required: "Please enter a page authority"
        },
        contact_person: {
            required: "Please enter a contact person"
        },
        status: {
            required: "Please select a status"
        }
    };

    $.validator.addMethod("validURL", function(value, element) {
        var urlRegex = /^(https?:\/\/)?((([a-z\d]([a-z\d-]*[a-z\d])*)\.)+[a-z]{2,}|((\d{1,3}\.){3}\d{1,3}))(:\d+)?(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/i;
        return this.optional(element) || urlRegex.test(value);
    }, "Please enter a valid URL");

    rules.website.validURL = true;
    messages.website.validURL = "Please enter a valid URL format";

    validateForm(rules, messages, 'backlinkForm');
});
</script>