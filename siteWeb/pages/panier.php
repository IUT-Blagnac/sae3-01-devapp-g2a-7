<?php include("../include/checkConnexion.php"); ?>

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

<?php
    include("../include/header.php");
    include("../include/infoPopup.php");
    if (isset($_SESSION['validerCommande']) && $_SESSION['validerCommande'] == true) {
        echo '<script type="text/javascript">show_info_popup("La commande a bien été validée.", "var(--green-blue)")</script>';
        unset($_SESSION['validerCommande']);
    }
?>

<body>
<section>
    <h1>Récapitulatif de mon panier</h1>
    <div class="commande">
        <?php if (count($panier->getProduits()) == 0) { ?>
            <p style="text-align: center; width: 100%;">Votre panier est vide</p>
        <?php } else { ?>
            <div class="produits">
                <?php foreach($panier->getProduits() as $produit) { ?>
                    <div class="produit" id="<?= $produit->getIdProduit(); ?>">
                        <div class="image" onclick="location.href='consultProduit.php?idProduit=<?= $produit->getIdProduit(); ?>'">
                            <img src="../public/images/produits/<?= $produit->getIdProduit(); ?>.<?= $produit->getExtensionImgProduit(); ?>" alt="NOM DU PRODUIT">
                        </div>
                        <div class="objet" onclick="location.href='consultProduit.php?idProduit=<?= $produit->getIdProduit(); ?>'">
                            <h2><?= $produit->getCategorie(); ?> - <?= $produit->getNomProduit(); ?></h2>
                            <p class="detail"><?= $produit->getDescriptionProduit(); ?></p>
                            <p><?= $produit->getPrixProduit(); ?>€</p>
                        </div>
                        <div class="prix">
                            <form action="../include/changerQuantiteProduitPanier.php" method="post">
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
                            <form action="../include/supprimerProduitPanier.php" method="post">
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
                        <form action="../include/validerCommande.php" method="post">
                            <?php if (isset($_SESSION['CLIENT'])) { ?>
                                <button name="submit">Valider ma commande</button>
                            <?php } else { ?>
                                <a href="./connexion.php">Se connecter pour valider ma commande</a>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
