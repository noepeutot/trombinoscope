$(document).ready(function () {
    let showImg = $('#show');
    let hideImg = $('#hide');
    let inputPW = $('#input-pw');
    let divPW = $('#display-pw');

    inputPW.keyup(function () {
        if (inputPW.val()) {
            divPW.show();
        } else {
            divPW.hide();
        }
    })

    divPW.click(function () {
        if (showImg.is(":visible")) {
            showImg.hide();
            hideImg.show();
            inputPW.attr('type', 'password');
        } else if (hideImg.is(":visible")) {
            hideImg.hide();
            showImg.show();
            inputPW.attr('type', 'text');
        }
    });
})