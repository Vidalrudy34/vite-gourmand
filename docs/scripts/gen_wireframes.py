"""
Génère 3 wireframes desktop + 3 wireframes mobile (basse fidélité) pour la
charte graphique : Accueil, Vue globale des menus, Commande.
"""
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
from matplotlib.patches import Rectangle
import os

OUT = os.path.join(os.path.dirname(__file__), '..', 'img')
os.makedirs(OUT, exist_ok=True)

LINE = '#666'
FILL = '#eee'
TEXT = '#333'


def block(ax, x, y, w, h, label, fill=FILL, fontsize=8):
    ax.add_patch(Rectangle((x, y), w, h, facecolor=fill, edgecolor=LINE, linewidth=1))
    ax.text(x + w / 2, y + h / 2, label, ha='center', va='center', fontsize=fontsize, color=TEXT, wrap=True)


def new_canvas(w, h):
    fig, ax = plt.subplots(figsize=(w / 100, h / 100))
    ax.set_xlim(0, w); ax.set_ylim(0, h); ax.invert_yaxis(); ax.axis('off')
    ax.add_patch(Rectangle((0, 0), w, h, fill=False, edgecolor='#333', linewidth=2))
    return fig, ax


# ---------- DESKTOP : Accueil ----------
fig, ax = new_canvas(1000, 650)
block(ax, 0, 0, 1000, 60, 'HEADER : Logo | Accueil - Menus - Contact | Connexion', '#2F4B3C', 9)
block(ax, 0, 60, 1000, 200, 'HERO : accroche + bouton "Découvrir nos menus"', '#e7d9c9')
block(ax, 40, 290, 280, 160, 'Présentation entreprise')
block(ax, 360, 290, 280, 160, 'Présentation équipe')
block(ax, 680, 290, 280, 160, 'Menu phare 1')
block(ax, 40, 470, 300, 120, 'Avis client 1')
block(ax, 360, 470, 300, 120, 'Avis client 2')
block(ax, 680, 470, 280, 120, 'Avis client 3')
block(ax, 0, 610, 1000, 40, 'FOOTER : horaires - mentions légales - CGV', '#2F4B3C', 8)
ax.set_title('Wireframe Desktop - Page d\'accueil', fontsize=11, fontweight='bold')
plt.tight_layout(); plt.savefig(os.path.join(OUT, 'wireframe_desktop_accueil.png'), dpi=150); plt.close()

# ---------- DESKTOP : Vue globale des menus ----------
fig, ax = new_canvas(1000, 650)
block(ax, 0, 0, 1000, 60, 'HEADER', '#2F4B3C', 9)
block(ax, 0, 60, 1000, 90, 'FILTRES : prix min/max - thème - régime - nb. personnes (dynamique JS)', '#e7d9c9')
for i in range(3):
    for j in range(2):
        block(ax, 30 + i * 320, 180 + j * 190, 290, 170, f'Carte menu {i + j*3 + 1}\n(titre, prix, bouton détail)')
block(ax, 0, 600, 1000, 50, 'FOOTER', '#2F4B3C', 8)
ax.set_title('Wireframe Desktop - Vue globale des menus', fontsize=11, fontweight='bold')
plt.tight_layout(); plt.savefig(os.path.join(OUT, 'wireframe_desktop_menus.png'), dpi=150); plt.close()

# ---------- DESKTOP : Commande ----------
fig, ax = new_canvas(1000, 650)
block(ax, 0, 0, 1000, 60, 'HEADER', '#2F4B3C', 9)
block(ax, 40, 90, 560, 120, 'Informations client (auto-remplies)')
block(ax, 40, 230, 560, 220, 'Formulaire prestation : date, heure,\nadresse, ville, nb. personnes')
block(ax, 630, 90, 330, 260, 'Récapitulatif prix\n(menu, remise, livraison, total)')
block(ax, 630, 370, 330, 80, 'Bouton "Valider la commande"', '#C1440E')
block(ax, 0, 600, 1000, 50, 'FOOTER', '#2F4B3C', 8)
ax.set_title('Wireframe Desktop - Commande d\'un menu', fontsize=11, fontweight='bold')
plt.tight_layout(); plt.savefig(os.path.join(OUT, 'wireframe_desktop_commande.png'), dpi=150); plt.close()

# ---------- MOBILE : Accueil ----------
fig, ax = new_canvas(375, 700)
block(ax, 0, 0, 375, 60, 'HEADER + burger', '#2F4B3C', 8)
block(ax, 0, 60, 375, 160, 'HERO', '#e7d9c9')
block(ax, 15, 235, 345, 90, 'Présentation entreprise')
block(ax, 15, 335, 345, 90, 'Présentation équipe')
block(ax, 15, 435, 345, 90, 'Menu phare')
block(ax, 15, 535, 345, 90, 'Avis clients (carrousel)')
block(ax, 0, 650, 375, 50, 'FOOTER', '#2F4B3C', 8)
ax.set_title('Wireframe Mobile - Accueil', fontsize=10, fontweight='bold')
plt.tight_layout(); plt.savefig(os.path.join(OUT, 'wireframe_mobile_accueil.png'), dpi=150); plt.close()

# ---------- MOBILE : Menus ----------
fig, ax = new_canvas(375, 700)
block(ax, 0, 0, 375, 60, 'HEADER', '#2F4B3C', 8)
block(ax, 0, 60, 375, 110, 'Filtres (accordéon)', '#e7d9c9')
for i in range(4):
    block(ax, 15, 185 + i * 120, 345, 110, f'Carte menu {i+1}')
block(ax, 0, 650, 375, 50, 'FOOTER', '#2F4B3C', 8)
ax.set_title('Wireframe Mobile - Vue globale des menus', fontsize=10, fontweight='bold')
plt.tight_layout(); plt.savefig(os.path.join(OUT, 'wireframe_mobile_menus.png'), dpi=150); plt.close()

# ---------- MOBILE : Commande ----------
fig, ax = new_canvas(375, 700)
block(ax, 0, 0, 375, 60, 'HEADER', '#2F4B3C', 8)
block(ax, 15, 75, 345, 100, 'Infos client (auto)')
block(ax, 15, 190, 345, 220, 'Formulaire prestation')
block(ax, 15, 425, 345, 140, 'Récapitulatif prix')
block(ax, 15, 580, 345, 60, 'Valider la commande', '#C1440E')
block(ax, 0, 650, 375, 50, 'FOOTER', '#2F4B3C', 8)
ax.set_title('Wireframe Mobile - Commande', fontsize=10, fontweight='bold')
plt.tight_layout(); plt.savefig(os.path.join(OUT, 'wireframe_mobile_commande.png'), dpi=150); plt.close()

print('Wireframes générés')
