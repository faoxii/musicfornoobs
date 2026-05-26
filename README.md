# MusicForNoobs

**Projet Web1 — LE1 2025-2026 — IG2I Centrale Lille**
**Auteurs :** LEFEBVRE Lucas — BOURGUIGNON Mathis

Site d'apprentissage de la théorie musicale pour débutants : fiches pédagogiques sur les gammes, accords et intervalles, avec exemples audio et quiz de validation.

---

## Installation

1. Cloner le repo dans le répertoire web du serveur :
   ```bash
   cd /Applications/MAMP/htdocs/
   git clone https://github.com/<utilisateur>/musicfornoobs.git
   ```
2. Dans phpMyAdmin, créer une base nommée `musicfornoobs` (interclassement `utf8mb4_unicode_ci`).
3. Importer le fichier `musicfornoobs.sql` dans cette base (onglet "Importer").
4. Lancer l'application dans le navigateur : `http://localhost:8888/musicfornoobs/`.

Si besoin, adapter les paramètres de connexion dans `libs/config.php`.

---

## Stack technique

- **PHP** + **MySQL/MariaDB** via phpMyAdmin (MAMP / XAMPP)
- **Architecture MVC tinyMVC** : `index.php` (vues), `controleur.php` (actions), `libs/modele/` (5 modèles séparés)
- **CSS natif** (pas de framework), 3 fichiers (`style.css`, `components.css`, `pages.css`)
- **HTML5 Audio** pour la lecture des exemples sonores

---

## Structure

```
musicfornoobs/
├── index.php              ← routeur des vues (GET)
├── controleur.php         ← traitement des actions (POST)
├── libs/                  ← helpers + modèles
│   ├── config.php
│   ├── maLibSQL.pdo.php   ← helpers SQL du tinyMVC
│   ├── maLibUtils.php     ← helpers généraux du tinyMVC
│   ├── maLibSecurisation.php
│   └── modele/            ← 5 modèles séparés par domaine
├── templates/             ← 14 vues + header + footer
├── css/                   ← 3 fichiers de style
├── js/                    ← audio.js + quiz.js
└── assets/
    └── audio/
        ├── fiches/        ← mp3 d'exemple des fiches
        └── quiz/          ← mp3 des questions audio
```

---

## Workflow Git

- Branche `main` : version stable, ce qu'on rendra
- Branches `lucas/xxx` et `mathis/xxx` pour le développement
- Pull requests pour merger sur `main` après relecture rapide

---

## Répartition des tâches

**Lucas** — Front-office utilisateur + auth
- Modèles : `users`, `quiz`, `resultats`
- Vues : accueil, connexion, inscription, quiz, quiz_play, resultats, dashboard, classement
- Actions : Connexion, Inscription, Logout, SoumettreQuiz

**Mathis** — Front-office fiches + back-office admin
- Modèles : `fiches`, `progression`
- Vues : fiches, fiche_read, admin_fiches, admin_fiche_form, admin_questions, admin_question_form
- Actions : MarquerLue, CRUD fiches, CRUD questions

Détails dans le Livrable 3 sur le Drive du projet.
