<?php
    session_start();
    require_once("../include/connect.inc.php");
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/header.css">
    <title>Revive</title>
</head>

<body>
    <header>
        <div>
            <a href="" class="logo">
                <img src="../public/images/logo.png" alt="Revive">
                <span>REVIVE</span>
            </a>
        </div>
        <nav>
            <a href="">
                <img src="../public/images/information.png" alt="Information">
                <span>INFORMATION</span>
            </a>
            <?php if (isset($_SESSION['CLIENT'])) { ?>
                <a href="./consultCompte.php">
                    <img src="../public/images/userIcon.png" alt="Icon utilisateur">
                    <span>MON PROFIL</span>
                </a>
            <?php } else { ?>
                <a href="./connexion.php">
                    <img src="../public/images/userIcon.png" alt="Icon utilisateur">
                    <span>CONNEXION</span>
                </a>
            <?php } ?>
            <a href="">
                <img src="../public/images/pannier.png" alt="Panier">
                <span>PANIER</span>
            </a>
        </nav>
        <div class="searchBox">
            <form action="" method="GET">
                <input type="text" placeholder="Rechercher un produit..." name="query">
                <button><img src="../public/images/loupe.png" alt="Rechercher"></button>
            </form>
        </div>
    </header>
    <div class="subHeader">
        <ul class="navbar">
            <li>
                <a href="#" class="titleSubmenu">
                    <img src="../public/images/menuIcon.png" alt="Menu Icon"> NOS PRODUITS
                </a>
                <ul>
                    <li><a href="#">Téléphone</a></li>
                    <li><a href="#">Bidet</a></li>
                    <li><a href="#">Ordinateur</a></li>
                </ul>
            </li>
            <li>
                <a href="#">REVENDRE</a>
            </li>
        </ul>
    </div>
</body>

</html>