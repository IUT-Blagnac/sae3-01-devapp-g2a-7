<?php
    class Panier {
        private $produits;
        private $idClient;

        public function __construct($idClient = null) {
            $this->produits = array();
            $this->idClient = $idClient;
            if ($this->idClient != null) {
                $this->setProduitsPanierBaseDeDonnees();
            } else {
                // cookie expire dans 1 semaine
                setcookie("panier", serialize($this), time() + 604800, "/");
            }
        }

        public function getProduits() {
            return $this->produits;
        }

        public function setIdClient($pfIdClient) {
            $this->idClient = $pfIdClient;
        }

        public function enleverProduit($idProduit) {
            if ($this->idClient != null) {
                $this->enleverProduitBaseDeDonnees($idProduit);
            } else {
                unset($this->produits[$idProduit]);
                setcookie("panier", serialize($this), time() + 604800, "/");
            }
        }

        private function enleverProduitBaseDeDonnees($idProduit) {
            global $connect;
            $sqlEnleverProduit = "DELETE FROM CONTENIR
            WHERE CONTENIR.IDPANIER IN (
                SELECT IDPANIER FROM PANIER
                WHERE PANIER.IDCLIENT = :idClient
            )
            AND CONTENIR.IDPRODUIT = :idProduit";
            $enleverProduit = oci_parse($connect, $sqlEnleverProduit);
            oci_bind_by_name($enleverProduit, ":idClient", $this->idClient);
            oci_bind_by_name($enleverProduit, ":idProduit", $idProduit);
            oci_execute($enleverProduit);
        }

        public function changeQuantiteProduit($idProduit, $quantite) {
            if ($this->idClient != null) {
                $this->changeQuantiteProduitBaseDeDonnees($idProduit, $quantite);
            } else {
                $this->produits[$idProduit]->setQuantiteProduit($quantite);
                setcookie("panier", serialize($this), time() + 604800, "/");
            }
        }

        private function changeQuantiteProduitBaseDeDonnees($idProduit, $quantite) {
            global $connect;
            $sqlChangeQuantiteProduit = "UPDATE CONTENIR
            SET CONTENIR.QUANTITEPRODUIT = :quantite
            WHERE CONTENIR.IDPANIER IN (
                SELECT IDPANIER FROM PANIER
                WHERE PANIER.IDCLIENT = :idClient
            )
            AND CONTENIR.IDPRODUIT = :idProduit";
            $changeQuantiteProduit = oci_parse($connect, $sqlChangeQuantiteProduit);
            oci_bind_by_name($changeQuantiteProduit, ":idClient", $this->idClient);
            oci_bind_by_name($changeQuantiteProduit, ":idProduit", $idProduit);
            oci_bind_by_name($changeQuantiteProduit, ":quantite", $quantite);
            oci_execute($changeQuantiteProduit);
        }

        public function __toString() {
            $str = "<h1>Panier:</h1> <br>";
            foreach ($this->produits as $produit) {
                $str .= $produit . "<br>";
            }
            return $str;
        }

        public function prixTotalProduits() {
            $prixTotal = 0;
            foreach ($this->produits as $produit) {
                $prixTotal += $produit->getPrixProduit() * $produit->getQuantiteProduit();
            }
            return $prixTotal;
        }

        private function getContenirBaseDeDonnees() {
            global $connect;
            $sqlContenir = "SELECT * FROM CONTENIR
            WHERE CONTENIR.IDPANIER IN (
                SELECT IDPANIER FROM PANIER
                WHERE PANIER.IDCLIENT = :idClient
            )
            ORDER BY CONTENIR.IDPRODUIT";
            $contenir = oci_parse($connect, $sqlContenir);
            oci_bind_by_name($contenir, ":idClient", $this->idClient);
            $resultContenir = oci_execute($contenir);
            return $contenir;
        }

        private function getProduitsBaseDeDonnees() {
            global $connect;
            $sqlProduits = "SELECT * FROM PRODUIT
            WHERE PRODUIT.IDPRODUIT IN (
                SELECT IDPRODUIT FROM CONTENIR
                WHERE CONTENIR.IDPANIER IN (
                    SELECT IDPANIER FROM PANIER
                    WHERE PANIER.IDCLIENT = :idClient
                )
            )
            ORDER BY PRODUIT.IDPRODUIT";
            $produits = oci_parse($connect, $sqlProduits);
            oci_bind_by_name($produits, ":idClient", $this->idClient);
            $resultProduits = oci_execute($produits);
            return $produits;
        }

        private function setProduitsPanierBaseDeDonnees() {
            $contenir = $this->getContenirBaseDeDonnees();
            $produits = $this->getProduitsBaseDeDonnees();
            while ($rowContenir = oci_fetch_array($contenir, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $rowProduit = oci_fetch_array($produits, OCI_ASSOC+OCI_RETURN_NULLS);
                $produit = new Produit( $rowProduit['IDPRODUIT'],
                                        $rowProduit['NOMPRODUIT'],
                                        $rowContenir['PRIXPRODUIT'],
                                        $rowContenir['DESCRIPTIFPRODUIT'],
                                        $rowContenir['QUANTITEPRODUIT'],
                                        $rowProduit['EXTENSIONIMGPRODUIT'],
                                        $rowProduit['QUANTITESTOCKPRODUIT']);
                $this->produits[$rowProduit['IDPRODUIT']] = $produit;
            }
        }

        public function ajouterProduit(Produit $produit) {
            if ($this->idClient != null) {
                $this->ajouterProduitBaseDeDonnees($produit);
            } else {
                $this->produits[$produit->getIdProduit()] = $produit;
                setcookie("panier", serialize($this), time() + 604800, "/");
            }
        }

        private function ajouterProduitBaseDeDonnees(Produit $produit) {
            global $connect;

            $produitExistant = false;
            foreach($this->produits as $prd) {
                if ($prd->getIdProduit() == $produit->getIdProduit()) {
                    $produitExistant = true;
                    break;
                }
            }

            // récupère l'id du panier du client
            $sqlIdPanier = "SELECT IDPANIER FROM PANIER
            WHERE PANIER.IDCLIENT = :idClient";
            $idPanier = oci_parse($connect, $sqlIdPanier);
            oci_bind_by_name($idPanier, ":idClient", $this->idClient);
            $resultIdPanier = oci_execute($idPanier);
            $rowIdPanier = array();
            oci_fetch_all($idPanier, $rowIdPanier);
            $idPanierClient = intval($rowIdPanier['IDPANIER'][0]);

            $quantiteProduit = $produit->getQuantiteProduit();
            $descriptifProduit = $produit->getDescriptionProduit();
            $idProduit = $produit->getIdProduit();
            $prixProduit = $produit->getPrixProduit();

            if (!$produitExistant) {

                // insère le produit dans la table contenir pour le panier
                $sqlInsertContenir = "INSERT INTO Contenir VALUES (:idPanier, :idProduit, :quantiteProduit, :prixProduit, :descriptifProduit)";

                $insertContenir = oci_parse($connect, $sqlInsertContenir);
                oci_bind_by_name($insertContenir, ":idPanier", $idPanierClient);
                oci_bind_by_name($insertContenir, ":idProduit", $idProduit);
                oci_bind_by_name($insertContenir, ":quantiteProduit", $quantiteProduit);
                oci_bind_by_name($insertContenir, ":prixProduit", $prixProduit);
                oci_bind_by_name($insertContenir, ":descriptifProduit", $descriptifProduit);
                oci_execute($insertContenir);
            } else {
                // met à jour la quantité du produit dans la table contenir
                $sqlUpdateContenir = "UPDATE Contenir
                                      SET QUANTITEPRODUIT = :quantiteProduit,
                                      PRIXPRODUIT = :prixProduit,
                                      DESCRIPTIFPRODUIT = :descriptifProduit
                                      WHERE IDPANIER = :idPanier
                                      AND IDPRODUIT = :idProduit";
                $updateContenir = oci_parse($connect, $sqlUpdateContenir);
                oci_bind_by_name($updateContenir, ":quantiteProduit", $quantiteProduit);
                oci_bind_by_name($updateContenir, ":prixProduit", $prixProduit);
                oci_bind_by_name($updateContenir, ":descriptifProduit", $descriptifProduit);
                oci_bind_by_name($updateContenir, ":idPanier", $idPanierClient);
                oci_bind_by_name($updateContenir, ":idProduit", $idProduit);
                oci_execute($updateContenir);
            }
        }

        public static function creerPanier() {
            switch (true) {
                case isset($_SESSION['CLIENT']) && !isset($_COOKIE['panier']):
                    $panier = new Panier($_SESSION['CLIENT']['idClient']);
                    break;
                case isset($_SESSION['CLIENT']) && isset($_COOKIE['panier']):
                    $panier = unserialize($_COOKIE['panier']);
                    $panier->setIdClient($_SESSION['CLIENT']['idClient']);
                    $panier->transformerPanierCookieEnPanierBaseDeDonnees();
                    break;
                case !isset($_SESSION['CLIENT']) && isset($_COOKIE['panier']):
                    $panier = unserialize($_COOKIE['panier']);
                    break;
                case !isset($_SESSION['CLIENT']) && !isset($_COOKIE['panier']):
                    $panier = new Panier();
                    break;
            }
            return $panier;
        }

        function transformerPanierCookieEnPanierBaseDeDonnees() {
            for($i = 0; $i < sizeof(this->produits); $i++) {
                $this->ajouterProduitBaseDeDonnees($this->produits[$i]);
            }
        }

        function validerCommande() {
            global $connect;

            // Lance la procédure nouvelleCommande
            $sqlNouvelleCommande = "BEGIN Gestion_REVIVE.NouvelleCommande(:PRIXCOMMANDE, :IDCLIENT); END;";
            $nouvelleCommande = oci_parse($connect, $sqlNouvelleCommande);
            oci_bind_by_name($nouvelleCommande, ":PRIXCOMMANDE", $this->prixTotalProduits());
            oci_bind_by_name($nouvelleCommande, ":IDCLIENT", $this->idClient);
            oci_execute($nouvelleCommande);

            // récupère le dernier idCommande
            $sqlIdCommande = "SELECT MAX(IDCOMMANDE) AS IDCOMMANDE FROM Commande";
            $idCommande = oci_parse($connect, $sqlIdCommande);
            oci_execute($idCommande);
            $rowIdCommande = array();
            oci_fetch_all($idCommande, $rowIdCommande);
            $idCommandeClient = intval($rowIdCommande['IDCOMMANDE'][0]);

            // insère les produits du panier dans la table Renseigner
            for($i = 0; $i < sizeof($this->produits); $i++) {
                $sqlInsertRenseigner = "INSERT INTO Renseigner VALUES (:idCommande, :idProduit, :quantiteProduit, :descriptifProduit)";
                $insertRenseigner = oci_parse($connect, $sqlInsertRenseigner);
                oci_bind_by_name($insertRenseigner, ":idCommande", $idCommandeClient);
                oci_bind_by_name($insertRenseigner, ":idProduit", $this->produits[$i]->getIdProduit());
                oci_bind_by_name($insertRenseigner, ":quantiteProduit", $this->produits[$i]->getQuantiteProduit());
                oci_bind_by_name($insertRenseigner, ":descriptifProduit", $this->produits[$i]->getDescriptionProduit());
                oci_execute($insertRenseigner);
            }
        }
    }


    class Produit {
        private $idProduit;
        private $nomProduit;
        private $categorie;
        private $prixProduit;
        private $descriptionProduit;
        private $quantiteProduit;
        private $extentionImageProduit;
        private $quantiteStockProduit;

        public function __construct($idProduit, $nomProduit, $prixProduit, $descriptionProduit, $quantiteProduit, $extensionImgProduit, $quantiteStockProduit) {
            $this->idProduit = $idProduit;
            $this->nomProduit = $nomProduit;
            $this->prixProduit = $prixProduit;
            $this->descriptionProduit = $descriptionProduit;
            $this->quantiteProduit = $quantiteProduit;
            $this->extentionImageProduit = $extensionImgProduit;
            $this->quantiteStockProduit = $quantiteStockProduit;
            $this->categorie = $this->setCategorie();
        }

        public function getIdProduit() {
            return $this->idProduit;
        }

        public function getNomProduit() {
            return $this->nomProduit;
        }

        public function getPrixProduit() {
            return $this->prixProduit;
        }

        public function getDescriptionProduit() {
            return $this->descriptionProduit;
        }

        public function getCategorie() {
            return $this->categorie;
        }

        public function getQuantiteProduit() {
            return $this->quantiteProduit;
        }

        public function getExtensionImgProduit() {
            return $this->extentionImageProduit;
        }

        public function getQuantiteStockProduit() {
            return $this->quantiteStockProduit;
        }

        public function setQuantiteProduit($quantite) {
            $this->quantiteProduit = $quantite;
        }

        public function __toString() {
            $res = "";
            $res .= "Id produit: " . $this->idProduit . "<br>";
            $res .= "Nom produit: " . $this->nomProduit . "<br>";
            $res .= "Prix produit: " . $this->prixProduit . "<br>";
            $res .= "Description produit: " . $this->descriptionProduit . "<br>";
            $res .= "Quantite produit: " . $this->quantiteProduit . "<br>";
            $res .= "Categorie: " . $this->categorie . "<br>";
            return $res;
        }

        private function setCategorie() {
            global $connect;
            $sqlCategorie = "SELECT * FROM CATEGORIE
            WHERE CATEGORIE.IDCATEGORIE IN (
                SELECT IDCATEGORIE FROM PRODUIT
                WHERE PRODUIT.IDPRODUIT = :idProduit
            )";

            $categorie = oci_parse($connect, $sqlCategorie);
            oci_bind_by_name($categorie, ":idProduit", $this->idProduit);
            $resultCategorie = oci_execute($categorie);
            $rowCategorie = oci_fetch_array($categorie, OCI_ASSOC);
            return $rowCategorie['NOMCATEGORIE'];
        }

    }
?>
