INSERT INTO role (name) VALUES ('REGISTERED');
INSERT INTO role (name) VALUES ('ADMIN');

-- CREO AMMINISTRATORI DEL SITO
INSERT INTO user (email, password, full_name, last_login, ins_date_time, last_upd_date_time) VALUES
('epifab@gmail.com', 'bed128365216c019988915ed3add75fb', 'Fabio Epifani', NULL, NOW(), NOW());

-- AGGIUNGO AL GRUPPO DI AMMINISTRATORI
INSERT INTO user_role (user_id, group_id) VALUES
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