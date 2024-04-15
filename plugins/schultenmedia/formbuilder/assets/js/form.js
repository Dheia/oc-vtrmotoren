// todo check
/*
 * Reset form after submission
 */
function resetForm($form) {
    $form[0].reset();

    if (window.grecaptcha) {
        grecaptcha.reset();
    }

    $('.upload-files-container').html('');

    $('.responsiv-uploader-fileupload').removeClass('is-populated');
}

/*
 * Implement nice invalid form fields
 */
$(window).on('ajaxInvalidField', function (event, fieldElement, fieldName, errorMsg, isFirst) {
    let $field = $(fieldElement).closest('.form-group');

    let $help = $('<p />').addClass('help-block');

    if (!$field.length) {
        return;
    }

    event.preventDefault();

    if (errorMsg) {
        $help.text(errorMsg.join(', '));
    }

    $help.addClass('form-field-error-label');

    $field.addClass('has-error');

    // Prevent the next error alert only once
    $(window).one('ajaxErrorMessage', function (event, message) {
        event.preventDefault();
    });

    if ($('.form-field-error-label', $field).length > 0) {
        return;
    }

    $field.append($help);

    if (isFirst) {
        fieldElement.focus();
    }
});

$(document).on('ajaxPromise', '[data-request]', function () {
    let $form = $(this).closest('form');

    let $help = $('.form-field-error-label', $form);

    if (!$form.length || !$help.length) {
        return;
    }

    $help.remove();

    $('.form-group.has-error', $form).removeClass('has-error');
});
