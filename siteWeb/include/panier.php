<?php
    require_once("../include/connect.inc.php");
    class Panier {
        private $produits;
        private $idClient;

        public function __construct($idClient = null) {
            $this->produits = array();
            $this->idClient = $idClient;
            if ($this->idClient != null) {
                $this->setProduitsPanierBaseDeDonne();
            }
        }

        public function getProduits() {
            return $this->produits;
        }

        public function enleverProduit($idProduit) {
            foreach ($this->produits as $key => $produit) {
                if ($produit->getIdProduit() == $idProduit) {
                    unset($this->produits[$key]);
                }
            }
        }

        public function changeQuantiteProduit($idProduit, $quantite) {
            foreach ($this->produits as $key => $produit) {
                if ($produit->getIdProduit() == $idProduit) {
                    $this->produits[$key]->setQuantiteProduit($quantite);
                }
            }
        }

        public function __toString() {
            $str = "";
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

        private function getContenirBaseDeDonne() {
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
            return ($resultContenir) ? $contenir : false;
        }

        private function getProduitsBaseDeDonne() {
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
            return ($resultProduits) ? $produits : false;
        }

        private function setProduitsPanierBaseDeDonne() {
            $contenir = $this->getContenirBaseDeDonne();
            $produits = $this->getProduitsBaseDeDonne();
            if ($contenir && $produits) {
                while ($rowContenir = oci_fetch_array($contenir, OCI_ASSOC+OCI_RETURN_NULLS)) {
                    $rowProduit = oci_fetch_array($produits, OCI_ASSOC+OCI_RETURN_NULLS);
                    $produit = new Produit( $rowProduit['IDPRODUIT'],
                                            $rowProduit['NOMPRODUIT'],
                                            $rowContenir['PRIXPRODUIT'],
                                            $rowContenir['DESCRIPTIFPRODUIT'],
                                            $rowContenir['QUANTITEPRODUIT'],
                                            $rowProduit['EXTENSIONIMGPRODUIT'],
                                            $rowProduit['QUANTITESTOCKPRODUIT']);
                    array_push($this->produits, $produit);
                }
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