TRUNCATE TABLE role;
TRUNCATE TABLE user;
TRUNCATE TABLE user_role;
TRUNCATE TABLE lang;
TRUNCATE TABLE dir;

INSERT INTO role (name) VALUES ('REGISTERED');
INSERT INTO role (name) VALUES ('ADMIN');

-- CREO AMMINISTRATORI DEL SITO
INSERT INTO user (email, password, full_name, last_login, ins_date_time, last_upd_date_time) VALUES
('epifab@gmail.com', 'bed128365216c019988915ed3add75fb', 'epifab', NULL, NOW(), NOW());

-- AGGIUNGO AL GRUPPO DI AMMINISTRATORI
INSERT INTO user_role (user_id, role_id) VALUES
(1,1),
(1,2);

INSERT INTO lang (id, name) VALUES 
('de', 'German'),
('en', 'English'),
('it', 'Italian'),
('fr', 'French');

INSERT INTO dir (path) VALUES ('data/');