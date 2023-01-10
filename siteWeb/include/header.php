<?php
    require_once("checkConnexion.php");
?>

<header>
    <div>
        <a href="../pages/index.php" class="logo">
            <img src="../public/images/logo.png" alt="Revive">
            <span>REVIVE</span>
        </a>
    </div>
    <nav>
        <a href="">
            <img src="../public/images/information.png" alt="Information">
            <span>INFORMATION</span>
        </a>
        <?php if (isset($_SESSION['CLIENT']) || isset($_SESSION['ADMIN'])) { ?>
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
