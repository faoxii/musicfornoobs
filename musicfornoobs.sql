-- ========================================
-- MusicForNoobs - Base de donnees
-- Projet Web1 2026
-- LEFEBVRE Lucas / BOURGUIGNON Mathis
-- ========================================

DROP TABLE IF EXISTS resultats;
DROP TABLE IF EXISTS progression;
DROP TABLE IF EXISTS quiz_reponses;
DROP TABLE IF EXISTS quiz_questions;
DROP TABLE IF EXISTS fiches;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS utilisateurs;

-- ========================================
-- STRUCTURE
-- ========================================

CREATE TABLE utilisateurs (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    login           VARCHAR(50)     NOT NULL UNIQUE,
    email           VARCHAR(150)    NOT NULL,
    mot_de_passe    VARCHAR(255)    NOT NULL,
    role            ENUM('utilisateur', 'admin') NOT NULL DEFAULT 'utilisateur',
    score_total     INT             NOT NULL DEFAULT 0,
    date_inscription DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id      INT             AUTO_INCREMENT PRIMARY KEY,
    nom     VARCHAR(50)     NOT NULL UNIQUE,
    couleur VARCHAR(7)      NOT NULL
);

CREATE TABLE fiches (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    titre           VARCHAR(150)    NOT NULL,
    slug            VARCHAR(150)    NOT NULL UNIQUE,
    id_categorie    INT             NOT NULL,
    niveau          ENUM('Debutant', 'Intermediaire', 'Avance') NOT NULL,
    contenu         TEXT            NOT NULL,
    chemin_audio    VARCHAR(255)    DEFAULT NULL,
    date_creation   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categorie) REFERENCES categories(id)
);

CREATE TABLE quiz_questions (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    id_fiche        INT             NOT NULL,
    type            ENUM('texte', 'audio') NOT NULL,
    enonce          VARCHAR(500)    NOT NULL,
    chemin_audio    VARCHAR(255)    DEFAULT NULL,
    ordre           INT             NOT NULL,
    FOREIGN KEY (id_fiche) REFERENCES fiches(id) ON DELETE CASCADE
);

CREATE TABLE quiz_reponses (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    id_question     INT             NOT NULL,
    contenu         VARCHAR(255)    NOT NULL,
    est_correct     INT             NOT NULL DEFAULT 0,
    ordre           INT             NOT NULL,
    FOREIGN KEY (id_question) REFERENCES quiz_questions(id) ON DELETE CASCADE
);

CREATE TABLE progression (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur  INT             NOT NULL,
    id_fiche        INT             NOT NULL,
    date_lecture    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_utilisateur, id_fiche),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (id_fiche) REFERENCES fiches(id) ON DELETE CASCADE
);

CREATE TABLE resultats (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur  INT             NOT NULL,
    id_fiche        INT             NOT NULL,
    score           INT             NOT NULL,
    points_gagnes   INT             NOT NULL,
    date_passage    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (id_fiche) REFERENCES fiches(id) ON DELETE CASCADE
);

-- ========================================
-- DONNEES DE TEST
-- ========================================

INSERT INTO categories (nom, couleur) VALUES
    ('Gammes',      '#3B82F6'),
    ('Accords',     '#10B981'),
    ('Intervalles', '#F59E0B');

-- Mots de passe : tous les utilisateurs de test ont "motdepasse123"
INSERT INTO utilisateurs (login, email, mot_de_passe, role, score_total) VALUES
    ('Lucas_L',    'lucas@example.com',    '$2y$10$qNZSIozhQ1P7AFVbodPy6.eLXXyjTE.myNmQt6NjEFSuYKPuXTDjq', 'admin',        120),
    ('Mathis_B',   'mathis@example.com',   '$2y$10$qNZSIozhQ1P7AFVbodPy6.eLXXyjTE.myNmQt6NjEFSuYKPuXTDjq', 'admin',        104),
    ('thomas.b',   'thomas@example.com',   '$2y$10$qNZSIozhQ1P7AFVbodPy6.eLXXyjTE.myNmQt6NjEFSuYKPuXTDjq', 'utilisateur',  88),
    ('noemie_22',  'noemie@example.com',   '$2y$10$qNZSIozhQ1P7AFVbodPy6.eLXXyjTE.myNmQt6NjEFSuYKPuXTDjq', 'utilisateur',  64);

INSERT INTO fiches (titre, slug, id_categorie, niveau, contenu, chemin_audio) VALUES
    ('La gamme de Do majeur', 'gamme-do-majeur', 1, 'Debutant',
     'La gamme de Do majeur est une suite de 7 notes qui sert de point de depart a toute la theorie occidentale. Elle suit le schema ton - ton - demi-ton - ton - ton - ton - demi-ton. Les notes sont : Do, Re, Mi, Fa, Sol, La, Si.',
     'assets/audio/fiches/gamme-do-majeur.mp3'),
    ('La gamme mineure naturelle', 'gamme-mineure-naturelle', 1, 'Intermediaire',
     'La gamme mineure naturelle est construite sur le schema ton - demi-ton - ton - ton - demi-ton - ton - ton. Exemple en La mineur : La, Si, Do, Re, Mi, Fa, Sol.',
     'assets/audio/fiches/gamme-mineure-naturelle.mp3'),
    ('Les accords parfaits majeurs', 'accords-parfaits-majeurs', 2, 'Debutant',
     'Un accord parfait majeur est constitue de trois notes : la fondamentale, la tierce majeure et la quinte juste. Exemple : Do majeur = Do + Mi + Sol.',
     NULL);

INSERT INTO quiz_questions (id_fiche, type, enonce, chemin_audio, ordre) VALUES
    (1, 'audio', 'Quelle gamme entendez-vous ?',                              'assets/audio/quiz/do-majeur-ascendant.mp3', 1),
    (1, 'texte', 'Combien de notes contient une gamme majeure ?',             NULL, 2),
    (1, 'audio', 'Quel accord est joue ?',                                    'assets/audio/quiz/accord-sol-mineur.mp3',   3),
    (1, 'texte', 'Quel est l intervalle entre Do et Mi en Do majeur ?',       NULL, 4),
    (1, 'texte', 'La gamme de Do majeur contient-elle des alterations ?',     NULL, 5);

INSERT INTO quiz_reponses (id_question, contenu, est_correct, ordre) VALUES
    -- Question 1
    (1, 'Do majeur',  1, 1), (1, 'Sol majeur', 0, 2), (1, 'Re mineur',  0, 3), (1, 'La mineur',  0, 4),
    -- Question 2
    (2, '5 notes',    0, 1), (2, '7 notes',    1, 2), (2, '6 notes',    0, 3), (2, '8 notes',    0, 4),
    -- Question 3
    (3, 'Do majeur',  0, 1), (3, 'Sol mineur', 1, 2), (3, 'Sol majeur', 0, 3), (3, 'Re majeur',  0, 4),
    -- Question 4
    (4, 'Tierce majeure', 1, 1), (4, 'Tierce mineure', 0, 2), (4, 'Quarte juste', 0, 3), (4, 'Quinte juste', 0, 4),
    -- Question 5
    (5, 'Oui, un diese', 0, 1), (5, 'Oui, un bemol', 0, 2), (5, 'Non, aucune', 1, 3), (5, 'Oui, deux dieses', 0, 4);

INSERT INTO progression (id_utilisateur, id_fiche) VALUES
    (1, 1), (1, 2), (1, 3),
    (2, 1), (2, 2),
    (3, 1);

INSERT INTO resultats (id_utilisateur, id_fiche, score, points_gagnes, date_passage) VALUES
    (1, 1, 4, 16, '2026-05-15 14:32:00'),
    (1, 2, 3, 12, '2026-05-16 10:15:00'),
    (1, 3, 5, 20, '2026-05-17 16:45:00'),
    (2, 1, 5, 20, '2026-05-15 18:00:00'),
    (2, 2, 4, 16, '2026-05-18 11:20:00'),
    (3, 1, 4, 16, '2026-05-19 09:30:00');
