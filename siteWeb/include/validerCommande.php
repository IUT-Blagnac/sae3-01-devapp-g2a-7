<?php
    include("../include/panier.php");
    require_once("checkConnexion.php");
    $panier = Panier::creerPanier();

    if (isset($_POST['submit'])) {
        $panier->validerCommande();
    }

    header("Location: ../pages/commandes.php");
    exit();
?>
