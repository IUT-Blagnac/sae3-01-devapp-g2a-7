<?php
    include("panier.php");
    $panier = Panier->creerPanier();
    foreach ($_POST as $key => $value) {
        echo "$key => $value";
    }
    $produit = new Produit()
    $panier->ajouterProduit()
?>