const searchInput = $("input#search");
const roleSelect = $(".select-role");

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
});

$(document).on('keydown', function (e) {
    // Check si c’est la touche de suppression qui est appuyée.
    // Si oui, on supprime le dernier caractère de la barre de recherche.
    if (e.which === 8) {
        e.preventDefault();
        let d = searchInput.val();
        searchInput.val(d.slice(0, -1))
    }
});

$(document).ready(function () {
    roleSelect.each(function (index, obj) {
        $(this).change(function () {
            let value = $(this).val();
            let id = "#role-" + value.split(" ")[1]
            const roleForm = $(id);
            roleForm.submit();
        });
    });
});

