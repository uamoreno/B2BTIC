CREATE DATABASE b2b;

CREATE TABLE b2b.archivo (
	id BIGINT UNSIGNED NOT NULL,
	id_archivo BIGINT UNSIGNED NOT NULL,
	nombre varchar(250) NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=latin1
COLLATE=latin1_spanish_ci;

CREATE TABLE b2b.extension (
	id BIGINT UNSIGNED NOT NULL,
	extension varchar(10) NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=latin1
COLLATE=latin1_spanish_ci;
