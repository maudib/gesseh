CREATE TABLE copisim_simulation (id BIGINT AUTO_INCREMENT, etudiant BIGINT NOT NULL, poste BIGINT, reste SMALLINT, absent TINYINT(1) NOT NULL, INDEX etudiant_idx (etudiant), INDEX poste_idx (poste), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_choix (id BIGINT AUTO_INCREMENT, etudiant BIGINT NOT NULL, poste BIGINT NOT NULL, ordre SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX etudiant_idx (etudiant), INDEX poste_idx (poste), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_critere (id BIGINT AUTO_INCREMENT, form BIGINT NOT NULL, titre VARCHAR(100) NOT NULL, type VARCHAR(10) NOT NULL, ratio BIGINT, ordre SMALLINT NOT NULL, INDEX form_idx (form), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_etudiant (id BIGINT AUTO_INCREMENT, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, promo_id BIGINT NOT NULL, classement BIGINT NOT NULL, email VARCHAR(50) NOT NULL, token_mail VARCHAR(50) NOT NULL, tel VARCHAR(14), naissance DATE, utilisateur BIGINT, updated_at DATETIME NOT NULL, INDEX promo_id_idx (promo_id), INDEX utilisateur_idx (utilisateur), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_eval (id BIGINT AUTO_INCREMENT, stage_id BIGINT NOT NULL, critere_id BIGINT NOT NULL, valeur VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX stage_id_idx (stage_id), INDEX critere_id_idx (critere_id), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_filiere (id BIGINT AUTO_INCREMENT, titre VARCHAR(100) NOT NULL, form BIGINT, INDEX form_idx (form), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_form_eval (id BIGINT AUTO_INCREMENT, titre VARCHAR(100) NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_hopital (id BIGINT AUTO_INCREMENT, nom VARCHAR(100) NOT NULL UNIQUE, adresse VARCHAR(255) NOT NULL, telephone VARCHAR(14), web VARCHAR(255), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_page (id BIGINT AUTO_INCREMENT, contenu LONGBLOB, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_param (id BIGINT AUTO_INCREMENT, titre VARCHAR(255) NOT NULL, valeur VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_periode (id BIGINT AUTO_INCREMENT, titre VARCHAR(20) NOT NULL, debut DATE NOT NULL, fin DATE NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_promo (id BIGINT AUTO_INCREMENT, titre VARCHAR(100) NOT NULL, ordre SMALLINT NOT NULL, active TINYINT(1) DEFAULT '1' NOT NULL, form BIGINT, INDEX form_idx (form), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_referent (id BIGINT AUTO_INCREMENT, utilisateur BIGINT NOT NULL, promo BIGINT NOT NULL, tel VARCHAR(10), divers TEXT, INDEX promo_idx (promo), INDEX utilisateur_idx (utilisateur), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_simulation (id BIGINT AUTO_INCREMENT, etudiant BIGINT NOT NULL, poste BIGINT, reste SMALLINT, absent TINYINT(1) NOT NULL, INDEX etudiant_idx (etudiant), INDEX poste_idx (poste), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_stage (id BIGINT AUTO_INCREMENT, terrain_id BIGINT NOT NULL, periode_id BIGINT NOT NULL, etudiant_id BIGINT NOT NULL, form BIGINT NOT NULL, is_active TINYINT(1) DEFAULT '1' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX terrain_id_idx (terrain_id), INDEX periode_id_idx (periode_id), INDEX etudiant_id_idx (etudiant_id), INDEX form_idx (form), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE gesseh_terrain (id BIGINT AUTO_INCREMENT, hopital_id BIGINT NOT NULL, titre VARCHAR(100) NOT NULL, filiere BIGINT NOT NULL, patron VARCHAR(50) NOT NULL, localisation VARCHAR(255), gardes_lieu VARCHAR(100), gardes_horaires VARCHAR(100), astreintes_horaires VARCHAR(100), total MEDIUMINT NOT NULL, page BIGINT, is_active TINYINT(1) DEFAULT '1' NOT NULL, INDEX hopital_id_idx (hopital_id), INDEX filiere_idx (filiere), INDEX page_idx (page), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE cs_setting (id INT AUTO_INCREMENT, name VARCHAR(255) NOT NULL UNIQUE, type VARCHAR(255) DEFAULT 'input' NOT NULL, widget_options LONGTEXT, value LONGTEXT, setting_group VARCHAR(255) DEFAULT NULL, setting_default LONGTEXT DEFAULT NULL, slug VARCHAR(255), UNIQUE INDEX cs_setting_sluggable_idx (slug), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE sf_guard_forgot_password (id BIGINT AUTO_INCREMENT, user_id BIGINT NOT NULL, unique_key VARCHAR(255), expires_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX user_id_idx (user_id), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE sf_guard_group (id BIGINT AUTO_INCREMENT, name VARCHAR(255) UNIQUE, description TEXT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE sf_guard_group_permission (group_id BIGINT, permission_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(group_id, permission_id)) ENGINE = INNODB;
CREATE TABLE sf_guard_permission (id BIGINT AUTO_INCREMENT, name VARCHAR(255) UNIQUE, description TEXT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE sf_guard_remember_key (id BIGINT AUTO_INCREMENT, user_id BIGINT, remember_key VARCHAR(32), ip_address VARCHAR(50), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX user_id_idx (user_id), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE sf_guard_user (id BIGINT AUTO_INCREMENT, first_name VARCHAR(255), last_name VARCHAR(255), email_address VARCHAR(255) NOT NULL UNIQUE, username VARCHAR(128) NOT NULL UNIQUE, algorithm VARCHAR(128) DEFAULT 'sha1' NOT NULL, salt VARCHAR(128), password VARCHAR(128), is_active TINYINT(1) DEFAULT '1', is_super_admin TINYINT(1) DEFAULT '0', last_login DATETIME, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX is_active_idx_idx (is_active), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE sf_guard_user_group (user_id BIGINT, group_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(user_id, group_id)) ENGINE = INNODB;
CREATE TABLE sf_guard_user_permission (user_id BIGINT, permission_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(user_id, permission_id)) ENGINE = INNODB;
ALTER TABLE copisim_simulation ADD CONSTRAINT copisim_simulation_poste_gesseh_terrain_id FOREIGN KEY (poste) REFERENCES gesseh_terrain(id);
ALTER TABLE copisim_simulation ADD CONSTRAINT copisim_simulation_etudiant_gesseh_etudiant_id FOREIGN KEY (etudiant) REFERENCES gesseh_etudiant(id) ON DELETE CASCADE;
ALTER TABLE gesseh_choix ADD CONSTRAINT gesseh_choix_poste_gesseh_terrain_id FOREIGN KEY (poste) REFERENCES gesseh_terrain(id) ON DELETE CASCADE;
ALTER TABLE gesseh_choix ADD CONSTRAINT gesseh_choix_etudiant_gesseh_etudiant_id FOREIGN KEY (etudiant) REFERENCES gesseh_etudiant(id) ON DELETE CASCADE;
ALTER TABLE gesseh_critere ADD CONSTRAINT gesseh_critere_form_gesseh_form_eval_id FOREIGN KEY (form) REFERENCES gesseh_form_eval(id) ON DELETE CASCADE;
ALTER TABLE gesseh_etudiant ADD CONSTRAINT gesseh_etudiant_utilisateur_sf_guard_user_id FOREIGN KEY (utilisateur) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE gesseh_etudiant ADD CONSTRAINT gesseh_etudiant_promo_id_gesseh_promo_id FOREIGN KEY (promo_id) REFERENCES gesseh_promo(id) ON DELETE CASCADE;
ALTER TABLE gesseh_eval ADD CONSTRAINT gesseh_eval_stage_id_gesseh_stage_id FOREIGN KEY (stage_id) REFERENCES gesseh_stage(id) ON DELETE CASCADE;
ALTER TABLE gesseh_eval ADD CONSTRAINT gesseh_eval_critere_id_gesseh_critere_id FOREIGN KEY (critere_id) REFERENCES gesseh_critere(id) ON DELETE CASCADE;
ALTER TABLE gesseh_filiere ADD CONSTRAINT gesseh_filiere_form_gesseh_form_eval_id FOREIGN KEY (form) REFERENCES gesseh_form_eval(id);
ALTER TABLE gesseh_promo ADD CONSTRAINT gesseh_promo_form_gesseh_form_eval_id FOREIGN KEY (form) REFERENCES gesseh_form_eval(id);
ALTER TABLE gesseh_referent ADD CONSTRAINT gesseh_referent_utilisateur_sf_guard_user_id FOREIGN KEY (utilisateur) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE gesseh_referent ADD CONSTRAINT gesseh_referent_promo_gesseh_promo_id FOREIGN KEY (promo) REFERENCES gesseh_promo(id) ON DELETE CASCADE;
ALTER TABLE gesseh_simulation ADD CONSTRAINT gesseh_simulation_poste_gesseh_terrain_id FOREIGN KEY (poste) REFERENCES gesseh_terrain(id);
ALTER TABLE gesseh_simulation ADD CONSTRAINT gesseh_simulation_etudiant_gesseh_etudiant_id FOREIGN KEY (etudiant) REFERENCES gesseh_etudiant(id) ON DELETE CASCADE;
ALTER TABLE gesseh_stage ADD CONSTRAINT gesseh_stage_terrain_id_gesseh_terrain_id FOREIGN KEY (terrain_id) REFERENCES gesseh_terrain(id) ON DELETE CASCADE;
ALTER TABLE gesseh_stage ADD CONSTRAINT gesseh_stage_periode_id_gesseh_periode_id FOREIGN KEY (periode_id) REFERENCES gesseh_periode(id) ON DELETE CASCADE;
ALTER TABLE gesseh_stage ADD CONSTRAINT gesseh_stage_form_gesseh_form_eval_id FOREIGN KEY (form) REFERENCES gesseh_form_eval(id);
ALTER TABLE gesseh_stage ADD CONSTRAINT gesseh_stage_etudiant_id_gesseh_etudiant_id FOREIGN KEY (etudiant_id) REFERENCES gesseh_etudiant(id) ON DELETE CASCADE;
ALTER TABLE gesseh_terrain ADD CONSTRAINT gesseh_terrain_page_gesseh_page_id FOREIGN KEY (page) REFERENCES gesseh_page(id);
ALTER TABLE gesseh_terrain ADD CONSTRAINT gesseh_terrain_hopital_id_gesseh_hopital_id FOREIGN KEY (hopital_id) REFERENCES gesseh_hopital(id) ON DELETE CASCADE;
ALTER TABLE gesseh_terrain ADD CONSTRAINT gesseh_terrain_filiere_gesseh_filiere_id FOREIGN KEY (filiere) REFERENCES gesseh_filiere(id);
ALTER TABLE sf_guard_forgot_password ADD CONSTRAINT sf_guard_forgot_password_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_group_permission ADD CONSTRAINT sf_guard_group_permission_permission_id_sf_guard_permission_id FOREIGN KEY (permission_id) REFERENCES sf_guard_permission(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_group_permission ADD CONSTRAINT sf_guard_group_permission_group_id_sf_guard_group_id FOREIGN KEY (group_id) REFERENCES sf_guard_group(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_remember_key ADD CONSTRAINT sf_guard_remember_key_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_user_group ADD CONSTRAINT sf_guard_user_group_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_user_group ADD CONSTRAINT sf_guard_user_group_group_id_sf_guard_group_id FOREIGN KEY (group_id) REFERENCES sf_guard_group(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_user_permission ADD CONSTRAINT sf_guard_user_permission_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_user_permission ADD CONSTRAINT sf_guard_user_permission_permission_id_sf_guard_permission_id FOREIGN KEY (permission_id) REFERENCES sf_guard_permission(id) ON DELETE CASCADE;
