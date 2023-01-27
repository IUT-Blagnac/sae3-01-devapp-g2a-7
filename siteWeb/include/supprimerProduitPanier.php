<?php
    include("../include/panier.php");
    require_once("checkConnexion.php");
    $panier = Panier::creerPanier();

    if (isset($_POST['supprimer'])) {
        $panier->enleverProduit($_POST['idProduit']);
    }

    header("Location: ../pages/panier.php");
    exit();
?>
