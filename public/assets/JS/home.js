const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

let inputCheckbox = $('input:checkbox');
let buttonReset = $('#reset:link');

$(document).ready(function () {
    checkCheckbox();

    buttonReset.click(function () {
        inputCheckbox.prop('checked', false);
        $(this).hide();
    });

    $('.form-check-input').change(checkCheckbox);
})

/**
 * Fonction qui check si au moins un input est checked dans les filtres
 */
function checkCheckbox() {
    if ($('input[type^="checkbox"]:checked').length > 0) {
        buttonReset.fadeIn(200);
    } else {
        buttonReset.fadeOut(200);
    }
}