import { Component, computed, inject } from '@angular/core';
import { AuthService, Role } from '../../core/auth.service';

interface GuideTask {
  title: string;
  body: string;
}

interface RoleGuide {
  title: string;
  intro: string;
  navScope: string[];
  tasks: GuideTask[];
}

const GUIDES: Record<Role, RoleGuide> = {
  vendeur: {
    title: 'Encaisser en boutique',
    intro:
      "Votre journée tourne autour d'un seul écran : le POS. Ce guide couvre tout ce dont vous avez besoin, de l'ouverture de caisse le matin à sa fermeture le soir.",
    navScope: ['Tableau de bord', 'POS (Caisse)', 'Ventes'],
    tasks: [
      {
        title: 'Ouvrir votre session de caisse',
        body: "Allez dans POS (Caisse). Tant qu'aucune session n'est ouverte, l'écran vous demande un montant initial en caisse (le fond de caisse du matin). Saisissez-le et cliquez « Ouvrir la session ». Une seule session peut être ouverte à la fois.",
      },
      {
        title: 'Encaisser une vente',
        body: "Cliquez sur une carte produit pour l'ajouter au panier (un second clic augmente la quantité). Ajustez quantité ou remise ligne par ligne si besoin, choisissez le mode de paiement, saisissez le montant reçu du client, puis cliquez « Encaisser ». Le ticket affiche la monnaie à rendre.",
      },
      {
        title: "Consulter l'historique des ventes",
        body: "L'écran Ventes liste vos transactions récentes. Cliquez sur une ligne pour dérouler le détail produit par produit — utile en cas de question d'un client sur un ticket passé.",
      },
      {
        title: 'Fermer la caisse en fin de journée',
        body: "La barre du haut de l'écran POS affiche le montant théorique, calculé automatiquement. Comptez votre caisse physique, saisissez ce total dans « Montant final », puis cliquez « Fermer la session ». Un écart est normal à surveiller mais n'empêche pas la fermeture.",
      },
    ],
  },
  gestionnaire: {
    title: 'Piloter votre magasin',
    intro:
      "Vous avez tous les écrans du vendeur, plus la gestion du catalogue, des stocks et des rapports pour votre magasin. Ce guide suit le cycle de vie d'un produit : de son arrivée en stock jusqu'à sa vente.",
    navScope: [
      'Produits',
      'Stocks',
      'Entrées de stock',
      'Transferts',
      'Boutiques',
      'Fournisseurs',
      'Partenaires',
      'Rapports',
    ],
    tasks: [
      {
        title: 'Lire votre tableau de bord',
        body: "Quatre chiffres clés dès la connexion : produits actifs, stock critique, ventes du jour, chiffre d'affaires du jour. La liste Alertes de stock signale tout produit tombé sous son seuil — votre point de départ chaque matin.",
      },
      {
        title: 'Gérer le catalogue produits',
        body: "Dans Produits, cliquez « + Nouveau produit » pour créer une fiche. Le statut actif/inactif contrôle si le produit apparaît au POS et dans les rapports — passez un produit en inactif plutôt que de le supprimer pour le retirer temporairement.",
      },
      {
        title: 'Réceptionner une livraison',
        body: 'Dans Entrées de stock, choisissez le produit, la quantité reçue, le prix d\'achat unitaire, et le fournisseur ou partenaire concerné. Le stock du magasin est incrémenté automatiquement.',
      },
      {
        title: 'Transférer du stock vers une boutique',
        body: "Un produit en stock au magasin n'est pas encore vendable — il doit être transféré. Dans Transferts, choisissez le produit, la boutique de destination et la quantité. Le stock disponible s'affiche en direct pendant la saisie.",
      },
      {
        title: 'Surveiller les stocks',
        body: "L'écran Stocks a deux onglets, Magasins et Boutiques, chacun avec un badge par ligne : OK, Alerte (sous le seuil) ou Rupture (à zéro).",
      },
      {
        title: 'Générer un rapport',
        body: 'Le rapport de stock et celui des partenaires se téléchargent en un clic (PDF). Le rapport de ventes demande une période et se télécharge en PDF ou en Excel, avec le détail par boutique et par produit.',
      },
    ],
  },
  admin: {
    title: "Administrer l'ensemble du système",
    intro:
      "Vous avez accès à tous les écrans de tous les rôles, plus deux responsabilités propres à l'admin : les magasins et les comptes utilisateurs. Le reste fonctionne comme pour un gestionnaire, sans limitation à un seul magasin.",
    navScope: ['Magasins', 'Utilisateurs'],
    tasks: [
      {
        title: 'Créer un magasin',
        body: "Dans Magasins, « + Nouveau magasin » : nom, localisation, et éventuellement un responsable (un utilisateur avec le rôle gestionnaire). Vous pouvez aussi l'assigner plus tard depuis l'écran Utilisateurs.",
      },
      {
        title: 'Créer un compte gestionnaire ou vendeur',
        body: "Dans Utilisateurs, « + Nouvel utilisateur » : nom, email, mot de passe provisoire, puis le rôle. Selon le rôle, assignez un magasin (gestionnaire) ou une boutique (vendeur) — c'est ce qui détermine ce que la personne pourra voir une fois connectée.",
      },
      {
        title: 'Modifier ou retirer un accès',
        body: "« Modifier » sur une ligne utilisateur change son rôle, son magasin/boutique, ou son mot de passe. « Supprimer » révoque l'accès immédiatement — indisponible sur votre propre compte pour éviter de vous verrouiller vous-même hors de l'application.",
      },
    ],
  },
};

@Component({
  selector: 'app-aide',
  imports: [],
  templateUrl: './aide.html',
  styleUrl: './aide.scss',
})
export class Aide {
  private readonly authService = inject(AuthService);

  protected readonly guide = computed(() => {
    const role = this.authService.user()?.role ?? 'vendeur';
    return GUIDES[role];
  });

  protected readonly roleLabel = computed(() => {
    const role = this.authService.user()?.role;
    return role === 'admin' ? 'Administrateur' : role === 'gestionnaire' ? 'Gestionnaire' : 'Vendeur';
  });
}
