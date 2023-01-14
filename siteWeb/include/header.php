<?php
    require_once("checkConnexion.php");
    error_reporting(0);

    // Récupère l'instance du panier
    require_once("../include/panier.php");
    $panier = Panier::creerPanier();

    function afficher_categories($idCategorieMere=null, $niveau=1) {
        global $connect;
        if ($idCategorieMere == null) {
            $sql = "SELECT idCategorie, nomCategorie FROM Categorie
                WHERE idCategorieMere IS NULL";
            $requete = oci_parse($connect, $sql);
        } else {
            $sql = "SELECT idCategorie, nomCategorie FROM Categorie
                WHERE idCategorieMere = :idCategorieMere";
            $requete = oci_parse($connect, $sql);
            oci_bind_by_name($requete, ":idCategorieMere", $idCategorieMere);
        }
        $result = oci_execute($requete);
        if (!$result) {
            echo "<script>throw 'Une erreur est survenue avec la base de données.'</script>";
            return null;
        }
        $categories = array();
        oci_fetch_all($requete, $categories);
        $idUl = $categories["IDCATEGORIE"][0] ?? "x";
        echo "<ul id='ul".$idUl."'>";
        for ($i=0; $i<count($categories["IDCATEGORIE"]); $i++) {
            echo "<li id='li".$categories["IDCATEGORIE"][$i]."'>".
                "<a href='../pages/listerProduits?idCategorie=".
                $categories["IDCATEGORIE"][$i]."'\">".
                $categories["NOMCATEGORIE"][$i]."</a>";
            afficher_categories($categories["IDCATEGORIE"][$i], $niveau+1);
            echo "</li>";
        }
        echo "</ul><style>
            #ul".$idUl." {
                display:none;
            }
            #li".$idCategorieMere.":hover #ul".$idUl." {
                display:block;
            } </style>";
    }
?>

<header>
    <div>
        <a href="../pages/index.php" class="logo">
            <img src="../public/images/logo.png" alt="Revive">
            <span>REVIVE</span>
        </a>
    </div>
    <nav>
        <a class="lien" href="">
            <img src="../public/images/information.png" alt="Information">
            <span>INFORMATION</span>
        </a>
        <?php if (isset($_SESSION['CLIENT']) || isset($_SESSION['ADMIN'])) { ?>
            <a class="lien" href="./consultCompte.php">
                <img src="../public/images/userIcon.png" alt="Icon utilisateur">
                <span>MON PROFIL</span>
            </a>
        <?php } else { ?>
            <a class="lien" href="./connexion.php">
                <img src="../public/images/userIcon.png" alt="Icon utilisateur">
                <span>CONNEXION</span>
            </a>
        <?php } ?>
        <a class="lien" href="panier.php">
            <img src="../public/images/pannier.png" alt="Panier">
            <span>PANIER</span>
        </a>
    </nav>
    <div class="searchBox">
        <form action="listerProduits.php" method="GET">
            <input type="text" placeholder="Rechercher un produit..." name="query">
            <button><img src="../public/images/loupe.png" alt="Rechercher"></button>
        </form>
    </div>
</header>
<div class="subHeader">
    <div class="navbar">
        <div id="div-submenu">
            <a class="lien" id="titleSubmenu">
                <img src="../public/images/menuIcon.png" alt="Menu Icon"> NOS PRODUITS
            </a>
            <?php afficher_categories() ?>
        </div>
        <?php if (isset($_SESSION['CLIENT'])) {
            echo '<a class="lien" href="../pages/revendreProduit.php">REVENDRE</a>';
        }
        if (isset($_SESSION['ADMIN'])) { ?>
<<<<<<< HEAD
            <div id="div-submenu2">
                <a class="lien" id="titleSubmenu2">GESTION</a>
=======
            <div id="div-submenu">
                <a class="lien" id="titleSubmenu">GESTION</a>
>>>>>>> 3cc7881d859801e7e06a31c5efb64b11a83935d6
                <ul>
                    <li><a>PRODUITS</a>
                        <ul>
                            <li><a href="../pages/ajouterProduit.php">Ajouter</a></li>
                            <li><a href="../pages/supprimerProduit.php">Supprimer</a></li>
                        </ul>
                    </li>
                    <li><a>CATÉGORIES</a>
                        <ul>
                            <li><a href="../pages/ajouterCategorie.php">Ajouter</a></li>
                            <li><a href="../pages/supprimerCategorie.php">Supprimer</a></li>
                        </ul>
                    </li>
<<<<<<< HEAD
                    <!--<li><a>CHOIX</a>
=======
                    <li><a>CHOIX</a>
>>>>>>> 3cc7881d859801e7e06a31c5efb64b11a83935d6
                        <ul>
                            <li><a href="../pages/ajouterChoix.php">Ajouter</a></li>
                            <li><a href="../pages/supprimerChoix.php">Supprimer</a></li>
                        </ul>
<<<<<<< HEAD
                    </li>-->
=======
                    </li>
>>>>>>> 3cc7881d859801e7e06a31c5efb64b11a83935d6
                </ul>
            </div>
        <?php } ?>
    </div>
</div>
