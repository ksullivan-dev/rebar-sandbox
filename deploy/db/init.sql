CREATE DATABASE IF NOT EXISTS sandbox;
GRANT ALL ON sandbox.* TO sandbox@localhost IDENTIFIED BY 'APassword';
GRANT ALL ON sandbox.* TO sandbox@'127.0.0.1' IDENTIFIED BY 'APassword';
