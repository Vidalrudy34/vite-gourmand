# Vite & Gourmand

Application web de présentation de menus et de prise de commande pour l'entreprise de traiteur événementiel **Vite & Gourmand** (Bordeaux). Projet réalisé dans le cadre de l'ECF du titre professionnel Développeur Web et Web Mobile (FastDev).

## Stack technique

- **Front-end** : HTML5, CSS3 (Bootstrap 5), JavaScript (vanilla, fetch API)
- **Back-end** : PHP 8.1+ (PDO, sans framework)
- **Base de données relationnelle** : MySQL / MariaDB
- **Base de données NoSQL** : MongoDB (statistiques de commandes pour l'espace administrateur)
- **Gestion de projet** : voir `docs/gestion-de-projet.pdf`

## Prérequis

- PHP >= 8.1 avec les extensions `pdo_mysql` et `mongodb` activées
- Un serveur MySQL / MariaDB
- Un serveur MongoDB (facultatif pour les fonctionnalités hors statistiques admin)
- [Composer](https://getcomposer.org/) (pour la librairie `mongodb/mongodb`)

## Installation en local

1. **Cloner le dépôt**
   ```bash
   git clone <url-du-depot>
   cd vite-gourmand
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```
   (si l'extension `mongodb` n'est pas installée sur votre machine, cette étape peut être ignorée : l'application
   fonctionne sans, seules les statistiques de l'espace admin ne seront pas disponibles)

3. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   ```
   Puis modifier `.env` avec vos identifiants MySQL / MongoDB locaux.

4. **Créer la base de données MySQL**
   ```bash
   mysql -u root -p < sql/01_structure.sql
   mysql -u root -p < sql/02_donnees.sql
   ```

5. **(Optionnel) Démarrer MongoDB** en local, la base sera créée automatiquement à la première commande passée.

6. **Lancer le serveur PHP intégré**
   ```bash
   php -S localhost:8000 -t public
   ```
   L'application est accessible sur http://localhost:8000

## Lancer le projet avec Docker (recommandé, tout-en-un)

```bash
docker compose up --build
```

Cela démarre l'application PHP/Apache, une base MySQL (initialisée automatiquement avec `sql/01_structure.sql`
et `sql/02_donnees.sql`) et une base MongoDB. L'application est accessible sur http://localhost:8000

## Déploiement en ligne (Railway, exemple)

1. Créer un compte sur [Railway](https://railway.app) (ou tout hébergeur supportant un `Dockerfile`).
2. Créer un nouveau projet à partir du dépôt GitHub public de l'application.
3. Railway détecte le `Dockerfile` et construit l'image automatiquement.
4. Ajouter un service MySQL (plugin Railway) et un service MongoDB (ou utiliser MongoDB Atlas, offre gratuite).
5. Renseigner les variables d'environnement du service applicatif (`DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`,
   `MONGO_URI`, `MONGO_DB`, `APP_URL`, `MAIL_LOG_ONLY=false`) avec les informations fournies par les services ajoutés.
6. Importer `sql/01_structure.sql` puis `sql/02_donnees.sql` dans la base MySQL de production (Railway propose un
   client SQL intégré, ou `mysql -h <host> -u <user> -p < sql/01_structure.sql`).
7. Une fois déployé, vérifier les 3 parcours (visiteur, utilisateur, employé/admin) avant de transmettre le lien.

## Comptes de démonstration

| Rôle           | Email                        | Mot de passe    |
|----------------|-------------------------------|-----------------|
| Administrateur | admin@vitegourmand.fr         | Admin1234!      |
| Employé        | employe@vitegourmand.fr       | Employe1234!    |
| Utilisateur    | client@vitegourmand.fr        | Client1234!     |

## Structure du projet

```
vite-gourmand/
├── public/                 # Racine web (document root)
│   ├── index.php, menus.php, menu-detail.php, commande.php, ...
│   ├── espace-utilisateur/
│   ├── espace-employe/
│   ├── espace-admin/
│   ├── api/                 # Endpoints AJAX (filtres de menus)
│   ├── partials/            # header.php / footer.php
│   └── assets/               # CSS, JS, images
├── src/
│   ├── config/               # Configuration, connexions PDO / MongoDB
│   ├── helpers/               # Auth, Security (CSRF), Mailer, Price
│   ├── models/                # Accès aux données (SQL + Mongo)
│   └── bootstrap.php
├── sql/
│   ├── 01_structure.sql       # Création des tables
│   └── 02_donnees.sql         # Données de démonstration
├── docs/                      # Livrables documentaires (voir ci-dessous)
└── .env.example
```

## Bonnes pratiques Git appliquées

- Branche principale : `main`
- Branche de développement : `dev`
- Chaque fonctionnalité développée sur une branche `feature/xxx` issue de `dev`
- Merge vers `dev` après tests, puis merge vers `main` une fois `dev` validée

## Documentation

L'ensemble des livrables documentaires demandés se trouve dans le dossier `docs/` :
- Manuel d'utilisation (PDF)
- Charte graphique (PDF)
- Documentation technique (MCD, diagrammes UML, choix technologiques, configuration de l'environnement)
- Documentation de gestion de projet
- Documentation de déploiement

## Sécurité et RGPD

- Mots de passe hachés avec `password_hash()` (BCRYPT)
- Requêtes SQL exclusivement préparées (PDO, protection contre les injections SQL)
- Jetons CSRF sur tous les formulaires
- Sessions sécurisées (cookies HttpOnly, régénération d'ID à la connexion)
- Contrôle d'accès par rôle sur chaque espace (visiteur / utilisateur / employé / administrateur)
- Page dédiée aux mentions légales et à la gestion des données personnelles (RGPD)
- Accessibilité : structure sémantique, attributs `alt`, labels de formulaire, lien d'évitement, contrastes de couleur
