CREATE OR REPLACE PACKAGE Gestion_REVIVE IS
    PROCEDURE AjouterClient(
        nom Client.nomClient%TYPE,
        prenom Client.prenomClient%TYPE,
        mail Client.mailClient%TYPE,
        mdpHash Client.mdpClient%TYPE,
        accepterAnnonce Client.accepterAnnonceClient%TYPE);

    PROCEDURE AjouterAdministrateur(
        nom Administrateur.nomAdmin%TYPE,
        prenom Administrateur.prenomAdmin%TYPE,
        mail Administrateur.mailAdmin%TYPE,
        mdpHash Administrateur.mdpAdmin%TYPE);

    PROCEDURE AjouterCategorie(
        nom Categorie.nomCategorie%TYPE,
        idCategorieMere Categorie.idCategorieMere%TYPE);

    PROCEDURE AjouterChoix(
        libelle Choix.libelleChoix%TYPE,
        taux Choix.tauxChoix%TYPE);

    PROCEDURE AjouterCommande(
        prixCommande DECIMAL,
        idClient Commande.idClient%TYPE);

    PROCEDURE AjouterProduit(
        nom Produit.nomProduit%TYPE,
        extensionImg Produit.extensionImgProduit%TYPE,
        prix Produit.prixProduit%TYPE,
        prixBase Produit.prixBaseProduit%TYPE,
        detailsProduit Produit.detailsProduit%TYPE,
        stock Produit.quantiteStockProduit%TYPE,
        delaiLivraison Produit.delaiLivraisonProduit%TYPE,
        dateRetractation Produit.dateRetractationProduit%TYPE,
        garantie Produit.garantieProduit%TYPE,
        verifier Produit.verifierProduit%TYPE,
        idRev Produit.idRevendeur%TYPE,
        idCat Produit.idCategorie%TYPE);

    PROCEDURE SupprimerProduit(idPrd Produit.idProduit%TYPE);

END;
/
