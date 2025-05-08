-- Crear la base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS gamecore;

-- Seleccionar la base de datos
USE gamecore;

-- ------------------------------------------------------
-- Tabla users_data (almacena la información personal de los usuarios)
-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS users_data (
    idUser INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    sexo ENUM('M', 'F', 'Otro') DEFAULT 'Otro',
    fecha_nacimiento DATE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------
-- Tabla users_login (almacena los detalles de inicio de sesión de los usuarios)
-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS users_login (
    idUser INT,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('user', 'admin') DEFAULT 'user',
    FOREIGN KEY (idUser) REFERENCES users_data(idUser) ON DELETE CASCADE
);

-- ------------------------------------------------------
-- Tabla noticias (almacena las noticias)
-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS noticias (
    idNoticia INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    imagen VARCHAR(255),
    texto TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idUser INT,
    FOREIGN KEY (idUser) REFERENCES users_data(idUser) ON DELETE CASCADE
);

-- ------------------------------------------------------
-- Tabla citas (almacena las citas o eventos)
-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS citas (
    idCita INT AUTO_INCREMENT PRIMARY KEY,        -- Columna para el ID de la cita
    idUser INT NOT NULL,                           -- Columna para almacenar el ID del usuario, no puede ser NULL
    titulo VARCHAR(255) NOT NULL,                  -- Columna para el título de la cita, no puede ser NULL
    descripcion TEXT,                              -- Columna para la descripción de la cita (puede ser más larga)
    fecha_inicio DATETIME NOT NULL,                -- Columna para la fecha de inicio de la cita, no puede ser NULL
    fecha_fin DATETIME NOT NULL,                   -- Columna para la fecha de finalización de la cita, no puede ser NULL
    motivo TEXT NOT NULL,                         -- Columna para el motivo de la cita, no puede ser NULL
    registrado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Columna para la fecha de creación de la cita (automática)
    
    -- Definición de la clave foránea
    FOREIGN KEY (idUser) REFERENCES users_data(idUser) ON DELETE CASCADE
);

-- ------------------------------------------------------
-- Tabla roles (si deseas manejar los roles de manera más flexible)
-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS roles (
    idRol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Insertar roles predeterminados en la tabla roles
INSERT INTO roles (nombre) VALUES ('user'), ('admin');