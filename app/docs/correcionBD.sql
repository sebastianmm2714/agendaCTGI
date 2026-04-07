

CREATE TABLE funcionarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cargo VARCHAR(100) NOT NULL,
    tipo ENUM('SUPERVISOR','ORDENADOR') NOT NULL,
    firma VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE categorias_personal (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE clasificacion_informacion (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE estados_agenda (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    tipo_documento VARCHAR(20),
    numero_documento VARCHAR(30),
    numero_contrato VARCHAR(50),
    anio_contrato YEAR,
    fecha_vencimiento DATE,
    objeto_contractual TEXT,
    firma VARCHAR(255),
    salario_honorarios DECIMAL(15,2),
    
    categoria_personal_id BIGINT UNSIGNED NULL,
    supervisor_id BIGINT UNSIGNED NULL,
    ordenador_id BIGINT UNSIGNED NULL,

    role ENUM('ADMIN','USUARIO') DEFAULT 'USUARIO',

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (categoria_personal_id)
        REFERENCES categorias_personal(id)
        ON DELETE SET NULL,

    FOREIGN KEY (supervisor_id)
        REFERENCES funcionarios(id)
        ON DELETE SET NULL,

    FOREIGN KEY (ordenador_id)
        REFERENCES funcionarios(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE obligaciones_contrato (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_personal_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (categoria_personal_id)
        REFERENCES categorias_personal(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE agendas_desplazamiento (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,
    clasificacion_id BIGINT UNSIGNED NOT NULL,
    estado_id BIGINT UNSIGNED NOT NULL,

    fecha_elaboracion DATE NOT NULL,

    ruta VARCHAR(255) NOT NULL,
    entidad_empresa VARCHAR(150) NOT NULL,
    contacto VARCHAR(150) NOT NULL,

    objetivo_desplazamiento VARCHAR(150) NOT NULL,
    
    regional VARCHAR(150) NULL,
    centro VARCHAR(150) NULL,
    destinos JSON NULL,

    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,


    valor_viaticos DECIMAL(12,2) NULL,
    observaciones_finanzas TEXT NULL,
    cdp VARCHAR(50) NULL,
    
	valor_terminal_aereo DECIMAL(12,2) NULL,
	valor_terminal_terrestre DECIMAL(12,2) NULL,
	valor_intermunicipal DECIMAL(12,2) NULL,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY (clasificacion_id)
        REFERENCES clasificacion_informacion(id),

    FOREIGN KEY (estado_id)
        REFERENCES estados_agenda(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE agenda_obligaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    agenda_id BIGINT UNSIGNED NOT NULL,
    obligacion_id BIGINT UNSIGNED NOT NULL,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (agenda_id)
        REFERENCES agendas_desplazamiento(id)
        ON DELETE CASCADE,

    FOREIGN KEY (obligacion_id)
        REFERENCES obligaciones_contrato(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE actividades (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    agenda_id BIGINT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,

    transporte_ida JSON NULL,
    transporte_regreso JSON NULL,

    actividad TEXT NOT NULL,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (agenda_id)
        REFERENCES agendas_desplazamiento(id)
        ON DELETE CASCADE
);