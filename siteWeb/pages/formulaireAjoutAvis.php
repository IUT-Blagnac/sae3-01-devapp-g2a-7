<?php

use LDAP\Result;

    include("../include/infoPopup.php");
    require_once("../include/checkConnexion.php");

    if(!isset($_GET['idProduit']) || !preg_match("/[0-9]{1,}/", $_GET['idProduit'])) {
        header("location: index.php");
    }

    if (isset($_SESSION["CLIENT"])) {
        $id = $_SESSION["CLIENT"]["idClient"];
    } else {
        header("Location: index.php");
    }

    $produitExiste = oci_parse($connect, "SELECT idProduit FROM Produit WHERE idProduit=".$_GET['idProduit']);
    $result = oci_execute($produitExiste);

    if (($existe = oci_fetch_assoc($produitExiste)) == false) {
        header("Location: index.php");
    }

    // Affiche un popup avec un message d'erreur
    if (!empty($messageErreur)) {
        echo '<script type="text/javascript">show_info_popup("'.$messageErreur.'", "red")</script>';
    }

    if (isset($_POST['ajouterProduit'])) {
        $description = "";
        $date = date('d/m/20y');
        if (!empty($_POST['descriptionAvis'])) {
            $description = htmlentities($_POST['descriptionAvis']);
        }
        $sqlInsertAvis = "INSERT INTO DonnerAvis
                          VALUES (:idClient, :idProduit, :descriptionAvis, :noteAvis, TO_DATE(:dateAvis, 'DD/MM/YYYY'))";

        $insertAvis = oci_parse($connect, $sqlInsertAvis) ;

        oci_bind_by_name($insertAvis, ":idClient", $id);
        oci_bind_by_name($insertAvis, ":idProduit", $_GET['idProduit']);
        oci_bind_by_name($insertAvis, ":descriptionAvis", $description);
        oci_bind_by_name($insertAvis, ":noteAvis", $_POST['noteAvis']);
        oci_bind_by_name($insertAvis, ":dateAvis", $date);

        $result = oci_execute($insertAvis);
        
        if($result) {
            header("Location: consultProduit.php?idProduit=".$_GET['idProduit']);
        } else {
            echo "aled";
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
        <title>Ajouter un avis</title>
    </head>
    <body>
        <?php include("../include/header.php") ?>
        <section>
            <div>
                <h1>Donner son avis</h1>
                <form method="post">
                    <label for="note">Note : </label>
                    <select name="noteAvis" id="note">
                        <option value='1' selected>1</option>
                        <option value='2' selected>2</option>
                        <option value='3' selected>3</option>
                        <option value='4' selected>4</option>
                        <option value='5' selected>5</option>
                    </select>
                    <textarea id="description-avis" name="descriptionAvis" wrap="wrap" rows="5" cols="30" placeholder="Expliquez votre note"></textarea>
                    <input type="submit" name="ajouterProduit" value="Valider">
                </form>
            </div>
        </section>
        <?php include("../include/footer.php") ?>
    </body>
</html>