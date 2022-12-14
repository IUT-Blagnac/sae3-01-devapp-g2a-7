<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/header.css">
    <link rel="stylesheet" href="../public/css/panier.css">
    <title>Revive | Panier</title>
</head>

<body>

<?php
    include("../include/header.php");
    require_once("../include/panier.php");

    // CHECKING FOR PAINIER -------------------------------------------------------

    switch (true) {
        case isset($_SESSION['CLIENT']) && !isset($_SESSION['panier']):
            $panier = new Panier($_SESSION['CLIENT']['idClient']);
            break;
        case !isset($_SESSION['CLIENT']) && !isset($_COOKIE['panier']):
            $panier = new Panier();
            break;
        case isset($_SESSION['CLIENT']) && isset($_SESSION['panier']):
            $panier = unserialize($_SESSION['panier']);
            break;
        case !isset($_SESSION['CLIENT']) && isset($_COOKIE['panier']):
            $panier = unserialize($_COOKIE['panier']);
            break;
    }

    // CHECKING FOR PAINIER -------------------------------------------------------

    // Changer la quantité d'un produit dans le panier
    if (isset($_POST['quantiteProduit'])) {
        $panier->changeQuantiteProduit($_POST['idProduit'], $_POST['quantiteProduit']);
        if (isset($_SESSION['CLIENT'])) {
            $_SESSION['panier'] = serialize($panier);
        }
    }

    // Supprimer un produit du panier
    if (isset($_POST['supprimer'])) {
        $panier->enleverProduit($_POST['idProduit']);
        if (isset($_SESSION['CLIENT'])) {
            $_SESSION['panier'] = serialize($panier);
        }
    }

?>

<section>
    <h1>Récapitulatif de mon panier</h1>
    <div class="commande">
        <?php if (count($panier->getProduits()) == 0) { ?>
            <p>Votre panier est vide</p>
        <?php } else { ?>
            <div class="produits">
                <?php foreach($panier->getProduits() as $produit) { ?>
                    <div class="produit" id="<?= $produit->getIdProduit(); ?>">
                        <div class="image">
                            <img src="../public/images/<?= $produit->getIdProduit(); ?>.<?= $produit->getExtensionImgProduit(); ?>" alt="NOM DU PRODUIT">
                        </div>
                        <div class="objet">
                            <h2><?= $produit->getCategorie(); ?> - <?= $produit->getNomProduit(); ?></h2>
                            <p class="detail"><?= $produit->getDescriptionProduit(); ?></p>
                            <p><?= $produit->getPrixProduit(); ?>€</p>
                        </div>
                        <div class="prix">
                            <form action="./panier.php#<?= $produit->getIdProduit(); ?>" method="post">
                                <select name="quantiteProduit" onchange="this.form.submit()">
                                    <?php 
                                    $quantiteStockProduit = $produit->getQuantiteStockProduit();
                                    for($i = 1; $i <= $quantiteStockProduit; $i++) {
                                        if ($i == $produit->getQuantiteProduit()) { ?>
                                            <option value='<?= $i; ?>' selected><?= $i; ?></option>
                                        <?php } else { ?>
                                            <option value='<?= $i; ?>'><?= $i; ?></option>
                                        <?php }
                                    } ?>
                                </select>
                                <input type="hidden" name="idProduit" value="<?= $produit->getIdProduit(); ?>">
                            </form>
                            <p>
                                <?php 
                                    $prixProduit = $produit->getPrixProduit();
                                    $quantiteProduit = $produit->getQuantiteProduit();
                                    $prixTotal = $prixProduit * $quantiteProduit;
                                    echo $prixTotal;
                                ?>€
                            </p>
                        </div>
                        <div class="supprimer">
                            <form action="./panier.php" method="post">
                                <input type="hidden" name="idProduit" value="<?= $produit->getIdProduit(); ?>">
                                <button type="submit" name="supprimer"><img src="../public/images/poubelle.png" alt="Supprimer"></button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="total">
                <div class="sticky">
                    <div class="prix">
                        <?php $prixTotalProduits = $panier->prixTotalProduits(); ?>
                        <p>Produits</p>
                        <p><?= $prixTotalProduits; ?>€</p>
                    </div>
                    <div class="livraison">
                        <p>Frais de livraison</p>
                        <p>GRATUIT</p>
                    </div>
                    <div class="prixTotal">
                        <p>Total</p>
                        <p><?= $prixTotalProduits; ?>€</p>
                    </div>
                    <div class="valider">
                        <form action="" method="post">
                            <button>Valider ma commande</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>