<?php
    session_start();
    require_once("../include/connect.inc.php");
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

?>
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
        <a href="">
            <img src="../public/images/userIcon.png" alt="Icon utilisateur">
            <span>CONNEXION</span>
        </a>
        <a href="../pages/panier.php">
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