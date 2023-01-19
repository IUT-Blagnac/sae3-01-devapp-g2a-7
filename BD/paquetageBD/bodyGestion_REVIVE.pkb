CREATE OR REPLACE PACKAGE BODY Gestion_REVIVE IS

    PROCEDURE AjouterClient(
        nom Client.nomClient%TYPE,
        prenom Client.prenomClient%TYPE,
        mail Client.mailClient%TYPE,
        mdpHash Client.mdpClient%TYPE,
        accepterAnnonce Client.accepterAnnonceClient%TYPE) IS
        erreurMail EXCEPTION;
        PRAGMA EXCEPTION_INIT (erreurMail, -00001);
    BEGIN
        INSERT INTO Client VALUES (seq_client.nextval, nom, prenom, mail, mdpHash, null, CURRENT_TIMESTAMP, accepterAnnonce);
        INSERT INTO Panier VALUES (seq_panier.nextval, seq_client.currval);
        COMMIT;
    EXCEPTION
        WHEN erreurMail THEN
            IF SQLERRM LIKE '%MAIL%' THEN
                DBMS_OUTPUT.PUT_LINE('Cette adresse mail existe déjà.');
            END IF;
        ROLLBACK;
    END;

    PROCEDURE AjouterAdministrateur(
        nom Administrateur.nomAdmin%TYPE,
        prenom Administrateur.prenomAdmin%TYPE,
        mail Administrateur.mailAdmin%TYPE,
        mdpHash Administrateur.mdpAdmin%TYPE) IS
        erreurMail EXCEPTION;
        PRAGMA EXCEPTION_INIT (erreurMail, -00001);
    BEGIN
        INSERT INTO Administrateur VALUES (seq_administrateur.nextval, nom, prenom, mail, mdpHash, CURRENT_TIMESTAMP);
        COMMIT;
    EXCEPTION
        WHEN erreurMail THEN
            IF SQLERRM LIKE '%MAIL%' THEN
                DBMS_OUTPUT.PUT_LINE('Cette adresse mail existe déjà.');
            END IF;
        ROLLBACK;
    END;

    PROCEDURE AjouterCategorie(
        nom Categorie.nomCategorie%TYPE,
        idCategorieMere Categorie.idCategorieMere%TYPE) IS
    BEGIN
        INSERT INTO Categorie VALUES (seq_categorie.nextval, nom, idCategorieMere);
        COMMIT;
    END;

    PROCEDURE AjouterChoix(
        libelle Choix.libelleChoix%TYPE,
        taux Choix.tauxChoix%TYPE
    ) IS
    BEGIN
        INSERT INTO Choix VALUES (seq_choix.nextval, libelle, taux);
        COMMIT;
    END;

    PROCEDURE AjouterCommande(
        prixCommande DECIMAL,
        idClient Commande.idClient%TYPE) IS
    BEGIN
        INSERT INTO Commande VALUES (seq_commande.nextval, prixCommande, CURRENT_TIMESTAMP, idClient);
        COMMIT;
    END;

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
        idCat Produit.idCategorie%TYPE
    ) IS
    BEGIN
        INSERT INTO Produit VALUES (seq_produit.nextval, nom, extensionImg, prix, prixBase, detailsProduit, stock, 1, delaiLivraison, dateRetractation, garantie, verifier, idRev, idCat);
        INSERT INTO Affecter VALUES (2, seq_produit.currval, 'Condition');
        INSERT INTO Affecter VALUES (4, seq_produit.currval, 'Condition');
        INSERT INTO Affecter VALUES (5, seq_produit.currval, 'Condition');
        COMMIT;
    END;

    PROCEDURE SupprimerProduit(idPrd Produit.idProduit%TYPE) IS
    BEGIN
        DELETE FROM Contenir
        WHERE Contenir.idProduit = idPrd;
        UPDATE Produit
        SET Produit.vendreProduit = 0
        WHERE Produit.idProduit = idPrd;
        COMMIT;
    END;

END;
/
