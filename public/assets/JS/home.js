const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

const inputCheckbox = $('input:checkbox');
const buttonReset = $('#reset:link');
const form = $('form#search');
const searchInput = $("input#search");

$(document).ready(function () {
    // Vérification pour faire apparaitre ou non le bouton reset au lancement de la page.
    checkCheckbox();

    // Si le bouton de reset est appuyé, on désactive toutes les checkbox des filtres et on cache le bouton reset.
    buttonReset.click(function () {
        inputCheckbox.prop('checked', false);
        $(this).hide();
    });

    // Si changement dans les filtres, on refait appel à la fonction qui fait apparaitre ou non le bouton reset.
    $('.form-check-input').change(checkCheckbox);
})

$(document).on('keypress', function (e) {
    // On récupère la longueur de la valeur de la barre de recherche, pour faire passer le curseur à la fin.
    let length = searchInput.val().length;
    searchInput.focus();
    searchInput[0].setSelectionRange(length, length)

    // Si la touche "entrée" est entrée, on lance la recherche avec le formulaire.
    if (e.which === 13) {
        form.submit();
        return false;
    }
})

$(document).on('keydown', function (e) {
    // Check si c’est la touche de suppression qui est appuyée.
    // Si oui, on supprime le dernier caractère de la barre de recherche.
    if (e.which === 8) {
        e.preventDefault();
        let d = searchInput.val();
        searchInput.val(d.slice(0, -1))
    }
})

/**
 * Fonction qui check si au moins un input est checked dans les filtres.
 * Si oui, on fait apparaître le bouton de reset avec un fade.
 */
function checkCheckbox() {
    if ($('input[type^="checkbox"]:checked').length > 0) {
        buttonReset.fadeIn(200);
    } else {
        buttonReset.fadeOut(200);
    }
}