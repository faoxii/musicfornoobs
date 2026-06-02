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
    ('Gammes',      '#D4A52A'),
    ('Accords',     '#E07A6E'),
    ('Intervalles', '#3DB5C9');

-- Mots de passe : tous les utilisateurs de test ont "motdepasse123"
INSERT INTO utilisateurs (login, email, mot_de_passe, role, score_total) VALUES
    ('Lucas_L',    'lucas@example.com',    '$2y$10$qNZSIozhQ1P7AFVbodPy6.eLXXyjTE.myNmQt6NjEFSuYKPuXTDjq', 'admin',        120),
    ('Mathis_B',   'mathis@example.com',   '$2y$10$qNZSIozhQ1P7AFVbodPy6.eLXXyjTE.myNmQt6NjEFSuYKPuXTDjq', 'admin',        104),
    ('thomas.b',   'thomas@example.com',   '$2y$10$qNZSIozhQ1P7AFVbodPy6.eLXXyjTE.myNmQt6NjEFSuYKPuXTDjq', 'utilisateur',  88),
    ('noemie_22',  'noemie@example.com',   '$2y$10$qNZSIozhQ1P7AFVbodPy6.eLXXyjTE.myNmQt6NjEFSuYKPuXTDjq', 'utilisateur',  64);

INSERT INTO fiches (titre, slug, id_categorie, niveau, contenu, chemin_audio) VALUES
    ('La gamme de Do majeur', 'gamme-do-majeur', 1, 'Debutant',
     '## Qu\'est-ce qu\'une gamme majeure ?\n\nUne gamme est une suite ordonnee de notes espacees selon un schema precis de tons (T) et demi-tons (DT). La gamme de Do majeur est la plus connue car elle ne contient aucune alteration (ni diese, ni bemol).\n\n## Schema des intervalles\n\nLa gamme majeure suit toujours ce schema :\n\n**T - T - DT - T - T - T - DT**\n\n## Les 7 notes de Do majeur\n\n- **Do** — fondamentale\n- **Re** — ton au-dessus\n- **Mi** — ton au-dessus\n- **Fa** — demi-ton au-dessus\n- **Sol** — ton au-dessus\n- **La** — ton au-dessus\n- **Si** — ton au-dessus\n- *(Do)* — demi-ton, retour a l octave\n\n## Pourquoi c\'est important ?\n\nLa gamme de Do majeur est le point de depart de toute la theorie musicale occidentale. Comprendre sa structure permet ensuite de construire n\'importe quelle autre gamme majeure en appliquant le meme schema depuis une autre note.',
     'assets/audio/fiches/gamme-do-majeur.mp3'),
    ('La gamme mineure naturelle', 'gamme-mineure-naturelle', 1, 'Intermediaire',
     '## Qu\'est-ce que la gamme mineure naturelle ?\n\nLa gamme mineure naturelle est une gamme a 7 notes qui se distingue de la gamme majeure par son schema d\'intervalles different. Elle donne une couleur plus sombre, souvent associee a des emotions tristes ou melancoliques.\n\n## Schema des intervalles\n\n**T - DT - T - T - DT - T - T**\n\nAttention : contrairement a la gamme majeure, le premier demi-ton arrive des la 2eme position.\n\n## Les 7 notes de La mineur naturel\n\n- **La** — fondamentale\n- **Si** — ton au-dessus\n- **Do** — demi-ton au-dessus\n- **Re** — ton au-dessus\n- **Mi** — ton au-dessus\n- **Fa** — demi-ton au-dessus\n- **Sol** — ton au-dessus\n- *(La)* — ton, retour a l octave\n\n## Gamme relative\n\nLa mineur naturel est la **gamme relative** de Do majeur : elles partagent exactement les memes notes, mais ne commencent pas sur la meme fondamentale. Chaque gamme majeure possede sa relative mineure, situee une tierce mineure en dessous.',
     'assets/audio/fiches/gamme-mineure-naturelle.mp3'),
    ('Les intervalles : tierce et sixte', 'intervalles-tierce-sixte', 3, 'Debutant',
     '## Qu\'est-ce qu\'un intervalle ?\n\nUn intervalle est la distance entre deux notes. On la mesure en **demi-tons**. Connaitre les intervalles permet de comprendre comment sont construits les accords et les gammes.\n\n## La tierce\n\nLa tierce est l\'intervalle de base de la construction des accords.\n\n- **Tierce mineure** — 3 demi-tons — son sombre\n- **Tierce majeure** — 4 demi-tons — son lumineux\n\nExemples de tierces majeures :\n\n- **Do → Mi** (4 demi-tons)\n- **Sol → Si** (4 demi-tons)\n- **Fa → La** (4 demi-tons)\n\nExemples de tierces mineures :\n\n- **La → Do** (3 demi-tons)\n- **Re → Fa** (3 demi-tons)\n- **Mi → Sol** (3 demi-tons)\n\n## La sixte\n\nLa sixte est l\'intervalle inverse de la tierce : on dit qu\'ils sont **complementaires**.\n\n- **Sixte mineure** — 8 demi-tons\n- **Sixte majeure** — 9 demi-tons\n\nExemple : Do → La = sixte majeure (9 demi-tons)\n\n## Astuce\n\nPour retenir : **tierce majeure + sixte mineure = octave (12 demi-tons)**. Les intervalles complementaires se completent toujours pour former une octave.',
     NULL),
    ('Les intervalles : quarte, quinte et octave', 'intervalles-quarte-quinte-octave', 3, 'Intermediaire',
     '## La quarte juste\n\nLa quarte juste separe deux notes de **5 demi-tons**. C\'est un intervalle tres stable, souvent utilise dans les basses et les cadences.\n\nExemples :\n\n- **Do → Fa** (5 demi-tons)\n- **Sol → Do** (5 demi-tons)\n- **Re → Sol** (5 demi-tons)\n\n## La quinte juste\n\nLa quinte juste separe deux notes de **7 demi-tons**. C\'est l\'intervalle le plus stable apres l\'octave, et il forme la base de la construction des accords parfaits.\n\nExemples :\n\n- **Do → Sol** (7 demi-tons)\n- **Re → La** (7 demi-tons)\n- **Mi → Si** (7 demi-tons)\n\n## Le triton\n\nLe triton est un intervalle particulier de **6 demi-tons**, exactement a mi-chemin entre la quarte et la quinte. Il sonne tres instable et dissonant.\n\n- **Do → Fa#** = triton\n- **Si → Fa** = triton\n\n## L\'octave\n\nL\'octave est l\'intervalle de **12 demi-tons**. Une note et son octave portent le meme nom mais sont percues comme la meme note a une hauteur differente. C\'est l\'intervalle de reference pour mesurer tous les autres.',
     NULL),
    ('Les accords parfaits mineurs', 'accords-parfaits-mineurs', 2, 'Intermediaire',
     '## Qu\'est-ce qu\'un accord mineur ?\n\nL\'accord parfait mineur est compose de **3 notes** comme l\'accord majeur, mais avec une tierce mineure (3 demi-tons) au lieu d\'une tierce majeure (4 demi-tons). Ce changement d\'un seul demi-ton suffit a donner une couleur completement differente, plus sombre et melancolique.\n\n## Les 3 composantes\n\n- **La fondamentale** — note de base\n- **La tierce mineure** — 3 demi-tons au-dessus\n- **La quinte juste** — 7 demi-tons au-dessus\n\n## Exemples d\'accords mineurs\n\n- **La mineur** — La + Do + Mi\n- **Re mineur** — Re + Fa + La\n- **Mi mineur** — Mi + Sol + Si\n- **Sol mineur** — Sol + Sib + Re\n\n## Majeur vs Mineur : le tableau comparatif\n\n- **Do majeur** : Do + Mi + Sol (tierce = 4 demi-tons)\n- **Do mineur** : Do + Mib + Sol (tierce = 3 demi-tons)\n\nUn seul demi-ton de difference sur la tierce change totalement l\'ambiance de l\'accord.\n\n## Utilisations courantes\n\nLes accords mineurs sont tres utilises dans la musique classique, le jazz et le rock pour creer des emotions plus intenses ou dramatiques.',
     'assets/audio/fiches/accords-mineurs.mp3'),
    ('La gamme pentatonique mineure', 'gamme-pentatonique-mineure', 1, 'Intermediaire',
     '## Qu\'est-ce que la gamme pentatonique ?\n\nLa gamme pentatonique est une gamme a **5 notes** (penta = cinq). C\'est l\'une des gammes les plus utilisees dans le monde, presente dans le blues, le rock, le jazz et les musiques traditionnelles asiatiques et africaines.\n\n## Les 5 notes de La pentatonique mineure\n\n- **La** — fondamentale\n- **Do** — tierce mineure (3 demi-tons)\n- **Re** — quarte juste (5 demi-tons)\n- **Mi** — quinte juste (7 demi-tons)\n- **Sol** — septieme mineure (10 demi-tons)\n- *(La)* — octave\n\n## Schema des intervalles\n\n**3 - 2 - 2 - 3 - 2** (en demi-tons)\n\n## Pourquoi 5 notes et pas 7 ?\n\nLa gamme mineure naturelle a 7 notes. La pentatonique retire la 2eme et la 6eme note, ce sont les notes qui creent le plus de dissonances. Ce qui reste sonne bien dans presque tous les contextes — c\'est pour ca qu\'elle est si populaire chez les guitaristes debutants.\n\n## Utilisations\n\n- **Blues** : base de presque tous les solos\n- **Rock** : riffs et solos de guitare\n- **Musiques du monde** : gamelan, musique chinoise, musique celtique',
     'assets/audio/fiches/gamme-pentatonique.mp3'),
    ('Les accords parfaits majeurs', 'accords-parfaits-majeurs', 2, 'Debutant',
     '## Qu\'est-ce qu\'un accord parfait ?\n\nUn accord est un ensemble de notes jouees simultanement. L\'accord parfait majeur est la forme la plus simple et la plus courante : il est compose de **3 notes** superposees selon un schema precis.\n\n## Les 3 composantes\n\n- **La fondamentale** — note de base qui donne son nom a l\'accord\n- **La tierce majeure** — 4 demi-tons au-dessus de la fondamentale\n- **La quinte juste** — 7 demi-tons au-dessus de la fondamentale\n\n## Exemples d\'accords majeurs\n\n- **Do majeur** — Do + Mi + Sol\n- **Sol majeur** — Sol + Si + Re\n- **Fa majeur** — Fa + La + Do\n- **Re majeur** — Re + Fa# + La\n\n## Majeur vs Mineur\n\nLa seule difference entre un accord majeur et un accord mineur est la tierce :\n\n- **Accord majeur** : tierce majeure (4 demi-tons) → son lumineux\n- **Accord mineur** : tierce mineure (3 demi-tons) → son sombre\n\nExemple : Do majeur = Do + Mi + Sol / Do mineur = Do + Mib + Sol',
     NULL);

INSERT INTO quiz_questions (id_fiche, type, enonce, chemin_audio, ordre) VALUES
    -- Fiche 1 : La gamme de Do majeur (questions 1-5)
    (1, 'audio', 'Quelle gamme entendez-vous ?',                              'assets/audio/quiz/do-majeur-ascendant.mp3', 1),
    (1, 'texte', 'Combien de notes contient une gamme majeure ?',             NULL, 2),
    (1, 'audio', 'Quel accord est joue ?',                                    'assets/audio/quiz/accord-sol-mineur.mp3',   3),
    (1, 'texte', 'Quel est l intervalle entre Do et Mi en Do majeur ?',       NULL, 4),
    (1, 'texte', 'La gamme de Do majeur contient-elle des alterations ?',     NULL, 5),
    -- Fiche 2 : La gamme mineure naturelle (questions 6-10)
    (2, 'texte', 'Combien de notes contient une gamme mineure naturelle ?',   NULL, 1),
    (2, 'texte', 'Quelle est la premiere note de la gamme de La mineur ?',    NULL, 2),
    (2, 'texte', 'Quel schema suit la gamme mineure naturelle ?',             NULL, 3),
    (2, 'texte', 'Quelle gamme mineure est relative de Do majeur ?',          NULL, 4),
    (2, 'texte', 'En La mineur naturel, quelle est la 6eme note ?',          NULL, 5),
    -- Fiche 3 : Les intervalles tierce et sixte (questions 11-15)
    (3, 'texte', 'Combien de demi-tons separe une tierce majeure ?',          NULL, 1),
    (3, 'texte', 'Combien de demi-tons separe une tierce mineure ?',          NULL, 2),
    (3, 'texte', 'Quelle est la tierce majeure de Do ?',                      NULL, 3),
    (3, 'texte', 'Combien de demi-tons separe une sixte majeure ?',           NULL, 4),
    (3, 'texte', 'Quelle est la tierce mineure de La ?',                      NULL, 5),
    -- Fiche 4 : Les intervalles quarte, quinte et octave (questions 16-20)
    (4, 'texte', 'Combien de demi-tons separe une quarte juste ?',            NULL, 1),
    (4, 'texte', 'Combien de demi-tons separe une quinte juste ?',            NULL, 2),
    (4, 'texte', 'Quelle est la quinte juste de Do ?',                        NULL, 3),
    (4, 'texte', 'Combien de demi-tons separe un triton ?',                   NULL, 4),
    (4, 'texte', 'Combien de demi-tons separe une octave ?',                  NULL, 5),
    -- Fiche 5 : Les accords parfaits mineurs (questions 21-25)
    (5, 'audio', 'Quel accord entendez-vous ?',                               'assets/audio/quiz/accord-la-mineur.mp3', 1),
    (5, 'texte', 'Combien de demi-tons forme la tierce mineure ?',            NULL, 2),
    (5, 'audio', 'Quel accord entendez-vous ?',                               'assets/audio/quiz/accord-re-mineur.mp3', 3),
    (5, 'texte', 'Quelles notes forment l accord de La mineur ?',             NULL, 4),
    (5, 'texte', 'Quelle note differe entre Do majeur et Do mineur ?',        NULL, 5),
    -- Fiche 6 : La gamme pentatonique mineure (questions 26-30)
    (6, 'audio', 'Quelle gamme entendez-vous ?',                              'assets/audio/quiz/gamme-pentatonique.mp3', 1),
    (6, 'texte', 'Combien de notes contient la gamme pentatonique ?',         NULL, 2),
    (6, 'texte', 'Quelles notes forment la pentatonique de La ?',             NULL, 3),
    (6, 'texte', 'Quel intervalle separe la 1ere et la 2eme note en pentatonique mineure ?', NULL, 4),
    (6, 'texte', 'Dans quel style musical utilise-t-on surtout la pentatonique ?', NULL, 5),
    -- Fiche 7 : Les accords parfaits majeurs (questions 31-35)
    (7, 'audio', 'Quel accord entendez-vous ?',                               'assets/audio/quiz/accord-do-majeur.mp3', 1),
    (7, 'texte', 'Combien de notes compose un accord parfait ?',              NULL, 2),
    (7, 'texte', 'Quelles notes forment l accord de Do majeur ?',             NULL, 3),
    (7, 'texte', 'Comment appelle-t-on la premiere note d un accord ?',       NULL, 4),
    (7, 'texte', 'Quelle est la quinte juste de l accord de Sol majeur ?',    NULL, 5);

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
    (5, 'Oui, un diese', 0, 1), (5, 'Oui, un bemol', 0, 2), (5, 'Non, aucune', 1, 3), (5, 'Oui, deux dieses', 0, 4),
    -- Question 6
    (6, '5 notes', 0, 1), (6, '6 notes', 0, 2), (6, '7 notes', 1, 3), (6, '8 notes', 0, 4),
    -- Question 7
    (7, 'Do', 0, 1), (7, 'Sol', 0, 2), (7, 'La', 1, 3), (7, 'Mi', 0, 4),
    -- Question 8
    (8, 'T-T-DT-T-T-T-DT', 0, 1), (8, 'T-DT-T-T-DT-T-T', 1, 2), (8, 'DT-T-T-DT-T-T-T', 0, 3), (8, 'T-T-T-DT-T-T-DT', 0, 4),
    -- Question 9
    (9, 'Sol mineur', 0, 1), (9, 'Re mineur', 0, 2), (9, 'La mineur', 1, 3), (9, 'Mi mineur', 0, 4),
    -- Question 10
    (10, 'Sol', 0, 1), (10, 'Mi', 0, 2), (10, 'Fa', 1, 3), (10, 'Re', 0, 4),
    -- Question 11 (fiche 4 : tierce majeure = 4 demi-tons)
    (11, '2 demi-tons', 0, 1), (11, '3 demi-tons', 0, 2), (11, '4 demi-tons', 1, 3), (11, '5 demi-tons', 0, 4),
    -- Question 12 (fiche 4 : tierce mineure = 3 demi-tons)
    (12, '2 demi-tons', 0, 1), (12, '3 demi-tons', 1, 2), (12, '4 demi-tons', 0, 3), (12, '5 demi-tons', 0, 4),
    -- Question 13 (fiche 4 : tierce majeure de Do = Mi)
    (13, 'Re', 0, 1), (13, 'Fa', 0, 2), (13, 'Mi', 1, 3), (13, 'Sol', 0, 4),
    -- Question 14 (fiche 4 : sixte majeure = 9 demi-tons)
    (14, '7 demi-tons', 0, 1), (14, '8 demi-tons', 0, 2), (14, '9 demi-tons', 1, 3), (14, '10 demi-tons', 0, 4),
    -- Question 15 (fiche 4 : tierce mineure de La = Do)
    (15, 'Si', 0, 1), (15, 'Do', 1, 2), (15, 'Re', 0, 3), (15, 'Mib', 0, 4),
    -- Question 16 (fiche 5 : quarte juste = 5 demi-tons)
    (16, '3 demi-tons', 0, 1), (16, '4 demi-tons', 0, 2), (16, '5 demi-tons', 1, 3), (16, '6 demi-tons', 0, 4),
    -- Question 17 (fiche 5 : quinte juste = 7 demi-tons)
    (17, '5 demi-tons', 0, 1), (17, '6 demi-tons', 0, 2), (17, '7 demi-tons', 1, 3), (17, '8 demi-tons', 0, 4),
    -- Question 18 (fiche 5 : quinte juste de Do = Sol)
    (18, 'Fa', 0, 1), (18, 'La', 0, 2), (18, 'Sol', 1, 3), (18, 'Si', 0, 4),
    -- Question 19 (fiche 5 : triton = 6 demi-tons)
    (19, '4 demi-tons', 0, 1), (19, '5 demi-tons', 0, 2), (19, '6 demi-tons', 1, 3), (19, '7 demi-tons', 0, 4),
    -- Question 20 (fiche 5 : octave = 12 demi-tons)
    (20, '8 demi-tons', 0, 1), (20, '10 demi-tons', 0, 2), (20, '11 demi-tons', 0, 3), (20, '12 demi-tons', 1, 4),
    -- Question 21 (fiche 5 : accord La mineur)
    (21, 'Do majeur', 0, 1), (21, 'Sol majeur', 0, 2), (21, 'La mineur', 1, 3), (21, 'Re mineur', 0, 4),
    -- Question 22 (fiche 5 : tierce mineure = 3 demi-tons)
    (22, '2 demi-tons', 0, 1), (22, '3 demi-tons', 1, 2), (22, '4 demi-tons', 0, 3), (22, '5 demi-tons', 0, 4),
    -- Question 23 (fiche 5 : accord Re mineur)
    (23, 'Do majeur', 0, 1), (23, 'Re mineur', 1, 2), (23, 'Re majeur', 0, 3), (23, 'Sol mineur', 0, 4),
    -- Question 24 (fiche 5 : notes de La mineur = La Do Mi)
    (24, 'La, Si, Mi', 0, 1), (24, 'La, Do, Mi', 1, 2), (24, 'La, Do, Re', 0, 3), (24, 'La, Mib, Sol', 0, 4),
    -- Question 25 (fiche 5 : Do majeur vs Do mineur = la tierce)
    (25, 'La fondamentale', 0, 1), (25, 'La quinte', 0, 2), (25, 'La tierce', 1, 3), (25, 'L octave', 0, 4),
    -- Question 26 (fiche 6 : gamme pentatonique)
    (26, 'Gamme majeure', 0, 1), (26, 'Gamme mineure naturelle', 0, 2), (26, 'Gamme pentatonique', 1, 3), (26, 'Gamme de Do majeur', 0, 4),
    -- Question 27 (fiche 6 : 5 notes)
    (27, '4 notes', 0, 1), (27, '5 notes', 1, 2), (27, '6 notes', 0, 3), (27, '7 notes', 0, 4),
    -- Question 28 (fiche 6 : notes pentatonique La = La Do Re Mi Sol)
    (28, 'La, Si, Re, Mi, Sol', 0, 1), (28, 'La, Do, Re, Mi, Sol', 1, 2), (28, 'La, Do, Re, Fa, Sol', 0, 3), (28, 'La, Do, Mi, Sol, Si', 0, 4),
    -- Question 29 (fiche 6 : 1er intervalle pentatonique mineure = 3 demi-tons)
    (29, '1 demi-ton', 0, 1), (29, '2 demi-tons', 0, 2), (29, '3 demi-tons', 1, 3), (29, '4 demi-tons', 0, 4),
    -- Question 30 (fiche 6 : blues et rock)
    (30, 'Musique classique', 0, 1), (30, 'Blues et rock', 1, 2), (30, 'Opera', 0, 3), (30, 'Musique baroque', 0, 4),
    -- Question 31 (fiche 7 : accord Do majeur)
    (31, 'Sol majeur', 0, 1), (31, 'Fa majeur', 0, 2), (31, 'Do majeur', 1, 3), (31, 'Re majeur', 0, 4),
    -- Question 32 (fiche 7 : 3 notes)
    (32, '2 notes', 0, 1), (32, '3 notes', 1, 2), (32, '4 notes', 0, 3), (32, '5 notes', 0, 4),
    -- Question 33 (fiche 7 : Do Mi Sol)
    (33, 'Do, Re, Sol', 0, 1), (33, 'Do, Mi, Sol', 1, 2), (33, 'Do, Fa, La', 0, 3), (33, 'Do, Sol, Si', 0, 4),
    -- Question 34 (fiche 7 : fondamentale)
    (34, 'La tierce', 0, 1), (34, 'La quinte', 0, 2), (34, 'La fondamentale', 1, 3), (34, 'L octave', 0, 4),
    -- Question 35 (fiche 7 : quinte de Sol = Re)
    (35, 'Do', 0, 1), (35, 'La', 0, 2), (35, 'Re', 1, 3), (35, 'Mi', 0, 4);

INSERT INTO progression (id_utilisateur, id_fiche) VALUES
    (1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7),
    (2, 1), (2, 2), (2, 3), (2, 5),
    (3, 1), (3, 3), (3, 6),
    (4, 1), (4, 5);

INSERT INTO resultats (id_utilisateur, id_fiche, score, points_gagnes, date_passage) VALUES
    (1, 1, 4, 16, '2026-05-15 14:32:00'),
    (1, 2, 3, 12, '2026-05-16 10:15:00'),
    (1, 3, 5, 20, '2026-05-17 16:45:00'),
    (1, 5, 4, 16, '2026-05-18 09:00:00'),
    (1, 6, 5, 20, '2026-05-19 11:00:00'),
    (2, 1, 5, 20, '2026-05-15 18:00:00'),
    (2, 2, 4, 16, '2026-05-18 11:20:00'),
    (3, 1, 4, 16, '2026-05-19 09:30:00');
