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
?>

<section>
    <h1>Récapitulatif de mon panier</h1>
    <div class="commande">
        <div class="produits">
            <?php for ($j = 0; $j <= 10; $j++) { ?>
                <div class="produit">
                    <div class="image">
                        <img src="../public/images/produit1.png" alt="NOM DU PRODUIT">
                    </div>
                    <div class="objet">
                        <h2>CATEGORIE - NOM DU PRODUIT</h2>
                        <p class="detail">Details objet/produit</p>
                        <p>15,33€</p>
                    </div>
                    <div class="prix">
                        <form action="" method="post">
                            <select name="" id="" onchange="this.form.submit()">
                                <?php for($i = 1; $i <= 100; $i++) { ?>
                                    <option value="<?= $i; ?>"><?= $i; ?></option>
                                <?php } ?>
                            </select>
                        </form>
                        <p>15,33€</p>
                    </div>
                    <div class="supprimer">
                        <form action="" method="get">
                            <button><img src="../public/images/poubelle.png" alt="Supprimer"></button>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="total">
            <div class="sticky">
                <div class="prix">
                    <p>Produits</p>
                    <p>30,66</p>
                </div>
                <div class="livraison">
                    <p>Frais de livraison</p>
                    <p>GRATUIT</p>
                </div>
                <div class="prixTotal">
                    <p>Total</p>
                    <p>30,66</p>
                </div>
                <div class="valider">
                    <form action="" method="post">
                        <button>Valider ma commande</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>