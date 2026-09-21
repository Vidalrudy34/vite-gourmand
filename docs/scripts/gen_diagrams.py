"""
Génère les diagrammes (MCD/classes, cas d'utilisation, séquence) et les
wireframes (desktop + mobile) sous forme d'images PNG, utilisées dans la
documentation PDF du projet Vite & Gourmand.
"""
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from matplotlib.patches import FancyArrowPatch, Ellipse, Rectangle
import os

OUT = os.path.join(os.path.dirname(__file__), '..', 'img')
os.makedirs(OUT, exist_ok=True)

PRIMARY = '#C1440E'
SECONDARY = '#2F4B3C'
CREAM = '#FBF3E7'
TEXT = '#2B2118'


def box(ax, x, y, w, h, title, lines, color=SECONDARY):
    ax.add_patch(Rectangle((x, y), w, h, facecolor='white', edgecolor=color, linewidth=1.5, zorder=2))
    ax.add_patch(Rectangle((x, y + h - 0.35), w, 0.35, facecolor=color, edgecolor=color, zorder=3))
    ax.text(x + w / 2, y + h - 0.175, title, ha='center', va='center', color='white', fontsize=9, fontweight='bold', zorder=4)
    for i, line in enumerate(lines):
        ax.text(x + 0.1, y + h - 0.55 - i * 0.245, line, ha='left', va='center', fontsize=6.8, color=TEXT, zorder=4)


def arrow(ax, p1, p2, label=''):
    a = FancyArrowPatch(p1, p2, arrowstyle='-', mutation_scale=10, color='#555', linewidth=1.2, zorder=1)
    ax.add_patch(a)
    if label:
        mx, my = (p1[0] + p2[0]) / 2, (p1[1] + p2[1]) / 2
        ax.text(mx, my + 0.1, label, ha='center', fontsize=6.5, color='#555', style='italic')


# ---------------------------------------------------------------
# 1. Modèle conceptuel de données (simplifié, orienté classes)
# ---------------------------------------------------------------
fig, ax = plt.subplots(figsize=(13, 9))
ax.set_xlim(0, 13); ax.set_ylim(0, 9); ax.axis('off')

box(ax, 0.3, 6.7, 2.6, 2, 'Role', ['role_id (PK)', 'libelle'])
box(ax, 3.4, 6.15, 2.8, 2.65, 'Utilisateur', ['utilisateur_id (PK)', 'email', 'mot_de_passe', 'nom, prenom', 'telephone', 'adresse, ville, cp', 'role_id (FK)', 'actif'])
box(ax, 0.3, 3.45, 2.6, 2.55, 'Commande', ['commande_id (PK)', 'numero_commande', 'utilisateur_id (FK)', 'menu_id (FK)', 'date_prestation', 'nombre_personnes', 'prix_total', 'statut'])
box(ax, 3.6, 3.6, 2.8, 2.4, 'Menu', ['menu_id (PK)', 'titre, description', 'theme_id (FK)', 'regime_id (FK)', 'nombre_personnes_min', 'prix_par_personne', 'quantite_disponible'])
box(ax, 7.0, 6.6, 2.4, 1.6, 'Theme', ['theme_id (PK)', 'libelle'])
box(ax, 7.0, 4.4, 2.4, 1.6, 'Regime', ['regime_id (PK)', 'libelle'])
box(ax, 6.9, 1.2, 2.6, 2, 'Plat', ['plat_id (PK)', 'nom', 'type'])
box(ax, 9.9, 1.2, 2.9, 2, 'Allergene', ['allergene_id (PK)', 'libelle'])
box(ax, 0.3, 1.2, 2.6, 2, 'Avis', ['avis_id (PK)', 'commande_id (FK)', 'note', 'commentaire', 'statut'])
box(ax, 9.9, 6.6, 2.9, 1.6, 'Horaire', ['horaire_id (PK)', 'jour', 'heure_ouverture', 'heure_fermeture'])

arrow(ax, (2.9, 7.7), (3.4, 7.7), '0,n - 1,1')
arrow(ax, (1.6, 6.7), (1.6, 6.0), '1,1 - 0,n')
arrow(ax, (2.9, 4.8), (3.6, 4.8), '0,n - 1,1')
arrow(ax, (5.0, 6.0), (5.0, 6.5))
arrow(ax, (6.4, 7.3), (7.0, 7.3), '0,n - 1,1')
arrow(ax, (6.4, 5.2), (7.0, 5.2), '0,n - 1,1')
arrow(ax, (5.2, 3.6), (7.2, 2.6), 'menu_plat 0,n-0,n')
arrow(ax, (8.2, 1.2), (9.9, 1.9), 'plat_allergene 0,n-0,n')
arrow(ax, (1.6, 3.45), (1.6, 3.2))
arrow(ax, (9.9, 7.2), (6.2, 7.2), '0,n')

ax.set_title("Modèle conceptuel de données - Vite & Gourmand", fontsize=13, fontweight='bold', color=SECONDARY)
plt.tight_layout()
plt.savefig(os.path.join(OUT, 'mcd.png'), dpi=170)
plt.close()

# ---------------------------------------------------------------
# 2. Diagramme de cas d'utilisation
# ---------------------------------------------------------------
fig, ax = plt.subplots(figsize=(11, 8.5))
ax.set_xlim(0, 11); ax.set_ylim(0, 8.5); ax.axis('off')

def actor(ax, x, y, label):
    ax.plot(x, y + 0.55, 'o', markersize=14, markerfacecolor='white', markeredgecolor=SECONDARY, zorder=3)
    ax.plot([x, x], [y + 0.15, y + 0.45], color=SECONDARY, zorder=3)
    ax.plot([x - 0.25, x + 0.25], [y + 0.35, y + 0.35], color=SECONDARY, zorder=3)
    ax.plot([x, x - 0.2], [y + 0.15, y - 0.15], color=SECONDARY, zorder=3)
    ax.plot([x, x + 0.2], [y + 0.15, y - 0.15], color=SECONDARY, zorder=3)
    ax.text(x, y - 0.35, label, ha='center', fontsize=8, fontweight='bold')

def usecase(ax, x, y, w, h, label):
    e = Ellipse((x, y), w, h, facecolor=CREAM, edgecolor=PRIMARY, linewidth=1.3, zorder=2)
    ax.add_patch(e)
    ax.text(x, y, label, ha='center', va='center', fontsize=7.3, wrap=True)

ax.add_patch(Rectangle((2.2, 0.3), 6.8, 7.9, fill=False, edgecolor='#999', linestyle='--'))
ax.text(5.6, 8.05, 'Application Vite & Gourmand', ha='center', fontsize=9, style='italic', color='#666')

actor(ax, 0.7, 6.8, 'Visiteur')
actor(ax, 0.7, 4.2, 'Utilisateur')
actor(ax, 10.2, 6.0, 'Employé')
actor(ax, 10.2, 2.2, 'Administrateur')

ucs_visitor = [(3.2, 7.4, 'Consulter les menus'), (3.2, 6.4, 'Filtrer les menus'), (3.2, 5.4, "Créer un compte"), (3.2, 4.5, 'Contacter l\'entreprise')]
for x, y, l in ucs_visitor:
    usecase(ax, x, y, 2.1, 0.75, l)
    arrow(ax, (1.1, 6.8), (x - 1.05, y))

ucs_user = [(5.6, 6.3, 'Se connecter'), (5.6, 5.3, 'Commander un menu'), (5.6, 4.3, 'Suivre ses commandes'), (5.6, 3.3, 'Modifier / annuler'), (5.6, 2.3, 'Déposer un avis')]
for x, y, l in ucs_user:
    usecase(ax, x, y, 2.3, 0.75, l)
    arrow(ax, (1.1, 4.2), (x - 1.2, y))

ucs_emp = [(8.0, 6.6, 'Gérer les menus'), (8.0, 5.6, 'Gérer les commandes'), (8.0, 4.6, 'Modérer les avis'), (8.0, 3.6, 'Gérer les horaires')]
for x, y, l in ucs_emp:
    usecase(ax, x, y, 2.1, 0.75, l)
    arrow(ax, (9.6, 6.0), (x + 1.05, y))

usecase(ax, 8.0, 2.0, 2.4, 0.8, 'Gérer les comptes employés')
usecase(ax, 8.0, 1.0, 2.4, 0.8, 'Consulter les statistiques')
arrow(ax, (9.6, 2.2), (9.2, 2.0))
arrow(ax, (9.6, 2.2), (9.2, 1.0))
ax.annotate('', xy=(8.0, 2.4), xytext=(8.0, 3.2), arrowprops=dict(arrowstyle='->', linestyle='dashed', color='#888'))
ax.text(8.3, 2.8, '<<hérite>>', fontsize=6, color='#888')

ax.set_title("Diagramme de cas d'utilisation - Vite & Gourmand", fontsize=13, fontweight='bold', color=SECONDARY)
plt.tight_layout()
plt.savefig(os.path.join(OUT, 'use_case.png'), dpi=170)
plt.close()

# ---------------------------------------------------------------
# 3. Diagramme de séquence : passer une commande
# ---------------------------------------------------------------
fig, ax = plt.subplots(figsize=(11, 8))
ax.set_xlim(0, 11); ax.set_ylim(0, 8); ax.axis('off')

acteurs = [('Utilisateur', 1), ('Navigateur (JS)', 3.5), ('Serveur PHP', 6), ('MySQL', 8.2), ('MongoDB', 10.2)]
for label, x in acteurs:
    ax.add_patch(Rectangle((x - 0.9, 7.2), 1.8, 0.6, facecolor=SECONDARY, edgecolor=SECONDARY))
    ax.text(x, 7.5, label, ha='center', va='center', color='white', fontsize=8, fontweight='bold')
    ax.plot([x, x], [0.3, 7.2], color='#bbb', linestyle='--', zorder=0)

messages = [
    (1, 3.5, 6.6, "Choisit un menu et remplit le formulaire"),
    (3.5, 6, 6.1, "POST /commande.php"),
    (6, 8.2, 5.6, "INSERT commande (PDO)"),
    (8.2, 6, 5.1, "OK (id commande)"),
    (6, 10.2, 4.6, "insertOne(stats_commandes)"),
    (6, 3.5, 4.0, "Redirection + confirmation"),
    (6, 1, 3.5, "Mail de confirmation (Mailer)"),
    (3.5, 1, 3.0, "Affiche le détail de la commande"),
]
for x1, x2, y, label in messages:
    arrow(ax, (x1, y), (x2, y), label)

ax.set_title("Diagramme de séquence - Passer une commande", fontsize=13, fontweight='bold', color=SECONDARY)
plt.tight_layout()
plt.savefig(os.path.join(OUT, 'sequence.png'), dpi=170)
plt.close()

print("Diagrammes générés dans", OUT)
