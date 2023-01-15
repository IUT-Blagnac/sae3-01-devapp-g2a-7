<?php
    require_once("../include/checkConnexion.php");
    include("../include/infoPopup.php");

    verifier_page();

    if (isset($_POST["supprimerProduitForm"])) {
        $idProduit = $_POST["idProduit"];
        $sql = "BEGIN Gestion_REVIVE.SupprimerProduit(:idProduit); END;";
        $requete = oci_parse($connect, $sql);
        oci_bind_by_name($requete, ":idProduit", $idProduit);
        $result = oci_execute($requete);
        if ($result) {
            echo '<script type="text/javascript">show_info_popup("Le produit a bien été supprimé.", "var(--green-blue)")</script>';
        } else {
            echo '<script type="text/javascript">show_info_popup("Erreur avec la base de données.", "red")</script>';
        }
    }
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
        <link rel="stylesheet" href="../public/css/connexionStyle.css">
        <title>Supprimer produit</title>
    </head>
    <body>
        <?php include("../include/header.php") ?>
        <section>
            <div>
                <h1>Supprimer un produit</h1>
                <form id="form-ajouterCategorie" method="post">
                    <p>Choisissez le produit à supprimer :</p>
                    <select name="idProduit" required>
                        <option value="" disabled selected>ID - Produit (catégorie)</option>
                        <?php
                            $sql = "SELECT P.nomProduit, P.idProduit, C.nomCategorie FROM Produit P, Categorie C
                                    WHERE C.idCategorie = P.idCategorie";
                            $requete = oci_parse($connect, $sql);
                            $result = oci_execute($requete);
                            if ($result) {
                                $produits = array();
                                oci_fetch_all($requete, $produits);
                                for ($i=0; $i<count($produits["IDPRODUIT"]); $i++) {
                                    echo "<option value='".$produits["IDPRODUIT"][$i]."'>".$produits["IDPRODUIT"][$i]." - ".$produits["NOMPRODUIT"][$i]." (".$produits["NOMCATEGORIE"][$i].")</option>";
                                }
                            }
                        ?>
                    </select>
                    <input type="submit" name="supprimerProduitForm" value="Valider">
                </form>
            </div>
        </section>
        <?php include("../include/footer.php") ?>
    </body>
</html>
