<?php
    include("../include/panier.php");
    require_once("checkConnexion.php");
    $panier = Panier::creerPanier();

    if (isset($_POST['submit'])) {
        $panier->validerCommande();
    }

    $_SESSION['validerCommande'] = true;

    header("Location: ../pages/panier.php");
    exit();
?>
