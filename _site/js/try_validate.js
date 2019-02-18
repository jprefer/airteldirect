$(document).ready(function() {
    $('#register').submit(function() {
        var $fields = $(this).find('input[name="selection"]:checked');
        if (!$fields.length) {
            alert('You must check at least one option!');
            return false; // The form will *not* submit
        }
    });
    $('#register').validate({// initialize the plugin
        rules: {
            selection: {
                required: true
            },
            email: {
                email: true,
                required: function() {
                    return (document.getElementById("1").checked) ? true : false;
                }
            },
            mob_phone: {
                phoneUS: true,
                required: function() {
                    return (document.getElementById("2").checked) ? true : false;
                }
            },
            land_phone: {
                phoneUS: true,
                required: function() {
                    return (document.getElementById("3").checked) ? true : false;
                }
            },
            email2: {
                email: true,
                required: function() {
                    return (document.getElementById("3").checked) ? true : false;
                }
            }
        },
        messages: {
            selection: {
                required: ""
            }
        },
        errorElement: "span",
        errorPlacement: function(error, element) {
            element.siblings("label").append(error);
        },
        highlight: function(element) {
            $(element).siblings("label").addClass("error");
        },
        unhighlight: function(element) {
            $(element).siblings("label").removeClass("error");
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});