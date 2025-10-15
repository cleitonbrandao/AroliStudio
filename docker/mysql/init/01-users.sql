-- create other users
CREATE USER 'cyber'@'localhost' IDENTIFIED BY 'cyber';
CREATE USER 'cyber'@'%' IDENTIFIED BY 'cyber';

GRANT ALL PRIVILEGES ON *.* TO 'cyber'@'localhost';
GRANT ALL PRIVILEGES ON *.* TO 'cyber'@'%';

FLUSH PRIVILEGES;