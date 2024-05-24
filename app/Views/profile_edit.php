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
    body {
        background: #88B6F2 url("<?= img_url('wave_edit.svg') ?>") no-repeat fixed center center;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
</style>
<body>
<?php if (isset($errors)):
    foreach ($errors as $error): ?>
        <li><?= esc($error) ?></li>
    <?php endforeach;
endif; ?>
<?= $this->include('header') ?>
<?php $base_url = base_url('profile');
if (isset($personne)) { ?>
    <main class="d-flex justify-content-center">
        <form class="row g-3 w-50" id="informations" method="post" action="<?= $base_url . '/edit' ?>"
              enctype="multipart/form-data" size="30">
            <div class="col-md-6 d-flex justify-content-start align-self-center">
                <h2 class="text-light">
                    Edition du profile
                </h2>
            </div>
            <div class="col-md-6 d-flex justify-content-start align-self-center">
                <a class="col-md-6 link-offset-2 text-light"
                   href="<?= $base_url . '/' . $personne->id_personne ?>">
                    Prévisualisation
                    <img src="<?= img_url('arrow_link.svg') ?>" alt="redirection">
                </a>
            </div>
            <div class="col-md-6">
                <label for="inputNom" class="form-label text-light">Nom
                    <?php if (isset($nomModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                        <img src="<?= img_url('waiting.svg') ?>" alt="en attente" width="10">
                    </span>
                    <?php } ?>
                </label>
                <input type="text" class="form-control" id="inputNom" placeholder="Nom" name="nom"
                       value="<?= $nomModif->apres ?? $personne->nom ?>">
            </div>
            <div class="col-md-6">
                <label for="inputPrenom" class="form-label text-light">Prénom
                    <?php if (isset($prenomModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                        <img src="<?= img_url('waiting.svg') ?>" alt="en attente" width="10">
                    </span>
                    <?php } ?>
                </label>
                <input type="text" class="form-control" id="inputPrenom" placeholder="Prénom" name="prenom"
                       value="<?= $prenomModif->apres ?? $personne->prenom ?>">
            </div>
            <div class="col-12">
                <label for="inputEmail" class="form-label text-light">Email
                    <?php if (isset($mailModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                        <img src="<?= img_url('waiting.svg') ?>" alt="en attente" width="10">
                    </span>
                    <?php } ?>
                </label>
                <input type="email" class="form-control" id="inputEmail" placeholder="prenom.nom@g2elab.grenoble-inp.fr"
                       name="email"
                       value="<?= $mailModif->apres ?? ($mailPersonne->libelle ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="inputTel" class="form-label text-light">Téléphone
                    <?php if (isset($telephoneModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                        <img src="<?= img_url('waiting.svg') ?>" alt="en attente" width="10">
                    </span>
                    <?php } ?>
                </label>
                <input type="tel" class="form-control" id="inputTel" placeholder="0123456789" name="telephone"
                       pattern="[0-9]{10}" value="<?= $telephoneModif->apres ?? ($personne->telephone ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="inputBureau" class="form-label text-light">Bureau
                    <?php if (isset($bureauModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                        <img src="<?= img_url('waiting.svg') ?>" alt="en attente" width="10">
                    </span>
                    <?php } ?>
                </label>
                <select class="selectpicker w-100" id="inputBureau" data-live-search="true"
                        data-style="btn-light" title="Sélectionner…" name="bureau" form="informations">
                    <?php if (!empty($allBureaux)) {
                        foreach ($allBureaux as $bureau) { ?>
                            <option <?php if (isset($bureauModif)) {
                                echo $bureauModif === $bureau->id_bureau ? 'selected' : '';
                            } else if (!empty($bureauPersonne)) {
                                echo $bureau->id_bureau === $bureauPersonne->id_bureau ? 'selected' : '';
                            } ?> value="<?= $bureau->id_bureau ?>">
                                <?= $bureau->numero ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="inputCategorie" class="form-label text-light">Catégorie
                    <?php if (isset($statutModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                        <img src="<?= img_url('waiting.svg') ?>" alt="en attente" width="10">
                    </span>
                    <?php } ?>
                </label>
                <select class="selectpicker w-100" id="inputCategorie" data-live-search="true"
                        data-style="btn-light" title="Sélectionner…" name="statut" form="informations">
                    <?php if (!empty($allStatuts)) {
                        foreach ($allStatuts as $statut) { ?>
                            <option <?php if (isset($statutModif->apres)) {
                                echo $statutModif->apres === $statut->id_statut ? 'selected' : '';
                            } else if (!empty($statutPersonne)) {
                                echo $statut->id_statut === $statutPersonne->id_statut ? 'selected' : '';
                            } ?> value="<?= $statut->id_statut ?>">
                                <?= $statut->nom ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="inputEquipe" class="form-label text-light">Equipe⋅s de rattachement
                    <?php if (isset($equipesModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                        <img src="<?= img_url('waiting.svg') ?>" alt="en attente" width="10">
                    </span>
                    <?php } ?>
                </label>
                <select class="selectpicker w-100" id="inputEquipe" data-live-search="true"
                        data-style="btn-light" title="Sélectionner…" name="equipe[]" form="informations" multiple>
                    <?php if (!empty($allEquipes)) {
                        foreach ($allEquipes as $equipe) { ?>
                            <option <?php if (isset($equipesModif)) {
                                echo in_array($equipe->id_equipe, $equipesModif) ? 'selected' : '';
                            } else if (isset($equipePersonne)) {
                                echo in_array($equipe->id_equipe,
                                    array_column($equipePersonne, 'id_equipe')) ? 'selected' : '';
                            } ?> value="<?= $equipe->id_equipe ?>">
                                <?= $equipe->nom_court ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="inputEmployeur" class="form-label text-light w-100">Employeur⋅s
                    <?php if (isset($employeursModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                        <img src="<?= img_url('waiting.svg') ?>" alt="en attente" width="10">
                    </span>
                    <?php } ?>
                </label>
                <select class="selectpicker w-100" id="inputEmployeur" data-live-search="true"
                        data-style="btn-light" title="Sélectionner…" name="employeur[]" form="informations" multiple>
                    <?php if (!empty($allEmployeurs)) {
                        foreach ($allEmployeurs as $employeur) { ?>
                            <option <?php if (isset($employeursModif)) {
                                $on_array = in_array($employeur->id_employeur, $employeursModif);
                                echo $on_array ? 'selected' : ''; ?>
                            <?php } else if (isset($employeursPersonne)) {
                                echo in_array($employeur->id_employeur,
                                    array_column($employeursPersonne, 'id_employeur')) ? 'selected' : '';
                            } ?> value="<?= $employeur->id_employeur ?>">
                                <?= $employeur->nom ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="photo_profile" class="form-label text-light w-100">Photo de profile</label>
                <input class="form-control" type="file" id="photo_profile" name="photo_profile" form="informations"
                       accept=".png, .jpg, .jpeg">
            </div>
            <!--            --><?php //if (!empty($responsablesPersonne)) { ?>
            <!--                <div class="col-md-6">-->
            <!--                    <label for="inputResponsable" class="form-label text-light w-100">Responsable⋅s</label>-->
            <!--                    <select class="selectpicker w-100" id="inputResponsable" data-live-search="true"-->
            <!--                            data-style="btn-light" title="Sélectionner ..." name="responsable[]" form="informations"-->
            <!--                            multiple>-->
            <!--                        --><?php //foreach ($responsablesPersonne as $responsable) { ?>
            <!--                            <option selected>-->
            <!--                                --><?php //= $responsable->nom . ' ' . $responsable->prenom ?>
            <!--                            </option>-->
            <!--                        --><?php //} ?>
            <!--                    </select>-->
            <!--                </div>-->
            <!--            --><?php //} ?>
            <?php if (!empty($encadresPersonne)) { ?>
                <div class="col-md-6">
                    <label for="inputEncadrement" class="form-label text-light w-100">Co-Encadrement de
                        recherche</label>
                    <select class="selectpicker w-100" id="inputEncadrement" data-live-search="true"
                            data-style="btn-light" title="Sélectionner…" form="informations" name="encadre[]" multiple>
                        <?php foreach ($encadresPersonne as $encadre) { ?>
                            <option selected>
                                <?= $encadre->nom . ' ' . $encadre->prenom ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>
            <?php if (isset($sejourPersonne) && isset($statutPersonne) && ($statutPersonne->nom === "Stagiaire" || $statutPersonne->nom === "Doctorant")) { ?>
                <div class="col-12">
                    <label for="inputActivite" class="form-label text-light w-100">Activités
                        <?php if (isset($activiteModif)) { ?>
                            <span class="badge rounded-pill text-bg-warning">En attente
                        <img src="<?= img_url('waiting.svg') ?>" alt="en attente" width="10">
                    </span>
                        <?php } ?></label>
                    <textarea class="form-control" id="inputActivite"
                              placeholder="Vos activités, sujet de stage, sujet de thèse…" rows="2"
                              form="informations"
                              name="activite"><?= $activiteModif->apres ?? $sejourPersonne->sujet ?></textarea>
                </div>
            <?php } ?>
            <div class="col-12">
                <label for="inputCommentaire" class="form-label text-light w-100">Commentaire</label>
                <input type="text" class="form-control" id="inputCommentaire"
                       placeholder="Commentaire sur le⋅s changement⋅s"
                       name="commentaire">
            </div>
            <div class="col-6 d-flex justify-content-center">
                <a href="<?= $base_url . 'edit' ?>"
                   class="btn btn-danger link-underline link-underline-opacity-0 w-50">Annuler</a>
            </div>
            <div class="col-6 d-flex justify-content-center">
                <button type="submit" class="btn btn-success link-underline link-underline-opacity-0 w-50">Sauvegarder
                </button>
            </div>
        </form>
    </main>
<?php } ?>
</body>
<script href="<?= js_url('profile_edit') ?>"></script>
</html>