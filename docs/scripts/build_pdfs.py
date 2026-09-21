#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Génère les 4 PDF livrables du projet Vite & Gourmand avec reportlab."""
import os
from reportlab.lib.pagesizes import A4
from reportlab.lib.units import cm
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.enums import TA_CENTER
from reportlab.lib import colors
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Image, Table, TableStyle, PageBreak, ListFlowable, ListItem
)

BASE = os.path.dirname(__file__)
IMG = os.path.join(BASE, '..', 'img')
OUT = os.path.join(BASE, '..')

PRIMARY = colors.HexColor('#C1440E')
SECONDARY = colors.HexColor('#2F4B3C')
CREAM = colors.HexColor('#FBF3E7')

styles = getSampleStyleSheet()
styles.add(ParagraphStyle('VGTitle', parent=styles['Title'], textColor=SECONDARY, fontSize=22))
styles.add(ParagraphStyle('VGH1', parent=styles['Heading1'], textColor=SECONDARY, fontSize=15, spaceBefore=14))
styles.add(ParagraphStyle('VGH2', parent=styles['Heading2'], textColor=PRIMARY, fontSize=12, spaceBefore=10))
styles.add(ParagraphStyle('VGBody', parent=styles['BodyText'], fontSize=10, leading=14))
styles.add(ParagraphStyle('VGCover', parent=styles['Normal'], fontSize=13, alignment=TA_CENTER, textColor=SECONDARY))


def cover(elements, title, subtitle):
    elements.append(Spacer(1, 4*cm))
    elements.append(Paragraph('Vite &amp; Gourmand', styles['VGTitle']))
    elements.append(Spacer(1, 0.5*cm))
    elements.append(Paragraph(title, ParagraphStyle('sub', parent=styles['Heading2'], alignment=TA_CENTER, textColor=PRIMARY)))
    elements.append(Spacer(1, 1*cm))
    elements.append(Paragraph(subtitle, styles['VGCover']))
    elements.append(Spacer(1, 3*cm))
    elements.append(Paragraph('Projet ECF - Titre professionnel Développeur Web et Web Mobile — FastDev', styles['VGCover']))
    elements.append(PageBreak())


def para_list(items):
    return ListFlowable([ListItem(Paragraph(i, styles['VGBody'])) for i in items], bulletType='bullet', leftIndent=14)


def build_manuel():
    doc = SimpleDocTemplate(os.path.join(OUT, 'manuel-utilisation.pdf'), pagesize=A4,
                             topMargin=2*cm, bottomMargin=2*cm, leftMargin=2*cm, rightMargin=2*cm)
    e = []
    cover(e, "Manuel d'utilisation", "Présentation de l'application et parcours utilisateurs")

    e.append(Paragraph("1. Présentation générale", styles['VGH1']))
    e.append(Paragraph(
        "Vite &amp; Gourmand est une application web permettant à l'entreprise du même nom de présenter ses menus "
        "événementiels et de recevoir des commandes en ligne. Quatre profils peuvent utiliser l'application : "
        "le visiteur (non connecté), l'utilisateur (client inscrit), l'employé et l'administrateur.", styles['VGBody']))

    e.append(Paragraph("2. Comptes de démonstration", styles['VGH1']))
    data = [
        ['Rôle', 'Identifiant (email)', 'Mot de passe'],
        ['Administrateur', 'admin@vitegourmand.fr', 'Admin1234!'],
        ['Employé', 'employe@vitegourmand.fr', 'Employe1234!'],
        ['Utilisateur', 'client@vitegourmand.fr', 'Client1234!'],
    ]
    t = Table(data, colWidths=[4*cm, 7*cm, 4*cm])
    t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), SECONDARY), ('TEXTCOLOR', (0,0), (-1,0), colors.white),
        ('GRID', (0,0), (-1,-1), 0.5, colors.grey), ('FONTSIZE', (0,0), (-1,-1), 9),
        ('BOTTOMPADDING', (0,0), (-1,-1), 6), ('TOPPADDING', (0,0), (-1,-1), 6),
    ]))
    e.append(t)

    e.append(Paragraph("3. Parcours Visiteur", styles['VGH1']))
    e.append(para_list([
        "Accéder à la page d'accueil : présentation de l'entreprise, de l'équipe, avis clients validés.",
        "Cliquer sur « Nos menus » pour voir la vue globale, avec filtres par prix, thème, régime et nombre de personnes minimum (mise à jour dynamique sans rechargement).",
        "Cliquer sur un menu pour en voir le détail (composition, allergènes, conditions de commande).",
        "Pour commander, le visiteur est invité à se connecter ou à créer un compte.",
        "La page « Contact » permet d'envoyer un message à l'entreprise.",
    ]))

    e.append(Paragraph("4. Parcours Utilisateur (client)", styles['VGH1']))
    e.append(para_list([
        "Créer un compte depuis « Créer un compte » (nom, prénom, GSM, adresse, mot de passe sécurisé) — un mail de bienvenue est envoyé.",
        "Se connecter puis choisir un menu et cliquer sur « Commander » : le formulaire de commande est pré-rempli avec les informations du compte.",
        "Renseigner la date de prestation, l'adresse de livraison et le nombre de personnes : le prix se met à jour en direct (remise de 10% au-delà de 5 personnes de plus que le minimum, frais de livraison hors Bordeaux).",
        "Depuis « Mon espace », consulter l'historique des commandes, leur statut et le suivi détaillé.",
        "Tant qu'une commande est « en attente », elle peut être modifiée ou annulée.",
        "Une fois la commande « terminée », un formulaire permet de laisser une note (1 à 5) et un commentaire.",
    ]))

    e.append(Paragraph("5. Parcours Employé", styles['VGH1']))
    e.append(para_list([
        "Depuis « Espace employé », gérer les menus (créer, modifier, retirer du catalogue) et les plats associés.",
        "Gérer les commandes : filtrer par statut ou par client, faire évoluer le statut (acceptée, en préparation, en livraison, livrée, en attente de retour de matériel, terminée).",
        "Pour annuler une commande, l'employé doit obligatoirement indiquer le mode de contact utilisé et le motif (le client doit avoir été prévenu au préalable).",
        "Modérer les avis clients en attente (validation ou refus).",
        "Mettre à jour les horaires d'ouverture affichés en pied de page.",
    ]))

    e.append(Paragraph("6. Parcours Administrateur", styles['VGH1']))
    e.append(para_list([
        "Dispose de toutes les fonctionnalités de l'espace employé.",
        "Peut créer un compte employé (email + mot de passe temporaire, communiqué en direct, jamais par mail).",
        "Peut désactiver / réactiver un compte employé.",
        "Consulte le tableau de bord statistique : nombre de commandes par menu (graphique) et chiffre d'affaires par menu avec filtres par menu et par période (données issues de MongoDB).",
    ]))

    doc.build(e)


def build_charte():
    doc = SimpleDocTemplate(os.path.join(OUT, 'charte-graphique.pdf'), pagesize=A4,
                             topMargin=2*cm, bottomMargin=2*cm, leftMargin=2*cm, rightMargin=2*cm)
    e = []
    cover(e, "Charte graphique", "Identité visuelle et maquettes (wireframes)")

    e.append(Paragraph("1. Palette de couleurs", styles['VGH1']))
    swatches = [
        ('Primaire (accent, CTA)', '#C1440E'), ('Secondaire (fond header/footer)', '#2F4B3C'),
        ('Fond général', '#FBF3E7'), ('Texte', '#2B2118'),
    ]
    data = [['Couleur', 'Usage', 'Code hexadécimal']]
    tstyle = [('BACKGROUND', (0,0), (-1,0), SECONDARY), ('TEXTCOLOR', (0,0), (-1,0), colors.white),
              ('GRID', (0,0), (-1,-1), 0.5, colors.grey), ('FONTSIZE', (0,0), (-1,-1), 9)]
    for i, (usage, hexcode) in enumerate(swatches, start=1):
        data.append(['', usage, hexcode])
        tstyle.append(('BACKGROUND', (0, i), (0, i), colors.HexColor(hexcode)))
    t = Table(data, colWidths=[2*cm, 9*cm, 4*cm], rowHeights=[0.8*cm] + [1*cm]*len(swatches))
    t.setStyle(TableStyle(tstyle))
    e.append(t)

    e.append(Paragraph("2. Typographie", styles['VGH1']))
    e.append(Paragraph("Police principale : <b>Poppins</b> (titres et boutons), avec repli sur Segoe UI / Arial pour "
                        "les navigateurs ne chargeant pas la police web. Une hiérarchie claire est utilisée : "
                        "titres en gras de couleur secondaire, corps de texte en couleur texte sur fond crème.", styles['VGBody']))

    e.append(Paragraph("3. Logo", styles['VGH1']))
    e.append(Paragraph("Le logo utilisé dans l'en-tête est une composition typographique « Vite &amp; Gourmand » en "
                        "gras, sans logotype graphique complexe, pour rester lisible sur tous les supports. "
                        "Un logo illustré pourra être fourni ultérieurement par l'entreprise.", styles['VGBody']))

    e.append(Paragraph("4. Wireframes — Desktop", styles['VGH1']))
    for label, filename in [
        ("Page d'accueil", 'wireframe_desktop_accueil.png'),
        ('Vue globale des menus', 'wireframe_desktop_menus.png'),
        ('Commande', 'wireframe_desktop_commande.png'),
    ]:
        e.append(Paragraph(label, styles['VGH2']))
        e.append(Image(os.path.join(IMG, filename), width=15*cm, height=9.75*cm))
        e.append(Spacer(1, 0.4*cm))

    e.append(PageBreak())
    e.append(Paragraph("5. Wireframes — Mobile", styles['VGH1']))
    for label, filename in [
        ("Page d'accueil", 'wireframe_mobile_accueil.png'),
        ('Vue globale des menus', 'wireframe_mobile_menus.png'),
        ('Commande', 'wireframe_mobile_commande.png'),
    ]:
        e.append(Paragraph(label, styles['VGH2']))
        e.append(Image(os.path.join(IMG, filename), width=6.5*cm, height=12.1*cm))
        e.append(Spacer(1, 0.4*cm))

    doc.build(e)


def build_technique():
    doc = SimpleDocTemplate(os.path.join(OUT, 'documentation-technique.pdf'), pagesize=A4,
                             topMargin=2*cm, bottomMargin=2*cm, leftMargin=2*cm, rightMargin=2*cm)
    e = []
    cover(e, "Documentation technique", "Choix technologiques, modélisation et déploiement")

    e.append(Paragraph("1. Réflexions technologiques initiales", styles['VGH1']))
    e.append(Paragraph(
        "L'énoncé n'impose aucune technologie précise, hormis l'utilisation conjointe d'une base de données "
        "relationnelle et d'une base non relationnelle. Les choix suivants ont été retenus :", styles['VGBody']))
    e.append(para_list([
        "<b>PHP 8 + PDO</b> plutôt qu'un framework (Laravel, Symfony) : pas d'installation lourde, code explicite "
        "et rapide à comprendre pour la soutenance, tout en respectant les bonnes pratiques (requêtes préparées, MVC allégé).",
        "<b>Bootstrap 5</b> côté front : permet une intégration responsive rapide et accessible sans réinventer les composants UI, "
        "ce qui était nécessaire compte tenu du délai contraint de réalisation.",
        "<b>JavaScript natif (fetch API)</b> pour la partie dynamique (filtres de menus, calcul de prix en direct) : "
        "évite une dépendance à un framework front lourd pour un besoin ciblé.",
        "<b>MySQL/MariaDB</b> pour les données structurées et relationnelles du cœur métier (utilisateurs, menus, commandes).",
        "<b>MongoDB</b> pour les statistiques de commandes de l'espace administrateur : un modèle document convient bien "
        "à l'agrégation de données analytiques (nombre de commandes par menu, chiffre d'affaires filtré par période) "
        "sans complexifier le schéma relationnel principal.",
    ]))

    e.append(Paragraph("2. Configuration de l'environnement de travail", styles['VGH1']))
    e.append(Paragraph("Voir le fichier README.md du dépôt pour la procédure détaillée. Résumé des étapes :", styles['VGBody']))
    e.append(para_list([
        "PHP >= 8.1 avec extensions pdo_mysql et mongodb",
        "Composer pour la dépendance mongodb/mongodb",
        "Base MySQL créée à partir des scripts sql/01_structure.sql et sql/02_donnees.sql",
        "Fichier .env (copié depuis .env.example) pour la configuration locale",
        "Serveur de développement lancé avec : php -S localhost:8000 -t public",
    ]))

    e.append(Paragraph("3. Modèle conceptuel de données", styles['VGH1']))
    e.append(Image(os.path.join(IMG, 'mcd.png'), width=17*cm, height=11.7*cm))

    e.append(PageBreak())
    e.append(Paragraph("4. Diagramme de cas d'utilisation", styles['VGH1']))
    e.append(Image(os.path.join(IMG, 'use_case.png'), width=17*cm, height=13.1*cm))

    e.append(PageBreak())
    e.append(Paragraph("5. Diagramme de séquence — Passer une commande", styles['VGH1']))
    e.append(Image(os.path.join(IMG, 'sequence.png'), width=17*cm, height=12.9*cm))

    e.append(PageBreak())
    e.append(Paragraph("6. Documentation du déploiement", styles['VGH1']))
    e.append(Paragraph("Démarche retenue pour le déploiement en ligne :", styles['VGBody']))
    e.append(para_list([
        "Hébergement de l'application PHP et de la base MySQL sur une plateforme gratuite adaptée (ex. Railway, "
        "Fly.io ou un hébergeur mutualisé compatible PHP/MySQL).",
        "Configuration des variables d'environnement de production (DB_HOST, DB_USER, DB_PASSWORD, MONGO_URI, "
        "APP_URL, MAIL_LOG_ONLY=false) directement dans le tableau de bord de l'hébergeur, sans les committer dans le dépôt.",
        "Import des scripts SQL (sql/01_structure.sql puis sql/02_donnees.sql) sur la base de production.",
        "Une base MongoDB Atlas (offre gratuite) est utilisée en production pour les statistiques, avec l'URI de connexion renseignée dans MONGO_URI.",
        "Vérification post-déploiement : parcours visiteur, création de compte, commande complète, et connexion sur "
        "chacun des trois rôles avant transmission du lien au client.",
        "Le lien de l'application déployée et celui du dépôt GitHub public sont transmis avec les autres livrables.",
    ]))

    doc.build(e)


def build_gestion_projet():
    doc = SimpleDocTemplate(os.path.join(OUT, 'gestion-de-projet.pdf'), pagesize=A4,
                             topMargin=2*cm, bottomMargin=2*cm, leftMargin=2*cm, rightMargin=2*cm)
    e = []
    cover(e, "Documentation de gestion de projet", "Organisation, planning et suivi")

    e.append(Paragraph("1. Contexte et contraintes", styles['VGH1']))
    e.append(Paragraph(
        "Le projet devait être réalisé en un temps très réduit au regard du volume fonctionnel demandé (durée "
        "indicative de 70h). Le périmètre a donc été priorisé pour livrer un produit fonctionnel couvrant "
        "l'intégralité des compétences évaluées (front, back, base relationnelle, base NoSQL, sécurité, déploiement), "
        "en simplifiant certains aspects secondaires (mise en forme graphique avancée, fonctionnalités de confort).", styles['VGBody']))

    e.append(Paragraph("2. Méthode de gestion de projet", styles['VGH1']))
    e.append(Paragraph(
        "Une approche itérative inspirée d'Agile/Kanban a été retenue, avec un tableau Trello organisé en colonnes "
        "« À faire », « En cours », « Terminé », une carte par fonctionnalité (correspondant aux branches Git "
        "feature/xxx). Le développement a suivi l'ordre de dépendance technique : structure du projet puis base de "
        "données, puis authentification (bloquante pour le reste), puis front public, puis parcours de commande, "
        "puis espaces utilisateur/employé/administrateur, puis sécurité transverse, puis documentation et déploiement.", styles['VGBody']))

    e.append(Paragraph("3. Découpage des tâches (backlog)", styles['VGH1']))
    taches = [
        ['Lot', 'Contenu', 'Statut'],
        ['1. Cadrage', 'Analyse du besoin, MCD, choix technique', 'Terminé'],
        ['2. Socle technique', 'Structure projet, connexions BDD, config', 'Terminé'],
        ['3. Base de données', 'Scripts SQL structure + données de test', 'Terminé'],
        ['4. Authentification', 'Inscription, connexion, mot de passe oublié, rôles', 'Terminé'],
        ['5. Front public', 'Accueil, menus, détail menu, contact', 'Terminé'],
        ['6. Commande', 'Formulaire, calcul de prix, confirmation mail', 'Terminé'],
        ['7. Espace utilisateur', 'Suivi, modification, annulation, avis', 'Terminé'],
        ['8. Espace employé', 'Gestion menus, commandes, avis, horaires', 'Terminé'],
        ['9. Espace admin', 'Gestion employés, statistiques MongoDB', 'Terminé'],
        ['10. Sécurité', 'CSRF, hachage, contrôle d\'accès, RGPD, RGAA', 'Terminé'],
        ['11. Documentation', 'Manuel, charte, doc technique, gestion de projet', 'Terminé'],
        ['12. Déploiement', 'Mise en ligne et vérifications', 'À finaliser par l\'étudiant'],
    ]
    t = Table(taches, colWidths=[3.5*cm, 9.5*cm, 3*cm])
    t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), SECONDARY), ('TEXTCOLOR', (0,0), (-1,0), colors.white),
        ('GRID', (0,0), (-1,-1), 0.5, colors.grey), ('FONTSIZE', (0,0), (-1,-1), 8.5),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
    ]))
    e.append(t)

    e.append(Paragraph("4. Outils utilisés", styles['VGH1']))
    e.append(para_list([
        "Trello pour le suivi visuel des tâches (lien communiqué dans les livrables).",
        "Git / GitHub pour le versionnement, avec les branches main, dev et une branche feature/ par lot fonctionnel.",
        "Environnement local PHP + MySQL + MongoDB pour le développement et les tests manuels.",
    ]))

    e.append(Paragraph("5. Bilan", styles['VGH1']))
    e.append(Paragraph(
        "L'ensemble des compétences du référentiel a été mobilisé malgré la contrainte de temps. Les pistes "
        "d'amélioration identifiées pour une itération suivante : tests automatisés (PHPUnit), gestion fine des "
        "stocks en cas de commandes simultanées, et enrichissement de la charte graphique avec un vrai logo.", styles['VGBody']))

    doc.build(e)


if __name__ == '__main__':
    build_manuel()
    build_charte()
    build_technique()
    build_gestion_projet()
    print('PDF générés dans', OUT)
