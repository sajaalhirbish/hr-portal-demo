-- 003_seed_admin.sql : demo admin user (change the password before any real use)
INSERT INTO users (full_name, username, password_hash)
VALUES ('HR Admin', 'admin', '$2y$10$s7w2ao3FDZWZvSAI5bppbOPiohawa0ivwKSOY1CiFGIyClt4nBm4m');
COMMIT;