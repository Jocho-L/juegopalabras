-- Active: 1731358489281@@127.0.0.1@3307@juegopalabras
CREATE DATABASE juegopalabras;
USE juegopalabras;

-- Tabla Roles
CREATE TABLE Roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) UNIQUE NOT NULL
);

-- Tabla de Usuarios
CREATE TABLE Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol_id INT,
    FOREIGN KEY (rol_id) REFERENCES Roles(id) ON DELETE SET NULL
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

-- Tabla de Rachas
CREATE TABLE Rachas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    racha_actual INT DEFAULT 0,
    mejor_racha INT DEFAULT 0,
    id_palabra_actual INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_palabra_actual) REFERENCES Palabras(id) ON DELETE SET NULL
);

-- inserts

INSERT INTO Palabras (palabra) VALUES 
    ('ahorcado'), 
    ('programacion'), 
    ('desarrollo'), 
    ('servidor'), 
    ('navegador');

SELECT * FROM palabras