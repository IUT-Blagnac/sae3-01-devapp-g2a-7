<?php
    include("../include/panier.php");
    $panier = Panier::creerPanier();
    
    // Supprime les éléments "choix-avis" et "ajoutPanier" de $_POST car non utilisés
    unset($_POST['choix-avis']);
    unset($_POST['ajoutPanier']);
    
    // Définit les variables en récupérant la valeur correspondante dans $_POST et en supprimant les élément du tableau
    $quantiteProduit = $_POST['quantiteProduit'];
    unset($_POST['quantiteProduit']);
    $idProduit = $_POST['idProduit'];
    unset($_POST['idProduit']);
    $prixProduit = $_POST['prixProduit'];
    unset($_POST['prixProduit']);
    $extensionImgProduit = $_POST['extensionImgProduit'];
    unset($_POST['extensionImgProduit']);
    $quantiteStockProduit = $_POST['quantiteStockProduit'];
    unset($_POST['quantiteStockProduit']);

    $detailProduit = "";

    foreach ($_POST as $key => $value) {
        // Sépare la clé en utilisant le caractère "-" comme délimiteur et récupère le dernier élément (ici le choix)
        $key = explode('-', $key);
        $key = end($key);
        // créer la chaîne detailProduit avec le nom du choix et son libellé
        $detailProduit .= $key . ": " . $value . ", ";
    }
    
    // Supprime les deux derniers caractères de la chaîne "$detailProduit" (une virgule et un espace en fin de chaîne)
    $detailProduit = substr($detailProduit, 0, -2);
    
    $produit = new Produit($idProduit, $nomProduit, $prixProduit, $detailProduit, $quantiteProduit, $extensionImgProduit, $quantiteStockProduit);
    $panier->ajoutProduit($produit);

    header("Location: ../pages/consultProduit.php?idProduit=$idProduit");
?>