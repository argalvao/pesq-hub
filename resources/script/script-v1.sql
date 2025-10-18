CREATE TABLE area_pesquisa (
	id uuid PRIMARY KEY,
	nome varchar(100) not null,
	descricao text
);

CREATE TABLE professor (
	id uuid primary key,
	nome varchar(75) not null,
	email varchar(100) unique,
	telefone varchar(11) unique,
	curso varchar(45) not null,
	departamento varchar(45)
);

CREATE TABLE linha_pesquisa (
	id uuid primary key,
	nome varchar(255),
	descricao text,
	id_area_pesquisa uuid,
	
	FOREIGN KEY (id_area_pesquisa) REFERENCES area_pesquisa(id)
);

CREATE TABLE professor_has_linha_pesquisa (
	id_professor uuid,
	id_linha_pesquisa uuid,
	PRIMARY KEY(id_professor, id_linha_pesquisa),
	
	FOREIGN KEY (id_professor) REFERENCES professor(id) 
	ON DELETE RESTRICT ON UPDATE CASCADE,
	
	FOREIGN KEY (id_linha_pesquisa) REFERENCES linha_pesquisa(id) 
	ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE professor_has_area_interesse (
	id_professor uuid,
	area_pesquisa uuid,
	PRIMARY KEY(id_professor, area_pesquisa),
	
	FOREIGN KEY (id_professor) REFERENCES professor(id) 
	ON DELETE RESTRICT ON UPDATE CASCADE,
	
	FOREIGN KEY (area_pesquisa) REFERENCES area_pesquisa(id) 
	ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";