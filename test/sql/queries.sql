TRUNCATE TABLE xmca_comment;
TRUNCATE TABLE xmca_component;
TRUNCATE TABLE xmca_content;
TRUNCATE TABLE xmca_content_style;
TRUNCATE TABLE xmca_content_tag;
TRUNCATE TABLE xmca_content_text;
TRUNCATE TABLE xmca_dir;
TRUNCATE TABLE xmca_email;
TRUNCATE TABLE xmca_file;
TRUNCATE TABLE xmca_group;
TRUNCATE TABLE xmca_group_component;
TRUNCATE TABLE xmca_image;
TRUNCATE TABLE xmca_lang;
TRUNCATE TABLE xmca_log;
TRUNCATE TABLE xmca_page;
TRUNCATE TABLE xmca_page_style;
TRUNCATE TABLE xmca_page_tag;
TRUNCATE TABLE xmca_page_text;
TRUNCATE TABLE xmca_record_mode;
TRUNCATE TABLE xmca_record_mode_log;
TRUNCATE TABLE xmca_tag;
TRUNCATE TABLE xmca_user;
TRUNCATE TABLE xmca_user_group;

INSERT INTO xmca_component (name) VALUES ('EditPage');
INSERT INTO xmca_component (name) VALUES ('DeletePage');
INSERT INTO xmca_component (name) VALUES ('EditContent');
INSERT INTO xmca_component (name) VALUES ('DeleteContent');
INSERT INTO xmca_component (name) VALUES ('EditComment');
INSERT INTO xmca_component (name) VALUES ('DeleteComment');
INSERT INTO xmca_component (name) VALUES ('EditGroup');
INSERT INTO xmca_component (name) VALUES ('DeleteGroup');
INSERT INTO xmca_component (name) VALUES ('EditUser');
INSERT INTO xmca_component (name) VALUES ('DeleteUser');
INSERT INTO xmca_component (name) VALUES ('Users');
INSERT INTO xmca_component (name) VALUES ('ToggleUserGroup');
INSERT INTO xmca_component (name) VALUES ('ErrorLog');

INSERT INTO xmca_group (name) VALUES ('ADMINS');
INSERT INTO xmca_group (name) VALUES ('GUESTS');

INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 1);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 2);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 3);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 4);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 5);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 6);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 7);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 8);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 9);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 10);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 11);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 12);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (1, 13);
-- Commenti per i guests
INSERT INTO xmca_group_component (group_id, component_id) VALUES (2, 5);
INSERT INTO xmca_group_component (group_id, component_id) VALUES (2, 6);

-- CREO AMMINISTRATORI DEL SITO
INSERT INTO xmca_user (email, password, full_name, last_login, ins_date_time, last_upd_date_time) VALUES
('epifab@gmail.com', 'bed128365216c019988915ed3add75fb', 'Fabio Epifani', NULL, NOW(), NOW());

-- AGGIUNGO AL GRUPPO DI AMMINISTRATORI
INSERT INTO xmca_user_group (user_id, group_id) VALUES
(1,1);

-- AGGIUNGO I LOG INIZIALI DELLE SEZIONI
INSERT INTO xmca_record_mode_log (record_mode_id, upd_date_time, modifier_id) VALUES
(1,NOW(),1);

INSERT INTO xmca_lang (id, description) VALUES 
('de', 'German'),
('en', 'English'),
('it', 'Italian'),
('fr', 'French');

INSERT INTO xmca_dir (path) VALUES ('contents_media/image/');
INSERT INTO xmca_dir (path) VALUES ('contents_media/download/');
INSERT INTO xmca_dir (path) VALUES ('contents_media/audio/');
INSERT INTO xmca_dir (path) VALUES ('contents_media/video/');

INSERT INTO xmca_page_style (code, description, page_template) VALUES ('1', '1 col layout', 'layout/Outline1');
INSERT INTO xmca_page_style (code, description, page_template) VALUES ('2L', '2 cols layout (main on the left)', 'layout/Outline2L');
INSERT INTO xmca_page_style (code, description, page_template) VALUES ('2R', '2 cols layout (main on the right)', 'layout/Outline2R');
INSERT INTO xmca_page_style (code, description, page_template) VALUES ('3', '3 cols layout', 'layout/Outline3');
INSERT INTO xmca_page_style (code, description, page_template) VALUES ('3L', '3 cols layout (main on the left)', 'layout/Outline3L');
INSERT INTO xmca_page_style (code, description, page_template) VALUES ('3R', '3 cols layout (main on the right)', 'layout/Outline3R');

INSERT INTO xmca_content_style (code, description, content_template, preview_template) VALUES ('STANDARD', 'Standard content', 'StdContent', 'StdContentPreview');