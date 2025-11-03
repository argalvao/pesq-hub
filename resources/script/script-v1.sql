CREATE TABLE area_pesquisa (
	id uuid PRIMARY KEY,
	nome varchar(100) not null,
	descricao text,
	criado_por varchar(100)/*pensei em salvar o id de quem fez o cadastro*/
);

CREATE TABLE curso (
	id uuid primary key,
	nome varchar(45) not null
);

CREATE TABLE professor (
	id uuid primary key,
	nome varchar(75) not null,
	email varchar(100) unique,
	telefone varchar(11) unique,
	id_curso uuid not null,
	departamento varchar(45),
	criado_por varchar(100),/*pensei em salvar o id de quem fez o cadastro*/
	
	FOREIGN KEY (id_curso) REFERENCES curso(id)
);

CREATE TABLE linha_pesquisa (
	id uuid primary key,
	nome varchar(255),
	descricao text,
	id_area_pesquisa uuid,
	criado_por varchar(100),/*pensei em salvar o id de quem fez o cadastro*/
	
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

CREATE TYPE tipo_usuario AS ENUM ('SUPER', 'DA', 'BASICO');

CREATE TABLE usuario (
	id uuid primary key,
	nome varchar(100) not null,
	email varchar(45) not null unique,
	senha varchar(100) not null,
	ativo bool not null,
	data_criacao timestamp,
	data_atualizacao timestamp,
	tipo_permissao tipo_usuario not null,
	id_curso uuid,
	
	FOREIGN KEY (id_curso) REFERENCES curso(id)
);

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";