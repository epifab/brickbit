CREATE DATABASE cider
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE cider;

-- CREATE TABLE config (
-- 	label VARCHAR(32),
-- 	int_val INT,
-- 	real_val DOUBLE,
-- 	date_val DATE,
-- 	datetime_val DATETIME,
-- 	time_val TIME,
-- 	string_s_val VARCHAR(32),
-- 	string_m_val VARCHAR(64),
-- 	string_l_val VARCHAR(128),
-- 	string_xl_val VARCHAR(256),
-- 	string_xxl_val TEXT,
-- 	PRIMARY KEY (label)
-- ) ENGINE=InnoDB;

-- CREATE TABLE email (
-- 	id INT AUTO_INCREMENT,
-- 	sent_date_time DATETIME,
-- 	ip_address VARCHAR(15),
-- 	user_id INT,
-- 	recipient_email VARCHAR(128),
-- 	recipient_name VARCHAR(128),
-- 	sender_email VARCHAR(128),
-- 	sender_name VARCHAR(128),
-- 	body TEXT,
-- 	subject VARCHAR(128),
-- 	PRIMARY KEY (id)
-- ) ENGINE=InnoDB;

CREATE TABLE module (
	id INT AUTO_INCREMENT,
	name VARCHAR(32),
	class VARCHAR(32),
	weight INT,
	PRIMARY KEY (id),
	UNIQUE KEY (name)
) ENGINE=InnoDB;

CREATE TABLE component (
	id INT AUTO_INCREMENT,
	module_id INT,
	name VARCHAR(32),
	class VARCHAR(32),
	PRIMARY KEY (id),
	UNIQUE KEY (module_id, name)
) ENGINE=InnoDB;

CREATE TABLE action (
	id INT AUTO_INCREMENT,
	component_id INT,
	name VARCHAR(32),
   url VARCHAR(128),
	PRIMARY KEY (id),
	UNIQUE KEY (component_id, name)
) ENGINE=InnoDB;

CREATE TABLE role (
	id INT AUTO_INCREMENT,
	name VARCHAR(32),
	PRIMARY KEY (id),
	UNIQUE KEY (name)
) ENGINE=InnoDB;

CREATE TABLE role_component (
	role_id INT,
	component_id INT,
	PRIMARY KEY (role_id, component_id)
) ENGINE=InnoDB;

CREATE TABLE role_action (
	role_id INT,
	action_id INT,
	PRIMARY KEY (role_id, action_id)
) ENGINE=InnoDB;

CREATE TABLE user (
	id INT AUTO_INCREMENT,
	email VARCHAR(128),
	password CHAR(32),
	full_name VARCHAR(128),
	last_login DATETIME,
	ins_date_time DATETIME,
	last_upd_date_time DATETIME,
	KEY (email),
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE user_role (
	user_id INT,
	role_id INT,
	PRIMARY KEY (user_id, role_id)
) ENGINE=InnoDB;

CREATE TABLE record_mode (
	id INT AUTO_INCREMENT,
	owner_id INT,
	role_id INT,
	read_mode INT,
	edit_mode INT,
	delete_mode INT,
	ins_date_time DATETIME,
	last_modifier_id INT,
	last_upd_date_time DATETIME,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE record_mode_log (
	id INT AUTO_INCREMENT,
	record_mode_id INT,
	upd_date_time DATETIME,
	modifier_id INT,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE log (
	id INT AUTO_INCREMENT,
	user_id INT,
	script_url VARCHAR(100),
	date_time_request DATETIME,
	ip_address VARCHAR(15),
	body TEXT,
	output TEXT,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE dir (
	id INT AUTO_INCREMENT,
	path VARCHAR(64),
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE file (
	id INT AUTO_INCREMENT,
	dir_id INT,
	name VARCHAR(32),
	size INT,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE image (
	id INT AUTO_INCREMENT,
	width1 INT,
	height1 INT,
	file1_id INT,
	width2 INT,
	height2 INT,
	file2_id INT,
	width3 INT,
	height3 INT,
	file3_id INT,
	width4 INT,
	height4 INT,
	file4_id INT,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE lang (
	id CHAR(2),
	name VARCHAR(32),
	PRIMARY KEY (id)
) ENGINE=InnoDB;


CREATE TABLE node (
	id INT AUTO_INCREMENT,
	parent_id INT,
	ldel INT,
	rdel INT,
	urn VARCHAR(128), -- il contenuto diventa raggiungibile alla url www.xxx.yyy/content/url.html
	type VARCHAR(32),
	subtype VARCHAR(32),
	sort_index INT,
	image_id INT,
	file_id INT,
	record_mode_id INT, -- gestione attiva del record mode
	UNIQUE KEY (urn),
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE node_text (
	node_id INT,
	lang_id CHAR(2),
	title VARCHAR(128),
	subtitle VARCHAR(256),
	preview TEXT,
	body TEXT,
	PRIMARY KEY (node_id, lang_id)
) ENGINE=InnoDB;

CREATE TABLE comment (
	id INT AUTO_INCREMENT,
	content_id INT,
	comment_id INT, -- risposte a commenti (albero di commenti)
	approved INT(1),
	record_mode_id INT,
	body TEXT,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE tag (
	id INT AUTO_INCREMENT,
	value VARCHAR(64),
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE node_tag (
	node_id INT,
	tag_id INT,
	PRIMARY KEY (node_id, tag_id)
) ENGINE=InnoDB;