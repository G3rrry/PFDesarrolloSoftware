CREATE DATABASE IF NOT EXISTS HarinasElizondo;
USE HarinasElizondo;

-- Tabla EquipoLaboratorio
CREATE TABLE IF NOT EXISTS EquipoLaboratorio (
    claveEquipo INT NOT NULL AUTO_INCREMENT,
    marca VARCHAR(255),
    modelo VARCHAR(255),
    serie VARCHAR(50),
    descripcionLarga VARCHAR(200),
    descripcionCorta VARCHAR(50),
    fechaAdquisicion DATE,
    garantia VARCHAR(100),
    vigenciaGarantia VARCHAR(100),
    ubicacion VARCHAR(255),
    responsable VARCHAR(255),
    estado VARCHAR(255),
    PRIMARY KEY (claveEquipo)
);

-- Tabla Proveedores
CREATE TABLE IF NOT EXISTS Proveedores (
    claveProveedor INT NOT NULL AUTO_INCREMENT,
    proveedor VARCHAR(255),
    PRIMARY KEY (claveProveedor)
);

-- Tabla Lotes
CREATE TABLE IF NOT EXISTS Lotes (
    numeroLote INT NOT NULL AUTO_INCREMENT,
    fechaCaducidad DATE,
    fechaProduccion DATE,
    cantidadLote INT,
    PRIMARY KEY (numeroLote)
);

-- Tabla Certificado
CREATE TABLE IF NOT EXISTS Certificado (
    numeroCertificado INT NOT NULL AUTO_INCREMENT,
    numeroLote INT,
    PRIMARY KEY (numeroCertificado),
    FOREIGN KEY (numeroLote) REFERENCES Lotes(numeroLote)
);

-- Tabla FechaDeAnalisis
CREATE TABLE IF NOT EXISTS FechaDeAnalisis (
    numeroCertificado INT NOT NULL,
    fechaAnalisis DATE,
    secuenciaInspeccion CHAR(2),
    PRIMARY KEY (numeroCertificado),
    FOREIGN KEY (numeroCertificado) REFERENCES Certificado(numeroCertificado)
);

-- Tabla Resultados
CREATE TABLE IF NOT EXISTS Resultados (
    resultado FLOAT,
    numeroCertificado INT NOT NULL,
    idParametro INT,
    claveEquipo INT,
    PRIMARY KEY (numeroCertificado, idParametro, claveEquipo),
    FOREIGN KEY (numeroCertificado) REFERENCES Certificado(numeroCertificado),
    FOREIGN KEY (idParametro) REFERENCES Parametros(idParametro),
    FOREIGN KEY (claveEquipo) REFERENCES EquipoLaboratorio(claveEquipo)
);

-- Tabla Parametros
CREATE TABLE IF NOT EXISTS Parametros (
    idParametro INT NOT NULL AUTO_INCREMENT,
    nombreParametro VARCHAR(240),
    descripcion VARCHAR(255),
    PRIMARY KEY (idParametro)
);

-- Tabla ValoresDeReferencia
CREATE TABLE IF NOT EXISTS ValoresDeReferencia (
    idSAP INT,
    idParametro INT NOT NULL,
    min FLOAT,
    max FLOAT,
    PRIMARY KEY (idParametro),
    FOREIGN KEY (idParametro) REFERENCES Parametros(idParametro)
);

-- Tabla Pedido
CREATE TABLE IF NOT EXISTS Pedido (
    numeroOrden INT NOT NULL AUTO_INCREMENT,
    idSAP INT,
    cantidadTotal INT,
    numeroFactura INT,
    fechaEnvio DATE,
    fechaCaducidad DATE,
    PRIMARY KEY (numeroOrden)
);

-- Tabla Orden
CREATE TABLE IF NOT EXISTS Orden (
    numeroOrden INT NOT NULL,
    numeroCertificado INT NOT NULL,
    cantidadLote INT,
    path VARCHAR(255),
    PRIMARY KEY (numeroOrden, numeroCertificado),
    FOREIGN KEY (numeroOrden) REFERENCES Pedido(numeroOrden),
    FOREIGN KEY (numeroCertificado) REFERENCES Certificado(numeroCertificado)
);

-- Tabla Clientes
CREATE TABLE IF NOT EXISTS Clientes (
    idSAP INT NOT NULL AUTO_INCREMENT,
    RFC VARCHAR(13),
    nombreCliente VARCHAR(70),
    domicilioEntrega VARCHAR(255),
    domicilioFiscal VARCHAR(255),
    requiereCertificado BOOLEAN,
    estadoCliente BOOLEAN,
    correo VARCHAR(255),
    telefono VARCHAR(70),
    nombreContacto VARCHAR(70),
    correoContacto VARCHAR(255),
    telefonoContacto VARCHAR(70),
    PRIMARY KEY (idSAP)
);

-- Tabla CausaDeBaja
CREATE TABLE IF NOT EXISTS CausaDeBaja (
    idSAP INT NOT NULL AUTO_INCREMENT,
    causaBaja VARCHAR(255),
    PRIMARY KEY (idSAP)
);
