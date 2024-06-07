let showImg = $('#show');
let hideImg = $('#hide');
let inputPW = $('#input-pw');
let divPW = $('#display-pw');

$(document).ready(function () {
    // Si click, faire changer l’image de protection de mot de passe
    // en changeant aussi l’attribut de l’input du mot de passe.
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