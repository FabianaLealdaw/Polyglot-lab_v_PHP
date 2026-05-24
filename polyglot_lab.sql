CREATE DATABASE IF NOT EXISTS polyglot_lab
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE polyglot_lab;

DROP TABLE IF EXISTS quote_requests;
DROP TABLE IF EXISTS citas;
DROP TABLE IF EXISTS noticias;
DROP TABLE IF EXISTS users_login;
DROP TABLE IF EXISTS users_data;

CREATE TABLE users_data (
    id_user INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    direccion VARCHAR(150) NOT NULL,
    sexo ENUM('female', 'male', 'other', 'prefer_not_to_say') NOT NULL DEFAULT 'prefer_not_to_say',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email (email)
);

CREATE TABLE users_login (
    id_login INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user INT UNSIGNED NOT NULL,
    username VARCHAR(50) NOT NULL,
    usuario VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_username (username),
    UNIQUE KEY unique_usuario (usuario),
    UNIQUE KEY unique_id_user (id_user),
    CONSTRAINT fk_users_login_user
        FOREIGN KEY (id_user) REFERENCES users_data(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE quote_requests (
    id_quote INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    descripcion TEXT NOT NULL,
    curso VARCHAR(100) NOT NULL,
    meses INT UNSIGNED NOT NULL,
    plazo INT UNSIGNED NOT NULL,
    extras TEXT DEFAULT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE citas (
    id_cita INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user INT UNSIGNED NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT DEFAULT NULL,
    motivo_cita TEXT NOT NULL,
    fecha_cita DATE NOT NULL,
    hora_cita TIME NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada') NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_citas_user
        FOREIGN KEY (id_user) REFERENCES users_data(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE noticias (
    id_noticia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    resumen TEXT NOT NULL,
    contenido TEXT NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    id_user INT UNSIGNED NOT NULL,
    autor_nombre VARCHAR(100) DEFAULT NULL,
    fecha_publicacion DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_titulo_noticia (titulo),
    CONSTRAINT fk_noticias_user
        FOREIGN KEY (id_user) REFERENCES users_data(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO noticias (id_user, titulo, resumen, contenido, imagen, autor_nombre, fecha_publicacion)
VALUES
(1, 'Welcome to Polyglot Lab', 'First news entry for the new PHP project.', 'Welcome to Polyglot Lab. This first news entry introduces the new PHP and MySQL version of the website and marks the start of a more dynamic experience for students and visitors.', './assets/images/gallery/clase1.jpg', 'Polyglot Lab Admin', '2026-05-13'),
(1, 'New language courses available', 'We are happy to announce the launch of new Korean and Italian courses for beginners.', 'We are happy to announce the launch of new Korean and Italian courses for beginners. These programs are designed for students who want to start learning a new language from scratch in a supportive and motivating environment. Classes focus on real communication, cultural context and practical use of the language, guided by native and certified teachers.', './assets/images/gallery/clase2.jpg', 'Polyglot Lab Admin', '2026-02-01'),
(1, 'Summer intensive programs', 'Our summer intensive courses are now open for enrollment.', 'Our summer intensive programs are now open for enrollment. These courses are ideal for students who want to improve their communication skills in a short period of time through immersive and intensive practice. Small groups and personalized attention ensure effective learning and visible progress.', './assets/images/gallery/clase3.jpg', 'Polyglot Lab Admin', '2026-03-15'),
(1, 'Online classes now available', 'You can now join our classes online and learn from anywhere in the world.', 'You can now join our classes online and learn from anywhere in the world. Our online lessons offer the same quality, interaction and methodology as our in-person classes. This flexible option is perfect for students who want to learn at their own pace without geographical limitations.', './assets/images/gallery/clase4.jpg', 'Polyglot Lab Admin', '2026-01-10'),
(1, 'Conversation club every Friday', 'Join our weekly conversation club to practice in a relaxed environment.', 'Our weekly conversation club is now open every Friday afternoon for students of all levels. It is a great opportunity to improve speaking fluency, gain confidence and meet other learners while practicing real-life conversations in a friendly atmosphere.', './assets/images/gallery/clase5.jpg', 'Polyglot Lab Admin', '2026-04-08'),
(1, 'Enrollment open for autumn term', 'Registrations for the autumn term are now available at Polyglot Lab.', 'Enrollment for the autumn term is now open. Students can reserve their place in regular, intensive and online programs. We recommend registering early because group sizes are limited and places fill quickly.', './assets/images/gallery/adults.jpg', 'Polyglot Lab Admin', '2026-04-28');
