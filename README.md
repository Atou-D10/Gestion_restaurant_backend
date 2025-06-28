# 🍽️ Backend Laravel – Gestion de Restaurant

Ce projet est le **backend d’un système complet de gestion de restaurant**, développé avec le framework **Laravel**.  
Il automatise toutes les tâches principales d’un restaurant : de la **prise de commande**, à la **réservation de tables**, jusqu'au **paiement** et à la **gestion des opérations par le gestionnaire**.

---

## 🚀 Fonctionnalités clés

- 📦 Prise de commande (menus, plats, boissons)
- 🪑 Réservation de tables en ligne
- 💳 Paiement (gestion des types de paiements, facturation)
- 👤 Authentification des clients et des gestionnaires
- 🛠️ Interface de gestion des tâches (gestionnaires)
- 📊 Statistiques de ventes et d'activités

---

## 🔧 Installation (en local)
<!--
1. Cloner le projet

git clone https://github.com/votre-utilisateur/gestion-restaurant-backend.git
cd gestion-restaurant-backend
2. Installer les dépendances PHP
composer install

3. Configurer la base de données
   Dans .env
 DB_DATABASE=restaurant_db
DB_USERNAME=root
DB_PASSWORD=  

4. Lancer les migrations
   php artisan migrate

5. Démarrer le serveur local
    php artisan serve
-->
📁 Structure du backend
app/Models/ – Modèles (Commande, Reservation, Paiement, etc.)

app/Http/Controllers/ – Contrôleurs REST pour les fonctionnalités

routes/api.php – Routes de l’API

database/migrations/ – Migrations pour les tables

app/Http/Middleware/ – Authentification et autorisations


🔐 Authentification
Authentification basée sur token

Utilisateurs : clients et gestionnaires

Middleware de sécurité pour restreindre l’accès aux routes sensibles

🧑‍💻 Auteur
Atou Diagne
🎓 Développeur fullstack | Informatique de gestion
📧 atoudiagne01@gmail.com
📍 Dakar, Sénégal

