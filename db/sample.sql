
INSERT INTO members(name,email,password,organization,recovery_email) VALUES
('Eesha Patel','eesha@example.com','pass','Concordia','eesha.recover@example.com'),
('Bhavya Mehta','bhavya@example.com','pass','Concordia','bhavya.recover@example.com'),
('A Razk','arazk@example.com','pass','Concordia','arazk.recover@example.com');


INSERT INTO authors(member_id,orcid,bio)
SELECT member_id,'0000-0002-1234-5678','Writes DB texts'
FROM members WHERE email='eesha@example.com';

INSERT INTO members (name, email, password, organization, is_admin)
VALUES ('Admin User', 'admin@example.com', 'admin123', 'CFP Admin', 1);



INSERT INTO items(author_id,title,description) VALUES
((SELECT author_id FROM authors LIMIT 1),'Intro to Databases','A gentle intro to DBMS'),
((SELECT author_id FROM authors LIMIT 1),'Relational Design 3NF','Normalization basics');


INSERT INTO charities(charity_name,description) VALUES
('KidsLearn','Children education charity'),
('CodeForAll','Coding education access');


INSERT INTO downloads(member_id,item_id)
VALUES (
 (SELECT member_id FROM members WHERE email='bhavya@example.com'),
 (SELECT item_id FROM items WHERE title='Intro to Databases')
);


INSERT INTO donations(member_id,item_id,charity_id,amount,pct_charity,pct_author,pct_cfp)
VALUES (
 (SELECT member_id FROM members WHERE email='bhavya@example.com'),
 (SELECT item_id FROM items WHERE title='Intro to Databases'),
 (SELECT charity_id FROM charities WHERE charity_name='KidsLearn'),
 20.00, 60, 20, 20
);


INSERT INTO committees(committee_name,description)
VALUES ('Plagiarism Review','Reviews suspected plagiarized items');


INSERT INTO committee_members(committee_id,member_id)
VALUES (
 (SELECT committee_id FROM committees WHERE committee_name='Plagiarism Review'),
 (SELECT member_id FROM members WHERE email='eesha@example.com')
),(
 (SELECT committee_id FROM committees WHERE committee_name='Plagiarism Review'),
 (SELECT member_id FROM members WHERE email='bhavya@example.com')
);


INSERT INTO comments(item_id,member_id,comment_text)
VALUES (
 (SELECT item_id FROM items WHERE title='Intro to Databases'),
 (SELECT member_id FROM members WHERE email='bhavya@example.com'),
 'Great intro!'
);


INSERT INTO plagiarism_votes(item_id,committee_id,member_id,vote_value)
VALUES (
 (SELECT item_id FROM items WHERE title='Intro to Databases'),
 (SELECT committee_id FROM committees WHERE committee_name='Plagiarism Review'),
 (SELECT member_id FROM members WHERE email='eesha@example.com'),
 'not_plagiarized'
);
