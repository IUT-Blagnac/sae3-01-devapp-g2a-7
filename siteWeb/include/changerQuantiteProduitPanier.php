<?php
    include("../include/panier.php");
    require_once("checkConnexion.php");
    $panier = Panier::creerPanier();

    if (isset($_POST['quantiteProduit'])) {
        $panier->changeQuantiteProduit($_POST['idProduit'], $_POST['quantiteProduit']);
        header("Location: ../pages/panier.php#" . $_POST['idProduit']);
        exit();
    }

    header("Location: ../pages/panier.php");
    exit();
?>
