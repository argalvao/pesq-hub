# PesqHub - Sistema de Gestão de Pesquisa Acadêmica

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?style=flat-square&logo=docker)](https://www.docker.com/)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php)](https://php.net)
[![Google Sheets](https://img.shields.io/badge/Google%20Sheets-Database-34A853?style=flat-square&logo=google-sheets)](https://sheets.google.com)

## 📖 Sobre o Projeto

O **PesqHub** é um sistema web inovador desenvolvido na **UEFS - Universidade Estadual de Feira de Santana** para facilitar a gestão e visualização de informações de pesquisa acadêmica. O projeto proporciona uma solução acessível e colaborativa para instituições de ensino e pesquisa.

### 🎯 Objetivos

- **Centralizar informações** de professores, estudantes e linhas de pesquisa
- **Facilitar a descoberta** de oportunidades de pesquisa
- **Promover colaboração** entre pesquisadores
- **Simplificar a gestão** acadêmica através de uma interface web moderna

## 🚀 Funcionalidades

### 👥 **Gestão de Usuários**
- Sistema de autenticação com três níveis de permissão
- **Administradores**: Acesso total ao sistema
- **Professores**: Gestão do perfil e pesquisas
- **Estudantes**: Visualização e busca de oportunidades

### 🔬 **Gestão de Pesquisa**
- Cadastro e edição de linhas de pesquisa
- Perfis detalhados de professores com áreas de interesse
- Sistema de busca e filtros avançados
- Visualização interativa de dados

### 📊 **Dashboard Interativo**
- Painéis personalizados por tipo de usuário
- Métricas em tempo real
- Interface responsiva e moderna
- API REST para integração com outros sistemas

## 🛠️ Tecnologias Utilizadas

### **Backend**
- **Laravel 11** - Framework PHP moderno e robusto
- **PHP 8.3** - Linguagem de programação
- **Google Sheets** - Banco de dados em nuvem
- **Apache 2.4** - Servidor web

### **Frontend**
- **Alpine.js** - Framework JavaScript reativo
- **Tailwind CSS** - Framework CSS utilitário
- **Blade Templates** - Sistema de templates do Laravel

### **DevOps & Infraestrutura**
- **Docker** - Containerização da aplicação
- **Docker Compose** - Orquestração de containers
- **Git** - Controle de versão

### **Integrações**
- **Google Sheets** - Armazenamento e sincronização de dados

## � Documentação Técnica

Para informações detalhadas sobre arquitetura, desenvolvimento e especificações técnicas, consulte nossa **documentação completa**:

### 📖 [**DOCUMENTAÇÃO TÉCNICA COMPLETA**](DOCUMENTACAO.md)

A documentação técnica inclui:

#### 📊 **Engenharia de Software**
- **📋 Documento de Requisitos** - Requisitos funcionais e não funcionais
- **🎯 Especificação Funcional** - Casos de uso e regras de negócio
- **🏗️ Arquitetura e Design** - Padrões, diagramas e estrutura
- **🧪 Plano de Testes** - Estratégias e cenários de teste

#### 🛠️ **Documentação Técnica**
- **🎯 Visão Geral** - Objetivos e funcionalidades
- **🏗️ Arquitetura** - Estrutura em camadas e módulos
- **🔄 Fluxos de Dados** - Jornadas dos usuários
- **📡 API REST** - Documentação OpenAPI/Swagger
- **🔧 Serviços** - PHPDoc dos principais serviços
- **🗄️ Banco de Dados** - Schema e relacionamentos

#### 🚀 **Desenvolvimento**
- **⚙️ Instalação** - Setup completo do ambiente
- **🔧 Configuração** - Variáveis e dependências
- **🐛 Troubleshooting** - Resolução de problemas comuns
- **🤝 Contribuição** - Guias para desenvolvedores

### 🎯 **Para Desenvolvedores**

Se você é um **novo desenvolvedor** no projeto, recomendamos começar por:

1. **[Visão Geral do Sistema](DOCUMENTACAO.md#visão-geral-do-sistema)** - Entenda o propósito
2. **[Requisitos Funcionais](DOCUMENTACAO.md#requisitos-funcionais)** - Conheça as funcionalidades
3. **[Arquitetura](DOCUMENTACAO.md#arquitetura-do-sistema)** - Compreenda a estrutura
4. **[Instalação](DOCUMENTACAO.md#instalação-e-configuração)** - Configure o ambiente

### 📋 **Para Gestores de Projeto**

Para **acompanhamento de projeto**, consulte:

1. **[Documento de Requisitos](DOCUMENTACAO.md#documento-de-requisitos)** - Status das funcionalidades
2. **[Plano de Testes](DOCUMENTACAO.md#plano-de-testes)** - Qualidade e cobertura
3. **[Especificações Técnicas](DOCUMENTACAO.md#especificação-funcional)** - Detalhes de implementação

---

## �📋 Pré-requisitos

- **Docker** 20.10+ e **Docker Compose** 2.0+
- **Git** para clonagem do repositório

## 🔧 Instalação e Configuração

### 1. **Clone o Repositório**
```bash
git clone git@github.com:argalvao/pesq-hub.git
cd pesq-hub
```

```bash
# Construir e iniciar os containers
docker-compose up -d --build

# Verificar se os containers estão rodando
docker-compose ps

# Acompanhar os logs
docker-compose logs -f
```

### 3. **Inicialização Automática**

Na primeira execução, o sistema irá:
- Criar automaticamente as abas necessárias na planilha
- Inserir dados de exemplo
- Criar usuários padrão com credenciais

## 🔐 Credenciais Padrão

Após a primeira execução, as seguintes credenciais estarão disponíveis:

| Tipo | Email | Senha | Nível |
|------|-------|-------|-------|
| **Admin** | admin@pesqhub.com | admin123 | 1 |
| **Professor** | joao.silva@univ.edu | professor123 | 2 |
| **Estudante** | ana.estudante@gmail.com | estudante123 | 3 |

## 🌐 Acessando a Aplicação

- **URL Principal**: http://localhost:8001
- **Login**: http://localhost:8001/login
- **API**: http://localhost:8001/api/data

## 📊 Estrutura do Banco de Dados (Google Sheets)

### **Aba: usuarios**
| Coluna | Tipo | Descrição |
|--------|------|-----------|
| A | ID | Identificador único |
| B | Nome | Nome completo |
| C | Email | Email de login |
| D | Senha | Hash da senha |
| E | Nível | Nível de permissão (1-3) |
| F | Ativo | Status ativo/inativo |
| G | Data Criação | Data de criação |

### **Aba: professores**
| Coluna | Tipo | Descrição |
|--------|------|-----------|
| A | ID | Identificador único |
| B | Nome | Nome completo |
| C | Email | Email de contato |
| D | Telefone | Telefone |
| E | Curso | Curso/Departamento |
| F | Áreas | Áreas de interesse |
| G | Linhas Pesquisa | IDs das linhas de pesquisa |

### **Aba: linhas_pesquisa**
| Coluna | Tipo | Descrição |
|--------|------|-----------|
| A | ID | Identificador único |
| B | Nome | Nome da linha |
| C | Descrição | Descrição detalhada |

## 🐳 Comandos Docker Úteis

```bash
# Parar os containers
docker-compose down

# Reconstruir os containers
docker-compose up -d --build

# Ver logs em tempo real
docker-compose logs -f

# Acessar o container da aplicação
docker exec -it pesq-hub-app bash

# Limpar cache do Laravel
docker exec pesq-hub-app php artisan optimize:clear

# Verificar status dos containers
docker-compose ps
```

## 🔧 Comandos Laravel Úteis

```bash
# Dentro do container (docker exec -it pesq-hub-app bash)

# Limpar cache
php artisan optimize:clear

# Acessar o console interativo
php artisan tinker

# Ver rotas disponíveis
php artisan route:list

# Verificar configuração
php artisan config:show
```

## 📁 Estrutura do Projeto

```
pesq-hub/
├── app/                        # Código da aplicação Laravel
│   ├── Http/Controllers/       # Controllers
│   ├── Models/                 # Models (não utilizados - Google Sheets)
│   └── Services/               # Serviços (GoogleSheetsService, UserService)
├── config/                     # Arquivos de configuração
├── docker/                     # Arquivos Docker
│   ├── apache/                 # Configuração Apache
│   └── start.sh               # Script de inicialização
├── public/                     # Arquivos públicos
├── resources/                  # Views, CSS, JS
│   ├── css/
│   ├── js/
│   └── views/
├── routes/                     # Definição de rotas
├── storage/                    # Storage Laravel
│   └── app/
│       └── credenciais.json   # Credenciais Google API (Solicitar o arquivo)
├── docker-compose.yml         # Configuração Docker Compose
├── Dockerfile                 # Imagem Docker
└── README.md                  # Este arquivo
└── .env                       # Configurações de credenciais (Solicitar o arquivo)
```

## 🔧 Desenvolvimento

### **Ambiente de Desenvolvimento**

Para desenvolvimento local, você pode:

1. **Modificar código**: Os arquivos são montados como volumes
2. **Debug**: Logs disponíveis via `docker-compose logs -f`
3. **Testes**: Execute dentro do container

### **Adicionando Novos Recursos**

1. **Controllers**: Adicione em `app/Http/Controllers/`
2. **Services**: Adicione em `app/Services/`
3. **Views**: Adicione em `resources/views/`
4. **Rotas**: Defina em `routes/web.php`

### **Modificando Estrutura da Planilha**

Para adicionar novas colunas ou abas:
1. Modifique os serviços em `app/Services/`
2. Atualize os comandos de inicialização
3. Execute `docker-compose down && docker-compose up --build`

## 🚨 Solução de Problemas

### **Container não inicia**
```bash
# Verificar logs
docker-compose logs

# Reconstruir completamente
docker-compose down
docker system prune -f
docker-compose up --build
```

### **Erro de permissões**
```bash
# Corrigir permissões do storage
docker exec pesq-hub-app chown -R www-data:www-data /var/www/html/storage
docker exec pesq-hub-app chmod -R 775 /var/www/html/storage
```

### **Erro no Google Sheets**
1. Verifique se o arquivo `credenciais.json` está presente
2. Confirme se a planilha está acessível
3. Verifique a conectividade com o Google Sheets

### **Problemas de Login**
1. Verifique se os usuários foram criados na planilha
2. Confirme as credenciais padrão
3. Limpe o cache: `docker exec pesq-hub-app php artisan optimize:clear`

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 👨‍💻 Autor

**Abel Galvão**
- GitHub: [@argalvao](https://github.com/argalvao)
- Email: abel.ramalho18@gmail.com

## 🙏 Agradecimentos

- Laravel Framework
- Google Sheets
- Docker Community
- Comunidade Open Source

---

<div align="center">
  <p>Desenvolvido com ❤️ para a comunidade acadêmica</p>
  <p><a href="https://github.com/argalvao/pesq-hub">⭐ Dê uma estrela se este projeto foi útil!</a></p>
</div>
