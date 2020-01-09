jQuery(document).ready(function ($) {
    //Custom From submistion
    $("#leadgen_form").submit(function (e) {
        e.preventDefault();
    }).validate({
        rules: {
            phone: {
                minlength: 9,
                maxlength: 10,
                number: true
            },
        },
        submitHandler: function (form) {
            var ajax_url = FrontAjax.ajaxurl;
            var formdata = new FormData(document.getElementById('leadgen_form'));
            formdata.append('action', 'form_submit_with_ajax');

            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: formdata,
                contentType: false,
                processData: false,
                success: function (response) {
                    $('#leadgen_form .successmessage').css('display', 'block').fadeIn('slow');
                    $('#leadgen_form .successmessage').delay(4000).fadeOut('slow');
                    $(document.getElementById('leadgen_form')).trigger("reset");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#leadgen_form .errormessage').css('display', 'block').fadeIn('slow');
                    $('#leadgen_form .errormessage').delay(4000).fadeOut('slow');
                    $(document.getElementById('leadgen_form')).trigger("reset");
                    console.log(jqXHR, textStatus, errorThrown);
                    console.warn(jqXHR.responseText);
                }
            });
        }
    });
});