<script>
$(document).ready(function () {
    var rules = {
        date: {
                        required: true,
                        date: true
                    },
                    website: {
                        required: true,
                        url: true
                    },
                    target_keyword: {
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
                    notes_comments: {
                        required: true
                        // Optional field, no validation needed
                    },
                    status: {
                        required: true
                    }
                };

    var messages = {
        date: {
                        required: "Please enter a date",
                        date: "Please enter a valid date format"
                    },
                    website: {
                        required: "Please enter a website",
                        url: "Please enter a valid URL format for the website"
                    },
                    url: {
                        required: "Please enter a URL",
                        url: "Please enter a valid URL format"
                    },
                    target_keyword: {
                        required: "Please enter a target keyword"
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

    validateForm(rules, messages, 'backlinkForm');
});
</script>