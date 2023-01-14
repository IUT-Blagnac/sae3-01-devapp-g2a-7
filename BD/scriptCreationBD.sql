/*SUPPRESSION des tables (si déjà existantes)*/
DROP TABLE Renseigner;
DROP TABLE Affecter;
DROP TABLE DonnerAvis;
DROP TABLE Contenir;
DROP TABLE Assigner;
DROP TABLE Commande;
DROP TABLE Panier;
DROP TABLE Produit;
DROP TABLE Administrateur;
DROP TABLE Revendeur;
DROP TABLE Client;
DROP TABLE Choix;
DROP TABLE Categorie;
DROP TABLE Caracteristique;


/*SUPPRESSION des séquences (si déjà existantes)*/
DROP SEQUENCE seq_administrateur;
DROP SEQUENCE seq_client;
DROP SEQUENCE seq_commande;
DROP SEQUENCE seq_panier;
DROP SEQUENCE seq_produit;
DROP SEQUENCE seq_choix;
DROP SEQUENCE seq_categorie;


/*CRÉATION des tables*/

-- Utilisateurs (héritage)
CREATE TABLE Administrateur (
    idAdmin NUMBER,
    nomAdmin VARCHAR(150),
    prenomAdmin VARCHAR(150),
    mailAdmin VARCHAR(150),
    mdpAdmin VARCHAR(150),
    dateCompteAdmin DATE,
    CONSTRAINT pk_administrateur PRIMARY KEY (idAdmin),
    CONSTRAINT uk_administrateur_mailadmin UNIQUE (mailAdmin)
);
CREATE TABLE Client (
    idClient NUMBER,
    nomClient VARCHAR(150),
    prenomClient VARCHAR(150),
    mailClient VARCHAR(150),
    mdpClient VARCHAR(150),
    adresseClient VARCHAR(150),
    dateCompteClient DATE,
    accepterAnnonceClient NUMBER(1),
    CONSTRAINT pk_client PRIMARY KEY (idClient),
    CONSTRAINT uk_client_mailclient UNIQUE (mailClient),
    CONSTRAINT uk_client_accepter CHECK (accepterAnnonceClient IN (0, 1))
);

CREATE TABLE Revendeur (
    idRevendeur NUMBER,
    nomRevendeur VARCHAR(150),
    localisationRevendeur VARCHAR(150),
    CONSTRAINT pk_revendeur PRIMARY KEY (idRevendeur)
);

CREATE TABLE Categorie (
    idCategorie NUMBER,
    nomCategorie VARCHAR(150),
    idCategorieMere NUMBER,
    CONSTRAINT pk_categorie PRIMARY KEY (idCategorie),
    CONSTRAINT fk_categorie_idcategoriemere FOREIGN KEY (idCategorieMere) REFERENCES Categorie(idCategorie)
);

CREATE TABLE Produit (
    idProduit NUMBER,
    nomProduit VARCHAR(150),
    extensionImgProduit VARCHAR(5),
    prixProduit DECIMAL,
    prixBaseProduit DECIMAL,
    detailsProduit VARCHAR(256),
    quantiteStockProduit NUMBER,
    vendreProduit NUMBER(1),
    delaiLivraisonProduit NUMBER,
    dateRetractationProduit DATE,
    garantieProduit NUMBER,
    verifierProduit NUMBER(1),
    idRevendeur NUMBER,
    idCategorie NUMBER,
    CONSTRAINT pk_produit PRIMARY KEY (idProduit),
    CONSTRAINT ck_produit_prix CHECK (prixProduit >= 0),
    CONSTRAINT ck_produit_prixbase CHECK (prixBaseProduit >= 0),
    CONSTRAINT ck_produit_quantitestock CHECK (quantiteStockProduit >= 0),
    CONSTRAINT ck_produit_vendreproduit CHECK (vendreProduit IN (0, 1)),
    CONSTRAINT ck_produit_delailiv CHECK (delaiLivraisonProduit >= 1),
    CONSTRAINT ck_produit_garantieproduit CHECK (garantieProduit >= 0),
    CONSTRAINT ck_produit_verifierproduit CHECK (verifierProduit IN (0, 1)),
    CONSTRAINT fk_produit_idrevendeur FOREIGN KEY (idRevendeur) REFERENCES Revendeur (idRevendeur),
    CONSTRAINT fk_produit_idcategorie FOREIGN KEY (idCategorie) REFERENCES Categorie (idCategorie)
);

CREATE TABLE Choix (
    idChoix NUMBER,
    libelleChoix VARCHAR(150),
    tauxChoix DECIMAL,
    CONSTRAINT pk_choix PRIMARY KEY (idChoix),
    CONSTRAINT ck_choix_tauxchoix CHECK (tauxChoix >= 1)
);

CREATE TABLE Caracteristique (
    idCaracteristique NUMBER,
    libelleCaracteristique VARCHAR(150),
    CONSTRAINT pk_caracteristique PRIMARY KEY (idCaracteristique)
);

CREATE TABLE Panier (
    idPanier NUMBER,
    idClient NUMBER,
    CONSTRAINT pk_panier PRIMARY KEY (idPanier),
    CONSTRAINT fk_panier_idclient FOREIGN KEY (idClient) REFERENCES Client(idClient)
);

CREATE TABLE Commande (
    idCommande NUMBER,
    prixCommande DECIMAL,
    dateCommande DATE,
    idClient NUMBER,
    CONSTRAINT pk_commande PRIMARY KEY (idCommande),
    CONSTRAINT ck_commande_prixcommande CHECK (prixCommande >= 0),
    CONSTRAINT fk_commande_idclient FOREIGN KEY (idClient) REFERENCES Client(idClient)
);


/*CRÉATION des classes d'association*/

-- Association entre Panier et Produit
CREATE TABLE Contenir (
    idPanier NUMBER,
    idProduit NUMBER,
    quantiteProduit NUMBER,
    prixProduit DECIMAL,
    descriptifProduit VARCHAR(150),
    CONSTRAINT pk_contenir PRIMARY KEY (idPanier, idProduit),
    CONSTRAINT ck_contenir_quantiteproduit CHECK (quantiteProduit >= 1),
    CONSTRAINT ck_contenir_prixproduit CHECK (prixProduit >= 0),
    CONSTRAINT fk_contenir_idpanier FOREIGN KEY (idPanier) REFERENCES Panier(idPanier),
    CONSTRAINT fk_contenir_idproduit FOREIGN KEY (idProduit) REFERENCES Produit(idProduit)
);

-- Association entre Commande et Produit
CREATE TABLE Renseigner (
    idCommande NUMBER,
    idProduit NUMBER,
    quantiteProduit NUMBER,
    CONSTRAINT pk_renseigner PRIMARY KEY (idCommande, idProduit),
    CONSTRAINT ck_renseigner_quantiteproduit CHECK (quantiteProduit >= 1),
    CONSTRAINT fk_renseigner_idcommande FOREIGN KEY (idCommande) REFERENCES Commande(idCommande),
    CONSTRAINT fk_renseigner_idproduit FOREIGN KEY (idProduit) REFERENCES Produit(idProduit)
);

-- Association entre Client et Produit
CREATE TABLE DonnerAvis (
    idClient NUMBER,
    idProduit NUMBER,
    descriptionAvis VARCHAR(256),
    noteAvis DECIMAL,
    CONSTRAINT pk_donneravis PRIMARY KEY (idClient, idProduit),
    CONSTRAINT fk_donneravis_idclient FOREIGN KEY (idClient) REFERENCES Client(idClient),
    CONSTRAINT fk_donneravis_idproduit FOREIGN KEY (idProduit) REFERENCES Produit(idProduit),
    CONSTRAINT ck_donneravis_noteavis CHECK (noteAvis BETWEEN 0 AND 5)
);

-- Association entre Produit et Choix
CREATE TABLE Affecter (
    idChoix NUMBER,
    idProduit NUMBER,
    typeChoix VARCHAR(150),
    CONSTRAINT pk_affecter PRIMARY KEY (idChoix, idProduit),
    CONSTRAINT fk_affecter_idchoix FOREIGN KEY (idChoix) REFERENCES Choix(idChoix),
    CONSTRAINT fk_affecter_idproduit FOREIGN KEY (idProduit) REFERENCES Produit(idProduit)
);

-- Association entre Produit et Caracteristique
CREATE TABLE Assigner (
    idCaracteristique NUMBER,
    idProduit NUMBER,
    donneeCaracteristique VARCHAR(150),
    CONSTRAINT pk_assigner PRIMARY KEY (idCaracteristique, idProduit),
    CONSTRAINT fk_assigner_idcar FOREIGN KEY (idCaracteristique) REFERENCES Caracteristique(idCaracteristique),
    CONSTRAINT fk_assigner_idproduit FOREIGN KEY (idProduit) REFERENCES Produit(idProduit)
);


/*CRÉATION des séquences*/
CREATE SEQUENCE seq_administrateur START WITH 0 MINVALUE 0;
CREATE SEQUENCE seq_client START WITH 0 MINVALUE 0;
CREATE SEQUENCE seq_commande START WITH 0 MINVALUE 0;
CREATE SEQUENCE seq_panier START WITH 0 MINVALUE 0;
CREATE SEQUENCE seq_produit START WITH 0 MINVALUE 0;
CREATE SEQUENCE seq_choix START WITH 0 MINVALUE 0;
CREATE SEQUENCE seq_categorie START WITH 0 MINVALUE 0;
