-- Create user if not exists
CREATE USER IF NOT EXISTS 'cloud_user'@'%' IDENTIFIED BY 'cloud_password';
GRANT ALL PRIVILEGES ON cloud_platform.* TO 'cloud_user'@'%';
FLUSH PRIVILEGES;

