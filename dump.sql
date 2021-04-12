CREATE DATABASE paruyrtest; 
CREATE TABLE info(
id int NOT NULL AUTO_INCREMENT,
ip_address char(20),
user_agent text,
view_date datetime, 
page_url char(255),
view_count int,
PRIMARY KEY (id)
);