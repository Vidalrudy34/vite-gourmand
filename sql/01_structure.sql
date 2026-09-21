-- ============================================================
-- Vite & Gourmand - Script de création de la base de données
-- SGBD : MySQL / MariaDB
-- ============================================================

CREATE DATABASE IF NOT EXISTS vite_gourmand CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vite_gourmand;

-- ---------------------------------------------------------
-- Rôles
-- ---------------------------------------------------------
CREATE TABLE role (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

-- ---------------------------------------------------------
-- Utilisateurs (visiteur inscrit, utilisateur, employé, admin)
-- ---------------------------------------------------------
CREATE TABLE utilisateur (
    utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    adresse_postale VARCHAR(255) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    role_id INT NOT NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    token_reset VARCHAR(255) NULL,
    token_reset_expire DATETIME NULL,
    FOREIGN KEY (role_id) REFERENCES role(role_id)
);

-- ---------------------------------------------------------
-- Thèmes et régimes des menus
-- ---------------------------------------------------------
CREATE TABLE theme (
    theme_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE regime (
    regime_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE allergene (
    allergene_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

-- ---------------------------------------------------------
-- Plats (entrée / plat / dessert), réutilisables dans plusieurs menus
-- ---------------------------------------------------------
CREATE TABLE plat (
    plat_id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    type ENUM('entree','plat','dessert') NOT NULL,
    photo VARCHAR(255) NULL
);

CREATE TABLE plat_allergene (
    plat_id INT NOT NULL,
    allergene_id INT NOT NULL,
    PRIMARY KEY (plat_id, allergene_id),
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id) ON DELETE CASCADE,
    FOREIGN KEY (allergene_id) REFERENCES allergene(allergene_id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Menus
-- ---------------------------------------------------------
CREATE TABLE menu (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    theme_id INT NOT NULL,
    regime_id INT NOT NULL,
    nombre_personnes_min INT NOT NULL DEFAULT 1,
    prix_par_personne DECIMAL(8,2) NOT NULL,
    conditions TEXT NULL,
    quantite_disponible INT NOT NULL DEFAULT 0,
    photo_principale VARCHAR(255) NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (theme_id) REFERENCES theme(theme_id),
    FOREIGN KEY (regime_id) REFERENCES regime(regime_id)
);

CREATE TABLE menu_image (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE
);

CREATE TABLE menu_plat (
    menu_id INT NOT NULL,
    plat_id INT NOT NULL,
    PRIMARY KEY (menu_id, plat_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id) ON DELETE CASCADE,
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Horaires (pied de page, du lundi au dimanche)
-- ---------------------------------------------------------
CREATE TABLE horaire (
    horaire_id INT AUTO_INCREMENT PRIMARY KEY,
    jour ENUM('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche') NOT NULL UNIQUE,
    heure_ouverture TIME NULL,
    heure_fermeture TIME NULL,
    ferme TINYINT(1) NOT NULL DEFAULT 0
);

-- ---------------------------------------------------------
-- Commandes
-- ---------------------------------------------------------
CREATE TABLE commande (
    commande_id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(30) NOT NULL UNIQUE,
    utilisateur_id INT NOT NULL,
    menu_id INT NOT NULL,
    date_commande DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_prestation DATE NOT NULL,
    heure_livraison TIME NOT NULL,
    adresse_livraison VARCHAR(255) NOT NULL,
    ville_livraison VARCHAR(100) NOT NULL,
    code_postal_livraison VARCHAR(10) NOT NULL,
    distance_km DECIMAL(6,2) NOT NULL DEFAULT 0,
    nombre_personnes INT NOT NULL,
    prix_menu DECIMAL(10,2) NOT NULL,
    prix_livraison DECIMAL(10,2) NOT NULL,
    remise DECIMAL(10,2) NOT NULL DEFAULT 0,
    prix_total DECIMAL(10,2) NOT NULL,
    statut ENUM('en_attente','accepte','en_preparation','en_cours_de_livraison','livre','en_attente_retour_materiel','terminee','annulee') NOT NULL DEFAULT 'en_attente',
    materiel_prete TINYINT(1) NOT NULL DEFAULT 0,
    materiel_restitue TINYINT(1) NOT NULL DEFAULT 0,
    date_limite_restitution DATE NULL,
    motif_annulation VARCHAR(255) NULL,
    mode_contact_annulation VARCHAR(100) NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id)
);

-- Historique des statuts (utilisé pour le suivi de commande)
CREATE TABLE commande_historique (
    historique_id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    statut VARCHAR(50) NOT NULL,
    date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commande(commande_id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Avis clients (visibles sur l'accueil une fois validés)
-- ---------------------------------------------------------
CREATE TABLE avis (
    avis_id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL UNIQUE,
    utilisateur_id INT NOT NULL,
    note TINYINT NOT NULL,
    commentaire TEXT NOT NULL,
    statut ENUM('en_attente','valide','refuse') NOT NULL DEFAULT 'en_attente',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CHECK (note BETWEEN 1 AND 5),
    FOREIGN KEY (commande_id) REFERENCES commande(commande_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id)
);

-- ---------------------------------------------------------
-- Messages de contact
-- ---------------------------------------------------------
CREATE TABLE contact (
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    email VARCHAR(150) NOT NULL,
    date_envoi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    traite TINYINT(1) NOT NULL DEFAULT 0
);
