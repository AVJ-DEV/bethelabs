-- Run this in phpMyAdmin or MySQL command line to create the database
CREATE DATABASE bethelabs_db;
USE bethelabs_db;
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR (255) NOT NULL,
    email VARCHAR (255) NOT NULL,
    message TEXT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR (255) NOT NULL,
    email VARCHAR (255) NOT NULL,
    phone VARCHAR (20) NOT NULL,
    formation VARCHAR (255) NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR (255) NOT NULL,
    email VARCHAR (255) NOT NULL,
    rating INT NOT NULL CHECK (
        rating >= 1
        AND rating <= 5
    ),
    comment TEXT,
    image VARCHAR (255),
    status ENUM ('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON
            UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR (255) NOT NULL,
    position VARCHAR (255),
    bio TEXT,
    image VARCHAR (255),
    speciality VARCHAR (255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR (255) NOT NULL,
    description TEXT NOT NULL,
    content LONGTEXT,
    image VARCHAR (255),
    video VARCHAR (255),
    author VARCHAR (255),
    status ENUM ('draft', 'published', 'archived') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE concours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR (255) NOT NULL,
    description TEXT NOT NULL,
    rules LONGTEXT,
    prizes TEXT,
    image VARCHAR (255),
    video VARCHAR (255),
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM ('upcoming', 'active', 'closed', 'completed') DEFAULT 'upcoming',
    max_participants INT,
    current_participants INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR (255) NOT NULL,
    description TEXT NOT NULL,
    content LONGTEXT,
    category VARCHAR (100),
    level ENUM ('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    duration VARCHAR (100),
    price DECIMAL (10, 2),
    instructor VARCHAR (255),
    image VARCHAR (255),
    video VARCHAR (255),
    max_participants INT,
    current_participants INT DEFAULT 0,
    status ENUM ('planned', 'active', 'completed', 'cancelled') DEFAULT 'planned',
    start_date DATETIME,
    end_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE concours_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    concours_id INT NOT NULL,
    name VARCHAR (255) NOT NULL,
    email VARCHAR (255) NOT NULL,
    phone VARCHAR (20),
    submission TEXT,
    score INT,
    rank INT,
    participation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (concours_id) REFERENCES concours(id) ON DELETE CASCADE
);
CREATE TABLE formation_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    formation_id INT NOT NULL,
    name VARCHAR (255) NOT NULL,
    email VARCHAR (255) NOT NULL,
    phone VARCHAR (20),
    progress INT DEFAULT 0,
    certificate BOOLEAN DEFAULT FALSE,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations(id) ON DELETE CASCADE
);
CREATE TABLE media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR (255) NOT NULL,
    file_type ENUM ('image', 'video') NOT NULL,
    file_path VARCHAR (500) NOT NULL,
    file_size INT,
    mime_type VARCHAR (50),
    related_type ENUM ('news', 'formation', 'concours', 'other'),
    related_id INT,
    description TEXT,
    uploaded_by INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admins(id) ON DELETE SET NULL
);
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR (100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR (100) NOT NULL UNIQUE,
    description TEXT,
    module VARCHAR (100),
    action VARCHAR (50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE role_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role_id, permission_id)
);
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR (100) NOT NULL UNIQUE,
    email VARCHAR (255) NOT NULL UNIQUE,
    password VARCHAR (255) NOT NULL,
    first_name VARCHAR (100),
    last_name VARCHAR (100),
    role_id INT NOT NULL,
    status ENUM ('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR (100) NOT NULL,
    module VARCHAR (100),
    description TEXT,
    ip_address VARCHAR (45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);
INSERT INTO roles (role_name, description) VALUES
('Super Admin', 'Full access to all features and settings'),
('Admin', 'Can manage content and users'),
('Editor', 'Can create and edit content'),
('Moderator', 'Can moderate comments and submissions');
INSERT INTO permissions (permission_name, description, module, action) VALUES
('manage_users', 'Can manage admin users', 'admin', 'read,create,update,delete'),
('manage_roles', 'Can manage roles', 'admin', 'read,create,update,delete'),
('manage_news', 'Can manage news', 'news', 'read,create,update,delete'),
('manage_formations', 'Can manage formations', 'formations', 'read,create,update,delete'),
('manage_concours', 'Can manage concours', 'concours', 'read,create,update,delete'),
('view_analytics', 'Can view analytics', 'dashboard', 'read'),
('manage_contact', 'Can manage contact messages', 'contact', 'read,delete'),
('manage_testimonials', 'Can manage testimonials', 'testimonials', 'read,update,delete'),
('manage_media', 'Can manage media files', 'media', 'read,create,delete');
INSERT INTO role_permissions (role_id, permission_id) SELECT r.id, p.id FROM roles r, permissions p WHERE r.role_name = 'Super Admin';

-- Partners Table
CREATE TABLE IF NOT EXISTS partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    firstname VARCHAR(255),
    expertise VARCHAR(255),
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default partners
INSERT INTO partners (name, firstname, expertise, image) VALUES
('Partner', 'Tech', 'Développement Web', 'partner1.jpg'),
('Partner', 'Cloud', 'Infrastructure Cloud', 'partner2.jpg'),
('Partner', 'Mobile', 'Développement Mobile', 'partner3.jpg'),
('Partner', 'Design', 'Design Graphique', 'partner4.jpg');
