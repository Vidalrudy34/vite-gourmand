-- ============================================================
-- Vite & Gourmand - Données de test / initialisation
-- ============================================================
USE vite_gourmand;

-- Rôles
INSERT INTO role (libelle) VALUES ('utilisateur'), ('employe'), ('administrateur');

-- Comptes de démonstration (mots de passe en clair ci-dessous, hachés en base avec password_hash/BCRYPT)
-- admin@vitegourmand.fr   / Admin1234!
-- employe@vitegourmand.fr / Employe1234!
-- client@vitegourmand.fr  / Client1234!
INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, telephone, adresse_postale, ville, code_postal, role_id, actif) VALUES
('admin@vitegourmand.fr', '$2y$12$eU7E3jxr2X5H.NvXGmnxNOhg6lqt8qJ.74b.UN6JfkSpSWgLDHwtq', 'Dupont', 'Julie', '0600000001', '12 rue de Bordeaux', 'Bordeaux', '33000', 3, 1),
('employe@vitegourmand.fr', '$2y$12$bwLs.trvMFwfKvNPa2QSqeV6qiw/k9bxOYLHuqZ/kG49O/GysilsK', 'Martin', 'Jose', '0600000002', '12 rue de Bordeaux', 'Bordeaux', '33000', 2, 1),
('client@vitegourmand.fr', '$2y$12$koXx0qui.8JLNhO0pGqMhOkl4K.dFEmSRyUXUi5fDo6Dj14ICP7qa', 'Durand', 'Marc', '0600000003', '5 avenue des Fleurs', 'Bordeaux', '33100', 1, 1);

-- Thèmes
INSERT INTO theme (libelle) VALUES ('Noel'), ('Paques'), ('Classique'), ('Evenement');

-- Régimes
INSERT INTO regime (libelle) VALUES ('Classique'), ('Vegetarien'), ('Vegan');

-- Allergènes
INSERT INTO allergene (libelle) VALUES ('Gluten'), ('Lactose'), ('Fruits a coque'), ('Oeuf'), ('Crustaces'), ('Arachide');

-- Plats
INSERT INTO plat (nom, type) VALUES
('Velouté de courge', 'entree'),
('Foie gras maison', 'entree'),
('Salade de chèvre chaud', 'entree'),
('Magret de canard', 'plat'),
('Dinde aux marrons', 'plat'),
('Risotto aux champignons', 'plat'),
('Bûche de Noël', 'dessert'),
('Tarte au citron', 'dessert'),
('Fondant au chocolat', 'dessert');

-- Horaires (fermé le lundi)
INSERT INTO horaire (jour, heure_ouverture, heure_fermeture, ferme) VALUES
('lundi', NULL, NULL, 1),
('mardi', '09:00:00', '19:00:00', 0),
('mercredi', '09:00:00', '19:00:00', 0),
('jeudi', '09:00:00', '19:00:00', 0),
('vendredi', '09:00:00', '20:00:00', 0),
('samedi', '09:00:00', '20:00:00', 0),
('dimanche', '10:00:00', '14:00:00', 0);

-- Menus
INSERT INTO menu (titre, description, theme_id, regime_id, nombre_personnes_min, prix_par_personne, conditions, quantite_disponible, actif) VALUES
('Menu de Noël Traditionnel', 'Un repas de fête complet pour célébrer Noël en famille, préparé avec des produits locaux et de saison.', 1, 1, 6, 45.00, 'Commande à passer au moins 7 jours avant la prestation. Conservation au frais obligatoire.', 20, 1),
('Menu de Pâques Végétarien', 'Un menu léger et gourmand pour fêter Pâques, sans viande ni poisson.', 2, 2, 4, 32.00, 'Commande à passer au moins 5 jours avant la prestation.', 15, 1),
('Menu Classique du Chef', 'Notre menu signature, disponible toute l\'année pour tous vos événements.', 3, 1, 2, 28.00, 'Aucune condition particulière.', 30, 1),
('Menu Réception Vegan', 'Menu 100% vegan, idéal pour les événements professionnels ou familiaux.', 4, 3, 10, 30.00, 'Commande à passer au moins 10 jours avant la prestation. Matériel de service prêté sur demande (verres, plats).', 10, 1);

-- Associations menu <-> plats
INSERT INTO menu_plat (menu_id, plat_id) VALUES
(1,1),(1,4),(1,7),
(2,3),(2,6),(2,8),
(3,2),(3,4),(3,9),
(4,1),(4,6),(4,9);

-- Allergènes des plats
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(2,2),(3,2),(6,2);
