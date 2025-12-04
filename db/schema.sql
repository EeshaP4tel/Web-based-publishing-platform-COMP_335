
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS plagiarism_votes;
DROP TABLE IF EXISTS committee_members;
DROP TABLE IF EXISTS committees;
DROP TABLE IF EXISTS donations;
DROP TABLE IF EXISTS charities;
DROP TABLE IF EXISTS downloads;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS authors;
DROP TABLE IF EXISTS members;

CREATE TABLE members (
  member_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  organization VARCHAR(120),
  introducer_id INT NULL,
  recovery_email VARCHAR(120),
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  join_date DATE DEFAULT (CURRENT_DATE),
  status ENUM('active','suspended','blacklisted') DEFAULT 'active',
  FOREIGN KEY (introducer_id) REFERENCES members(member_id)
);

CREATE TABLE authors (
  author_id INT AUTO_INCREMENT PRIMARY KEY,
  member_id INT NOT NULL,
  orcid VARCHAR(40) NOT NULL,
  bio TEXT,
  FOREIGN KEY (member_id) REFERENCES members(member_id)
);

CREATE TABLE items (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  author_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  upload_date DATE DEFAULT (CURRENT_DATE),
  status ENUM('pending','approved','plagiarized','removed') DEFAULT 'approved',
  FOREIGN KEY (author_id) REFERENCES authors(author_id)
);

CREATE TABLE comments (
  comment_id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT NOT NULL,
  member_id INT NOT NULL,
  comment_text TEXT NOT NULL,
  comment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (item_id) REFERENCES items(item_id),
  FOREIGN KEY (member_id) REFERENCES members(member_id)
);

CREATE TABLE downloads (
  download_id INT AUTO_INCREMENT PRIMARY KEY,
  member_id INT NOT NULL,
  item_id INT NOT NULL,
  download_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (member_id) REFERENCES members(member_id),
  FOREIGN KEY (item_id) REFERENCES items(item_id)
);

CREATE TABLE charities (
  charity_id INT AUTO_INCREMENT PRIMARY KEY,
  charity_name VARCHAR(150) NOT NULL,
  description TEXT
);

CREATE TABLE donations (
  donation_id INT AUTO_INCREMENT PRIMARY KEY,
  member_id INT NOT NULL,
  item_id INT NOT NULL,
  charity_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  pct_charity TINYINT NOT NULL,
  pct_author TINYINT NOT NULL,
  pct_cfp TINYINT NOT NULL,
  donation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (member_id) REFERENCES members(member_id),
  FOREIGN KEY (item_id) REFERENCES items(item_id),
  FOREIGN KEY (charity_id) REFERENCES charities(charity_id)
);

CREATE TABLE committees (
  committee_id INT AUTO_INCREMENT PRIMARY KEY,
  committee_name VARCHAR(150) NOT NULL,
  description TEXT
);

CREATE TABLE committee_members (
  committee_id INT NOT NULL,
  member_id INT NOT NULL,
  join_date DATE DEFAULT (CURRENT_DATE),
  PRIMARY KEY (committee_id, member_id),
  FOREIGN KEY (committee_id) REFERENCES committees(committee_id),
  FOREIGN KEY (member_id) REFERENCES members(member_id)
);

CREATE TABLE plagiarism_votes (
  vote_id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT NOT NULL,
  committee_id INT NOT NULL,
  member_id INT NOT NULL,
  vote_value ENUM('plagiarized','not_plagiarized') NOT NULL,
  vote_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (item_id) REFERENCES items(item_id),
  FOREIGN KEY (committee_id) REFERENCES committees(committee_id),
  FOREIGN KEY (member_id) REFERENCES members(member_id)
);

CREATE TABLE messages (
  message_id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  receiver_id INT NOT NULL,
  item_id INT NOT NULL,
  comment_id INT NULL,
  message_text TEXT NOT NULL,
  is_public TINYINT(1) DEFAULT 0,
  sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (sender_id) REFERENCES members(member_id),
  FOREIGN KEY (receiver_id) REFERENCES members(member_id),
  FOREIGN KEY (item_id) REFERENCES items(item_id),
  FOREIGN KEY (comment_id) REFERENCES comments(comment_id)
);
SET FOREIGN_KEY_CHECKS=1;
