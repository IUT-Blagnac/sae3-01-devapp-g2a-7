<?php

    include("../include/checkConnexion.php");
    include("../include/infoPopup.php");

    verifier_page();
    error_reporting(E_ALL);

    if (isset($_POST["ajouterProduitForm"])) {
        foreach ($_POST as $key => $value) {
            $_POST[$key] = htmlentities($value);
        }
        extract($_POST);

        if(!empty($_FILES['imageProduit']) && $_FILES['imageProduit']['error'] == 0) {
            $infosImg = pathinfo($_FILES['imageProduit']['name']);
            $extensionImg = $infosImg['extension'];
            $extensions_autorisees = array('jpg', 'jpeg', 'png');
            if (in_array($extensionImg, $extensions_autorisees)) {
                $extensionValide = true;
            } else {
                $extensionValide = false;
                echo '<script type="text/javascript">show_info_popup("L\'extension de l\'image n\'est pas valide.", "red")</script>';
            }
        } else {
            echo '<script type="text/javascript">show_info_popup("Erreur avec l\'enregistrement de l\'image.", "red")</script>';
        }
        if ($idRevendeur != "null") {
            $sql = "BEGIN Gestion_REVIVE.AjouterProduit(:nomProduit, :extensionImgProduit, :prixProduit,
                                                    :prixBaseProduit, :detailsProduit, :quantiteStockProduit,
                                                    :delaiLivraisonProduit, TO_DATE(:dateRetractationProduit, 'YYYY-MM-DD'), :garantieProduit,
                                                    :verifierProduit, :idRevendeur, :idCategorie); END;";
        } else {
            $sql = "BEGIN Gestion_REVIVE.AjouterProduit(:nomProduit, :extensionImgProduit, :prixProduit,
                                                    :prixBaseProduit, :detailsProduit, :quantiteStockProduit,
                                                    :delaiLivraisonProduit, TO_DATE(:dateRetractationProduit, 'YYYY-MM-DD'), :garantieProduit,
                                                    :verifierProduit, NULL, :idCategorie); END;";
        }
        $requete = oci_parse($connect, $sql);
        oci_bind_by_name($requete, ":nomProduit", $nomProduit);
        oci_bind_by_name($requete, ":extensionImgProduit", $extensionImg);
        oci_bind_by_name($requete, ":prixProduit", $prixProduit);
        oci_bind_by_name($requete, ":prixBaseProduit", $prixBaseProduit);
        oci_bind_by_name($requete, ":detailsProduit", $detailsProduit);
        oci_bind_by_name($requete, ":quantiteStockProduit", $quantiteStockProduit);
        oci_bind_by_name($requete, ":delaiLivraisonProduit", $delaiLivraisonProduit);
        oci_bind_by_name($requete, ":dateRetractationProduit", $dateRetractationProduit);
        oci_bind_by_name($requete, ":garantieProduit", $garantieProduit);
        oci_bind_by_name($requete, ":verifierProduit", $verifierProduit);
        oci_bind_by_name($requete, ":idCategorie", $idCategorie);
        if($idRevendeur != "null")   {
            oci_bind_by_name($requete, ":idRevendeur", $idRevendeur);
        }


        $result = oci_execute($requete);
        if ($result) {
            echo '<script type="text/javascript">show_info_popup("Le produit a bien été ajouté.", "var(--green-blue)")</script>';
        } else {
            echo '<script type="text/javascript">show_info_popup("Erreur avec la base de données.", "red")</script>';
            var_dump(oci_error($requete));
        }

        $sqlIdProduit = "SELECT MAX(idProduit) as idProduit FROM Produit";
        $recupIdProduit = oci_parse($connect, $sqlIdProduit);
        $result = oci_execute($recupIdProduit);
        $idProduit = oci_fetch_assoc($recupIdProduit);
        if ($extensionValide) {
            // move_uploaded_file($_FILES['imageProduit']['tmp_name'], '../public/images/' . $idProduit . '.' . $extensionImg);
        }
    }

    $sqlCategories = "SELECT idCategorie, nomCategorie FROM Categorie";
    $sqlRevendeurs = "SELECT idrevendeur, nomRevendeur FROM Revendeur";
    $selectCategories = oci_parse($connect, $sqlCategories);
    $selectRevendeurs = oci_parse($connect, $sqlRevendeurs);

    $resultCategories = oci_execute($selectCategories);
    $resultRevendeurs = oci_execute($selectRevendeurs);

?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../public/css/style.css">
        <link rel="stylesheet" href="../public/css/header.css">
        <link rel="stylesheet" href="../public/css/footer.css">
        <link rel="stylesheet" href="../public/css/ajoutProduit.css">
        <title>Ajouter produit</title>
    </head>
    <body>
        <?php include("../include/header.php") ?>
        <section>
            <div>
                <h1>Ajouter un produit</h1>
                <p>Entrez les données du nouveau produit :</p>
                <form id="form-ajouterProduit" method="post" action="" enctype="multipart/form-data">
                    <div>
                        <label for="nomProduit">Nom</label>
                        <input type="text" name="nomProduit" id="nomProduit" required>
                    </div>
                    <div>
                        <label for="prixProduit">Prix</label>
                        <input type="number" name="prixProduit" id="prixProduit" required>
                    </div>
                    <div>
                        <label for="prixBaseProduit">Prix de base</label>
                        <input type="number" name="prixBaseProduit" id="prixBaseProduit" required>
                    </div>
                    <div>
                        <label for="detailsProduit">Détails</label>
                        <input type="text" name="detailsProduit" id="detailsProduit" required>
                    </div>
                    <div>
                        <label for="quantiteStockProduit">Quantité en stock</label>
                        <input type="number" name="quantiteStockProduit" id="quantiteStockProduit" required>
                    </div>
                    <div>
                        <label for="delaiLivraisonProduit">Délai de livraison</label>
                        <input type="number" name="delaiLivraisonProduit" id="delaiLivraisonProduit" required>
                    </div>
                    <div>
                        <label for="dateRetractationProduit">Date de rétractation</label>
                        <input type="date" name="dateRetractationProduit" id="dateRetractationProduit" required>
                    </div>
                    <div>
                        <label for="garantieProduit">Garantie</label>
                        <input type="number" name="garantieProduit" id="garantieProduit" required>
                    </div>

                    <div>
                        <label for="verifierProduit">Produit vérifié</label>
                        <select name="verifierProduit" id="verifierProduit">
                            <option value="1">Oui</option>
                            <option value="0">Non</option>
                        </select>
                    </div>

                    <div>
                        <label for="idRevendeur">Nom du Revendeur</label>
                        <select name="idRevendeur" id="idRevendeur">
                            <option value="null">Pas de revendeur</option>
                            <?php
                            $result = array();
                            oci_fetch_all($selectRevendeurs, $result);
                            for ($i = 0; $i < count($result["IDREVENDEUR"]); $i++) { ?>
                                <option value="<?= $result["IDREVENDEUR"][$i] ?>"><?= $result["NOMREVENDEUR"][$i] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <label for="idCategorie">Nom de la categorie</label>
                        <select name="idCategorie" id="idCategorie">
                            <?php
                            $result = array();
                            oci_fetch_all($selectCategories, $result);
                            for ($i = 0; $i < count($result["IDCATEGORIE"]); $i++) { ?>
                                <option value="<?= $result["IDCATEGORIE"][$i] ?>"><?= $result["NOMCATEGORIE"][$i] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <label for="imageProduit">Image</label>
                        <input type="file" onchange="loadFile();" name="imageProduit" id="imageProduit" accept="image/*" required>
                        <img id="imagePreview" src="" alt="Preview">
                    </div>
                    <input type="submit" name="ajouterProduitForm" id="inputImage" value="Ajouter">
                </form>
            </div>
        </section>

        <script>
            let imageProduit = document.getElementById('imageProduit');
            let imagePreview = document.getElementById('imagePreview');

            function loadFile() {
                if (imageProduit.files && imageProduit.files[0]) {
                    imagePreview.style.display = "block";
                    imagePreview.src = URL.createObjectURL(imageProduit.files[0]);
                } else {
                    imagePreview.style.display = "none";
                }
            }
        </script>

        <?php include("../include/footer.php") ?>
    </body>
</html>
