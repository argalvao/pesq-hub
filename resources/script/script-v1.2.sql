--------------------------------------------------------------------------------
-- PASSO 1: Inserir todas as ÁREAS DE PESQUISA
-- Primeiro, inserimos todas as áreas de interesse e linhas de pesquisa na tabela 'area_pesquisa'.
-- Guarde esses IDs, pois vamos usá-los nos próximos passos.
--------------------------------------------------------------------------------

INSERT INTO area_pesquisa (id, nome, descricao) VALUES
('a1f1b3b4-1a93-4e31-9f3c-1b7d8d6a3a78', 'Inteligência Artificial', 'Pesquisa em algoritmos de IA e machine learning'),
('b2c2d4e5-2b84-4f22-8e4d-2c8e9e7b4b89', 'Machine Learning', NULL),
('c3d3e5f6-3c75-4d13-7d5e-3d9f0f8c5c9a', 'Engenharia de Software', 'Desenvolvimento de metodologias e ferramentas de software'),
('d4e4f6a7-4d66-4c04-6c6f-4ea0109d6da0', 'Desenvolvimento de Software', NULL),
('e5f5a7b8-5e57-4b95-5b70-5fb121ae7eb1', 'Metodologias Ágeis', NULL),
('f6a6b8c9-6f48-4a86-4a81-6ac232bf8fc2', 'Computação Gráfica', 'Processamento de imagens e renderização 3D'),
('a7b7c9d0-7a39-4977-3992-7bd343ca9ad3', 'Realidade Virtual', NULL),
('b8c8d0e1-8b2a-4868-28a3-8ce454dbabf4', 'Banco de Dados', 'Otimização e design de sistemas de banco de dados'),
('c9d9e1f2-9c1b-4759-17b4-9df565ecbca5', 'Big Data', NULL),
('d0e0f2a3-a09c-464a-06c5-ae0676fcdbd6', 'Redes de Computadores', 'Protocolos e arquiteturas de rede'),
('e1f1a3b4-b18d-453b-f5d6-bf1787adce17', 'Segurança', NULL),
('f2a2b4c5-c27e-442c-e4e7-cf2898beed28', 'Protocolos de Rede', NULL),
('a3b3c5d6-d36f-431d-d3f8-da39a9cff939', 'Matemática', 'Equações Diferenciais e Integrais 2');

--------------------------------------------------------------------------------
-- PASSO 2: Inserir as LINHAS DE PESQUISA
-- Agora, inserimos as linhas de pesquisa específicas, usando os IDs das áreas que criamos acima.
-- Por exemplo, a linha 'Inteligência Artificial' se conecta à área 'Inteligência Artificial'.
--------------------------------------------------------------------------------

INSERT INTO linha_pesquisa (id, nome, descricao, id_area_pesquisa) VALUES
('11111111-a1b2-c3d4-e5f6-111111111111', 'Inteligência Artificial', 'Pesquisa em algoritmos de IA e machine learning', 'a1f1b3b4-1a93-4e31-9f3c-1b7d8d6a3a78'),
('22222222-a1b2-c3d4-e5f6-222222222222', 'Engenharia de Software', 'Desenvolvimento de metodologias e ferramentas de software', 'c3d3e5f6-3c75-4d13-7d5e-3d9f0f8c5c9a'),
('33333333-a1b2-c3d4-e5f6-333333333333', 'Computação Gráfica', 'Processamento de imagens e renderização 3D', 'f6a6b8c9-6f48-4a86-4a81-6ac232bf8fc2'),
('44444444-a1b2-c3d4-e5f6-444444444444', 'Banco de Dados', 'Otimização e design de sistemas de banco de dados', 'b8c8d0e1-8b2a-4868-28a3-8ce454dbabf4'),
('55555555-a1b2-c3d4-e5f6-555555555555', 'Redes de Computadores', 'Protocolos e arquiteturas de rede', 'd0e0f2a3-a09c-464a-06c5-ae0676fcdbd6'),
('66666666-a1b2-c3d4-e5f6-666666666666', 'Matemática', 'Equações Diferenciais e Integrais 2', 'a3b3c5d6-d36f-431d-d3f8-da39a9cff939');

--------------------------------------------------------------------------------
-- PASSO 3: Inserir os PROFESSORES
-- Inserimos os dados de cada professor. Note que limpamos os telefones manualmente.
--------------------------------------------------------------------------------

INSERT INTO professor (id, nome, email, telefone, curso) VALUES
('70a12cbd-8f69-4acf-854f-6c375a98a399', 'Dr. João Silva', 'joao.silva@univ.edu', '75999990001', 'Ciência da Computação'),
('fdfb4e3b-f70a-4ea9-a18a-82cf60152f7c', 'Dra. Maria Santos', 'maria.santos@univ.edu', '75999990002', 'Engenharia de Software'),
('40230056-345d-486e-93a9-3189f60845f8', 'Dr. Carlos Oliveira', 'carlos.oliveira@univ.edu', '75999990003', 'Ciência da Computação'),
('1506b064-72e9-4915-bd89-66b1d5f5285c', 'Dra. Ana Costa', 'ana.costa@univ.edu', '75999990004', 'Sistemas de Informação'),
('e706ce6e-4dc9-4efc-8e00-ceee8326f87d', 'Dr. Pedro Lima', 'pedro.lima@univ.edu', '75999990005', 'Redes de Computadores'),
('d6bde0a2-3ca5-4df8-9fec-f40a2260506d', 'Dr. Angelo Duarte', 'duarte@uefs.com', '75999999999', 'Biologia'),
('ef262d65-73cc-40e3-af2a-304d24ede73c', 'Dr. Elinaldo Santos', 'elinaldo@uefs.br', '75992626262', 'Engenharia de Computação'),
('fbd4f444-d8a2-44e8-bedf-562208e0cb16', 'Dr. Vinícius Maciel', 'maciel@gmail.com', '75999999998', 'Carpintaria');


--------------------------------------------------------------------------------
-- PASSO 4: CONECTAR Professores com suas ÁREAS DE INTERESSE
-- Esta é a parte mais repetitiva. Para cada interesse de cada professor,
-- criamos uma linha que conecta o ID do professor com o ID da área.
--------------------------------------------------------------------------------

-- Interesses do Dr. João Silva
INSERT INTO professor_has_area_interesse (id_professor, area_pesquisa) VALUES
('70a12cbd-8f69-4acf-854f-6c375a98a399', 'a1f1b3b4-1a93-4e31-9f3c-1b7d8d6a3a78'), -- Inteligência Artificial
('70a12cbd-8f69-4acf-854f-6c375a98a399', 'b2c2d4e5-2b84-4f22-8e4d-2c8e9e7b4b89'); -- Machine Learning

-- Interesses da Dra. Maria Santos
INSERT INTO professor_has_area_interesse (id_professor, area_pesquisa) VALUES
('fdfb4e3b-f70a-4ea9-a18a-82cf60152f7c', 'd4e4f6a7-4d66-4c04-6c6f-4ea0109d6da0'), -- Desenvolvimento de Software
('fdfb4e3b-f70a-4ea9-a18a-82cf60152f7c', 'e5f5a7b8-5e57-4b95-5b70-5fb121ae7eb1'); -- Metodologias Ágeis

-- Interesses do Dr. Carlos Oliveira
INSERT INTO professor_has_area_interesse (id_professor, area_pesquisa) VALUES
('40230056-345d-486e-93a9-3189f60845f8', 'f6a6b8c9-6f48-4a86-4a81-6ac232bf8fc2'), -- Computação Gráfica
('40230056-345d-486e-93a9-3189f60845f8', 'a7b7c9d0-7a39-4977-3992-7bd343ca9ad3'); -- Realidade Virtual

-- Interesses da Dra. Ana Costa
INSERT INTO professor_has_area_interesse (id_professor, area_pesquisa) VALUES
('1506b064-72e9-4915-bd89-66b1d5f5285c', 'b8c8d0e1-8b2a-4868-28a3-8ce454dbabf4'), -- Banco de Dados
('1506b064-72e9-4915-bd89-66b1d5f5285c', 'c9d9e1f2-9c1b-4759-17b4-9df565ecbca5'); -- Big Data

-- Interesses do Dr. Pedro Lima
INSERT INTO professor_has_area_interesse (id_professor, area_pesquisa) VALUES
('e706ce6e-4dc9-4efc-8e00-ceee8326f87d', 'e1f1a3b4-b18d-453b-f5d6-bf1787adce17'), -- Segurança
('e706ce6e-4dc9-4efc-8e00-ceee8326f87d', 'f2a2b4c5-c27e-442c-e4e7-cf2898beed28'); -- Protocolos de Rede

-- Interesses do Dr. Angelo Duarte
INSERT INTO professor_has_area_interesse (id_professor, area_pesquisa) VALUES
('d6bde0a2-3ca5-4df8-9fec-f40a2260506d', 'b8c8d0e1-8b2a-4868-28a3-8ce454dbabf4'), -- Banco de Dados
('d6bde0a2-3ca5-4df8-9fec-f40a2260506d', 'c3d3e5f6-3c75-4d13-7d5e-3d9f0f8c5c9a'); -- Engenharia de Software

-- Interesses do Dr. Elinaldo Santos
INSERT INTO professor_has_area_interesse (id_professor, area_pesquisa) VALUES
('ef262d65-73cc-40e3-af2a-304d24ede73c', 'b8c8d0e1-8b2a-4868-28a3-8ce454dbabf4'), -- Banco de Dados
('ef262d65-73cc-40e3-af2a-304d24ede73c', 'c3d3e5f6-3c75-4d13-7d5e-3d9f0f8c5c9a'), -- Engenharia de Software
('ef262d65-73cc-40e3-af2a-304d24ede73c', 'd0e0f2a3-a09c-464a-06c5-ae0676fcdbd6'); -- Redes de Computadores

-- Interesses do Dr. Vinícius Maciel
INSERT INTO professor_has_area_interesse (id_professor, area_pesquisa) VALUES
('fbd4f444-d8a2-44e8-bedf-562208e0cb16', 'b8c8d0e1-8b2a-4868-28a3-8ce454dbabf4'), -- Banco de Dados
('fbd4f444-d8a2-44e8-bedf-562208e0cb16', 'c3d3e5f6-3c75-4d13-7d5e-3d9f0f8c5c9a'); -- Engenharia de Software


--------------------------------------------------------------------------------
-- PASSO 5: CONECTAR Professores com suas LINHAS DE PESQUISA
-- Finalmente, conectamos os professores às suas linhas de pesquisa, usando
-- os IDs do PASSO 2 e PASSO 3.
--------------------------------------------------------------------------------

INSERT INTO professor_has_linha_pesquisa (id_professor, id_linha_pesquisa) VALUES
('70a12cbd-8f69-4acf-854f-6c375a98a399', '11111111-a1b2-c3d4-e5f6-111111111111'), -- Dr. João Silva -> Inteligência Artificial
('fdfb4e3b-f70a-4ea9-a18a-82cf60152f7c', '22222222-a1b2-c3d4-e5f6-222222222222'), -- Dra. Maria Santos -> Engenharia de Software
('40230056-345d-486e-93a9-3189f60845f8', '33333333-a1b2-c3d4-e5f6-333333333333'), -- Dr. Carlos Oliveira -> Computação Gráfica
('1506b064-72e9-4915-bd89-66b1d5f5285c', '44444444-a1b2-c3d4-e5f6-444444444444'), -- Dra. Ana Costa -> Banco de Dados
('e706ce6e-4dc9-4efc-8e00-ceee8326f87d', '55555555-a1b2-c3d4-e5f6-555555555555'), -- Dr. Pedro Lima -> Redes de Computadores
('fbd4f444-d8a2-44e8-bedf-562208e0cb16', '11111111-a1b2-c3d4-e5f6-111111111111'); -- Dr. Vinícius Maciel -> Inteligência Artificial