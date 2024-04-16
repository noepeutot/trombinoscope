<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
            integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
            crossorigin="anonymous"></script>
</head>
<style>
    * {
        outline: red dashed 1px;
    }

    body {
        background-image: url("assets/images/wave_main.svg");
        background-repeat: no-repeat;
    }

    article:hover {
        transform: scale(0.98);
        transition: transform 200ms ease-in-out;
    }

    a img {
        object-fit: cover;
        object-position: top;
    }

</style>
<body>
<header class="d-flex justify-content-between align-items-center my-5">
    <a type="button" class="btn btn-light d-flex align-items-center mx-5 px-4" href="<?= base_url('/')?>">
        <img class="me-2" src="assets/images/back_arrow.svg" alt="retour" width="20px">
        <span>Retour</span>
    </a>
    <button type="button" class="btn btn-light d-flex align-items-center mx-5 px-4">
        <img class="me-2" src="assets/images/account.svg" alt="compte" width="30px" height="30px">
        <span>Se connecter</span>
    </button>
</header>
<main class="d-flex justify-content-center">
    <img class="border border-5 border-light rounded-3" src="assets/images/pp.jpg" width="200px" alt="photographie">
    <h2><?=$id[0]?></h2>
</main>
</body>
</html>