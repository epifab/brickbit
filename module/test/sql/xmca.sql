CREATE DATABASE xmca
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE xmca;

CREATE TABLE xmca_config (
	label VARCHAR(32),
	intval INT,
	real_val DOUBLE,
	date_val DATE,
	datetime_val DATETIME,
	time_val TIME,
	l_string_val VARCHAR(300),
	xl_string_val TEXT,
	value TEXT,
	PRIMARY KEY (label)
) ENGINE=InnoDB;

CREATE TABLE xmca_email (
	id INT AUTO_INCREMENT,
	sent_date_time DATETIME,
	ip_address VARCHAR(15),
	user_id INT,
	recipient_email VARCHAR(100),
	recipient_name VARCHAR(200),
	sender_email VARCHAR(100),
	sender_name VARCHAR(200),
	body TEXT,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_component (
	id INT AUTO_INCREMENT,
	name VARCHAR(30),
	PRIMARY KEY (id),
	UNIQUE KEY (name)
) ENGINE=InnoDB;

CREATE TABLE xmca_group (
	id INT AUTO_INCREMENT,
	name VARCHAR(30),
	PRIMARY KEY (id),
	UNIQUE KEY (name)
) ENGINE=InnoDB;

CREATE TABLE xmca_group_component (
	group_id INT,
	component_id INT,
	PRIMARY KEY (group_id, component_id)
) ENGINE=InnoDB;

CREATE TABLE xmca_user (
	id INT AUTO_INCREMENT,
	email VARCHAR(80),
	password CHAR(32),
	full_name VARCHAR(100),
	last_login DATETIME,
	ins_date_time DATETIME,
	last_upd_date_time DATETIME,
	KEY (email),
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_user_group (
	user_id INT,
	group_id INT,
	PRIMARY KEY (user_id, group_id)
) ENGINE=InnoDB;

CREATE TABLE xmca_record_mode (
	id INT AUTO_INCREMENT,
	owner_id INT,
	group_id INT,
	read_mode INT,
	edit_mode INT,
	delete_mode INT,
	ins_date_time DATETIME,
	last_modifier_id INT,
	last_upd_date_time DATETIME,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_record_mode_log (
	id INT AUTO_INCREMENT,
	record_mode_id INT,
	upd_date_time DATETIME,
	modifier_id INT,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_log (
	id INT AUTO_INCREMENT,
	user_id INT,
	script_url VARCHAR(100),
	date_time_request DATETIME,
	ip_address VARCHAR(15),
	body TEXT,
	output TEXT,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_dir (
	id INT AUTO_INCREMENT,
	path VARCHAR(80),
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_file (
	id INT AUTO_INCREMENT,
	dir_id INT,
	name VARCHAR(20),
	size INT,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_image (
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

CREATE TABLE xmca_lang (
	id CHAR(2),
	description VARCHAR(20),
	PRIMARY KEY (id)
) ENGINE=InnoDB;


CREATE TABLE xmca_page_style (
	code VARCHAR(15),
	description VARCHAR(100),
	page_template VARCHAR(50),
	PRIMARY KEY (code)
) ENGINE=InnoDB;

CREATE TABLE xmca_page (
	id INT AUTO_INCREMENT,
	url VARCHAR(50), -- www.xxx.yyy/URL.html
	style_code VARCHAR(15),
	sort_index INT,
	content_sorting ENUM('date_asc', 'date_desc', 'sort_index'),
	content_paging INT DEFAULT 0, -- se page size e' a 0, nessun controllo di paginazione
	content_filters INT(1), -- mostra / nascondi controlli per la ricerca di contenuti all'interno della pagina
	record_mode_id INT, -- gestione attiva del record mode
	UNIQUE KEY (url),
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_page_text (
	page_id INT,
	lang_id CHAR(2),
	title VARCHAR(100),
	body TEXT,
	PRIMARY KEY (page_id, lang_id)
) ENGINE=InnoDB;

CREATE TABLE xmca_content_type (
	code VARCHAR(32),
	description VARCHAR(100),
	PRIMARY KEY (code)
) ENGINE=InnoDB;

CREATE TABLE xmca_content_style (
	code VARCHAR(32),
	description VARCHAR(100),
	content_template VARCHAR(50),
	preview_template VARCHAR(50),
	PRIMARY KEY (code)
) ENGINE=InnoDB;

CREATE TABLE xmca_content (
	id INT AUTO_INCREMENT,
	page_id INT, -- 0 per contenuti visibile su ogni pagina
	supercontent_id INT,
	url VARCHAR(100), -- il contenuto diventa raggiungibile alla url www.xxx.yyy/content/url.html
	type_code VARCHAR(32),
	style_code VARCHAR(32),
	-- layout ENUM('TIB','T2ISB','T2IMB','2ISTB','2IMTB'),
	-- 1 'TIB': titolo, immagine dimensioni complete, corpo
	-- 2 'T2ISB': titolo in alto, colonna sinistra immagine dimensioni S e a destra corpo
	-- 3 'T2IMB': titolo in alto, colonna sinistra immagine dimensioni M e a destra corpo
	-- 4 '2ISTB': colonna sinistra immagine dimensioni M e a destra titolo e corpo
	-- 5 '2IMTB': colonna sinistra immagine dimensioni S e a destra titolo e corpo
	main_column INT(1), -- colonna principale o secondaria
	sort_index INT,
	public_date DATETIME,
	image_id INT,
	download_file_id INT,
	audio_file_id INT,
	video_file_id INT,
	expandable INT(1),
	comments INT(1),
	social_networks INT(1),
	record_mode_id INT, -- gestione attiva del record mode
	UNIQUE KEY (url),
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_content_text (
	content_id INT,
	lang_id CHAR(2),
	title VARCHAR(100),
	subtitle VARCHAR(200),
	body TEXT,
	preview TEXT,
	PRIMARY KEY (content_id, lang_id)
) ENGINE=InnoDB;

CREATE TABLE xmca_comment (
	id INT AUTO_INCREMENT,
	content_id INT,
	comment_id INT, -- risposte a commenti (albero di commenti)
	approved INT(1),
	record_mode_id INT,
	body TEXT,
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_tag (
	id INT AUTO_INCREMENT,
	value VARCHAR(50),
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE xmca_content_tag (
	content_id INT,
	tag_id INT,
	PRIMARY KEY (content_id, tag_id)
) ENGINE=InnoDB;

CREATE TABLE xmca_page_tag (
	page_id INT,
	tag_id INT,
	PRIMARY KEY (page_id, tag_id)
) ENGINE=InnoDB;