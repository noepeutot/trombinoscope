<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="<?= css_url('profile') ?>" rel='stylesheet'>
    <link href="<?= css_url('bootstrap.min.v5.3.3') ?>" rel="stylesheet">
    <script src="<?= js_url('popper.v2.11.8') ?>"></script>
    <script src="<?= js_url('bootstrap.v5.3.3') ?>"></script>
    <script src="<?= js_url('jquery.v3.7.1') ?>"></script>

    <link href="<?= css_url('bootstrap-select.min.v1.13.18') ?>" rel="stylesheet">
    <script src="<?= js_url('bootstrap-select.v1.14.0') ?>"></script>
</head>
<style>
    /** {*/
    /*    outline: red dashed 1px;*/
    /*}*/

    body {
        background: #88B6F2 url("<?= img_url('wave_edit.svg') ?>") no-repeat fixed center center;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
</style>
<body>
<?= $this->include('header') ?>
<main class="d-flex justify-content-center">
    <form class="row g-3 w-50">
        <div class="col-md-6 d-flex justify-content-start align-self-center">
            <h2 class="text-light">
                Edition du profile
            </h2>
        </div>
        <div class="col-md-6 d-flex justify-content-start align-self-center">
            <a class="col-md-6 link-offset-2 text-light" href="<?= base_url('profile/' . $personne->id_personne) ?>">
                Prévisualisation
                <img src="<?= img_url('arrow_link.svg') ?>" alt="redirection">
            </a>
        </div>
        <div class="col-md-6">
            <label for="inputNom" class="form-label text-light">Nom</label>
            <input type="text" class="form-control" id="inputNom" placeholder="Nom" name="nom"
                   value="<?= (isset($personne->nom)) ? $personne->nom : '' ?>">
        </div>
        <div class="col-md-6">
            <label for="inputPrenom" class="form-label text-light">Prénom</label>
            <input type="text" class="form-control" id="inputPrenom" placeholder="Prénom" name="prenom"
                   value="<?= (isset($personne->prenom)) ? $personne->prenom : '' ?>">
        </div>
        <div class="col-12">
            <label for="inputEmail" class="form-label text-light">Email</label>
            <input type="email" class="form-control" id="inputEmail" placeholder="prenom.nom@g2elab.grenoble-inp.fr"
                   name="email" value="<?= (isset($mailPersonne->libelle)) ? $mailPersonne->libelle : '' ?>">
        </div>
        <div class="col-md-6">
            <label for="inputTel" class="form-label text-light">Téléphone</label>
            <input type="tel" class="form-control" id="inputTel" placeholder="0123456789" name="telephone"
                   pattern="[0-9]{10}" value="<?= (isset($personne->telephone)) ? $personne->telephone : '' ?>">
        </div>
        <div class="col-md-6">
            <label for="inputBureau" class="form-label text-light">Bureau(x)</label>
            <select class="selectpicker w-100" id="inputBureau" data-live-search="true"
                    data-style="btn-light" title="Sélectionner…">
                <?php if (!empty($allBureaux)) {
                    foreach ($allBureaux as $bureau) { ?>
                        <option <?php if (!empty($bureauPersonne)) {
                            echo $bureau->id_bureau === $bureauPersonne->id_bureau ? 'selected' : '';
                        } ?>>
                            <?= $bureau->numero ?>
                        </option>
                    <?php }
                } ?>
            </select>
        </div>
        <div class=" col-md-6">
            <label for="inputCategorie" class="form-label text-light">Catégorie</label>
            <select class="selectpicker w-100" id="inputCategorie" data-live-search="true"
                    data-style="btn-light" title="Sélectionner…">
                <?php if (!empty($allStatuts)) {
                    foreach ($allStatuts as $statut) { ?>
                        <option <?php if (!empty($statutPersonne)) {
                            echo $statut->id_statut === $statutPersonne->id_statut ? 'selected' : '';
                        } ?>>
                            <?= $statut->nom ?>
                        </option>
                    <?php }
                } ?>
            </select>
        </div>
        <div class=" col-md-6">
            <label for="inputEquipe" class="form-label text-light">Equipe(s) de rattachement</label>
            <select class="selectpicker w-100" id="inputEquipe" data-live-search="true"
                    data-style="btn-light" title="Sélectionner…" multiple>
                <?php if (!empty($allEquipes)) {
                    foreach ($allEquipes as $equipe) { ?>
                        <option <?php if (isset($equipePersonne)) {
                            echo in_array($equipe->id_equipe,
                                array_column($equipePersonne, 'id_equipe')) ? 'selected' : '';
                        } ?>>
                            <?= $equipe->nom_court ?>
                        </option>
                    <?php }
                } ?>
            </select>
        </div>
        <div class=" col-md-6">
            <label for="inputEmployeur" class="form-label text-light w-100">Employeur(s)</label>
            <select class="selectpicker w-100" id="inputEmployeur" data-live-search="true"
                    data-style="btn-light" title="Sélectionner…" multiple>
                <?php if (!empty($allEmployeurs)) {
                    foreach ($allEmployeurs as $employeur) { ?>
                        <option <?php if (isset($employeursPersonne)) {
                            echo in_array($employeur->id_employeur,
                                array_column($employeursPersonne, 'id_employeur')) ? 'selected' : '';
                        } ?>>
                            <?= $employeur->nom ?>
                        </option>
                    <?php }
                } ?>
            </select>
        </div>
        <?php if (!empty($responsablesPersonne)) { ?>
            <div class="col-md-6">
                <label for="inputResponsable" class="form-label text-light w-100">Responsable(s)</label>
                <select class="selectpicker w-100" id="inputResponsable" data-live-search="true"
                        data-style="btn-light" title="Sélectionner ..." multiple>
                    <?php if (!empty($responsablesPersonne)) {
                        foreach ($responsablesPersonne as $responsable) { ?>
                            <option selected>
                                <?= $responsable->nom . ' ' . $responsable->prenom ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>
        <?php } ?>
        <?php if (!empty($encadresPersonne)) { ?>
            <div class="col-md-6">
                <label for="inputEncadrement" class="form-label text-light w-100">Co-Encadrement de recherche</label>
                <select class="selectpicker w-100" id="inputEncadrement" data-live-search="true"
                        data-style="btn-light" title="Sélectionner…" multiple>
                    <?php if (!empty($encadresPersonne)) {
                        foreach ($encadresPersonne as $encadre) { ?>
                            <option selected>
                                <?= $encadre->nom . ' ' . $encadre->prenom ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>
        <?php } ?>
        <?php if (isset($sejour->sujet)) { ?>
            <div class="col-12">
                <label for="inputActivite" class="form-label text-light w-100">Activités</label>
                <textarea class="form-control" id="inputActivite"
                          placeholder="Vos activités, sujet de stage, sujet de thèse…" rows="2"
                          name="activite"><?= $sejour->sujet ?></textarea>
            </div>
        <?php } ?>
        <div class="col-12">
            <label for="inputCommentaire" class="form-label text-light w-100">Commentaire</label>
            <input type="text" class="form-control" id="inputCommentaire" placeholder="Un commentaire ..."
                   name="commentaire">
        </div>
        <div class="col-6 d-flex justify-content-center">
            <a class="btn btn-danger link-underline link-underline-opacity-0 w-50">Annuler</a>
        </div>
        <div class="col-6 d-flex justify-content-center">
            <a class="btn btn-success link-underline link-underline-opacity-0 w-50">Sauvegarder</a>
        </div>
    </form>
</main>
</body>
<script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker('toggle');
    });
</script>
</html>