-- Use the existing database
USE bitcot_db;

-- Create the users table if it doesn’t exist
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- (Optional) Insert a sample record
INSERT INTO users (name, email, message)
VALUES 
('Arpit Choudhary', 'arpit@example.com', 'Hello from Bitcot PHP app!');

