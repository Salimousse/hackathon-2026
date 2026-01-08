# Hackathon 2026 - Plateforme de Recherche d'Associations

Application web permettant de rechercher, consulter et évaluer des associations en France. Le projet utilise l'API du Répertoire National des Associations pour fournir des informations actualisées.

## 📋 Fonctionnalités

- Rechercher des associations par nom, ville ou code postal
- Localiser les associations via géolocalisation
- Consulter les détails complets des associations (adresse, contact, description)
- Ajouter et consulter des avis/commentaires sur les associations
- S'inscrire comme membre d'une association

## 🛠️ Technologies utilisées

### Backend
- **PHP 8.2+**
- **Laravel 12.0**
- **Laravel Breeze** - Authentification
- **Livewire 3.7** - Composants dynamiques
- **MySQL** - Base de données

### Frontend
- **Blade** - Templates
- **Tailwind CSS 3.1**
- **Alpine.js 3.4**
- **Vite 7.0**

### APIs externes
- **API Huwise** - Répertoire National des Associations
- **OpenStreetMap** - Cartographie

## 🚀 Installation

### Prérequis

- PHP 8.2+
- Composer
- Node.js 18+ et NPM
- MySQL 8.0+
- Git

### Étapes

**1. Cloner le projet**
```bash
git clone <url-du-repo>
cd hackathon_2026
```

**2. Installer les dépendances**
```bash
composer install
npm install
```

**3. Configurer l'environnement**
```bash
copy .env.example .env
php artisan key:generate
```

**4. Configurer la base de données**

Dans le fichier `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hackathon_2026
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

Créer la base de données :
```sql
CREATE DATABASE hackathon_2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**5. Lancer les migrations**
```bash
php artisan migrate
php artisan db:seed --class=DemoDataSeeder
```

**6. Compiler les assets**
```bash
npm run build
```

**7. Démarrer le serveur**
```bash
php artisan serve
```

L'application sera accessible à : `http://localhost:8000`
