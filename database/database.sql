-- Active: 1724697242762@@127.0.0.1@3306@juegopalabras
CREATE DATABASE juegopalabras;
USE juegopalabras;

-- Tabla Roles
CREATE TABLE Roles (
    idRol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) UNIQUE NOT NULL
);

-- Tabla de Usuarios
CREATE TABLE Usuarios (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    idRol INT,
    FOREIGN KEY (idRol) REFERENCES Roles(idRol) ON DELETE SET NULL
);


-- Tabla de Palabras
CREATE TABLE Palabras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    palabra VARCHAR(50) NOT NULL
);

-- Tabla de Imágenes
CREATE TABLE Imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_palabra INT,
    ruta_imagen VARCHAR(255) NOT NULL ,
    FOREIGN KEY (id_palabra) REFERENCES Palabras(id) ON DELETE CASCADE
);

-- inserts


SELECT * FROM imagenes;
SELECT * FROM palabras;
SELECT * FROM Roles;
SELECT * FROM Usuarios;

drop Table rachas;
drop Table imagenes;
drop Table Roles;


INSERT INTO Usuarios (nombre_usuario, email, password, rol_id)
VALUES ('admin', 'admin@example.com', 'admin', 1);

INSERT INTO Roles (nombre_rol) VALUES
('Administrador'),
('Usuario');
