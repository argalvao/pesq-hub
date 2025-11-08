# Filtro de Dados por Curso do Organizador

## Descrição
Foi implementado um sistema de filtros que permite que organizadores visualizem apenas os dados relacionados ao seu curso específico. Este sistema afeta os seguintes endpoints:

## Funcionalidades Implementadas

### 1. Filtro de Professores
- **Endpoint**: `GET /organizador/professores`
- **Comportamento**: Retorna apenas professores do mesmo curso do organizador logado
- **Método no DatabaseService**: `getProfessoresPorCurso($idCurso)`

### 2. Filtro de Áreas de Pesquisa
- **Endpoint**: `GET /organizador/areas-pesquisa`
- **Comportamento**: Retorna apenas áreas de pesquisa que possuem professores do curso do organizador
- **Método no DatabaseService**: `getAreasPesquisaPorCurso($idCurso)`

### 3. Filtro de Linhas de Pesquisa
- **Endpoint**: `GET /organizador/linhas-pesquisa`
- **Comportamento**: Retorna apenas linhas de pesquisa que possuem professores do curso do organizador
- **Método no DatabaseService**: `getLinhasPesquisaPorCurso($idCurso)`

### 4. Filtro de Usuários
- **Endpoint**: `GET /organizador/usuarios`
- **Comportamento**: Retorna apenas usuários do mesmo curso do organizador logado
- **Método no DatabaseService**: `getUsersPorCurso($idCurso)`

## Como Funciona

### Lógica de Filtro
1. O sistema verifica se o usuário logado possui um curso associado (`id_curso`)
2. Se **TEM curso**: aplica o filtro específico do curso
3. Se **NÃO TEM curso**: retorna todos os dados (comportamento de admin)

### Implementação Técnica
```php
private function getUsuarioLogadoComCurso()
{
    $usuarioLogado = \Illuminate\Support\Facades\Session::get('user');
    return $usuarioLogado && isset($usuarioLogado['id_curso']) ? $usuarioLogado : null;
}
```

### Cache Inteligente
Cada filtro por curso possui seu próprio cache para otimizar performance:
- `professores_curso_{$idCurso}_db`
- `areas_pesquisa_curso_{$idCurso}_db`
- `linhas_pesquisa_curso_{$idCurso}_db`
- `usuarios_curso_{$idCurso}_db`

## Endpoints Adicionados

### Debug/Informações do Filtro
- **Endpoint**: `GET /organizador/info-filtro`
- **Descrição**: Retorna informações sobre o estado atual do filtro
- **Resposta**:
```json
{
    "usuario_logado": {...},
    "tem_curso": true,
    "id_curso": "uuid-do-curso",
    "filtro_ativo": true,
    "success": true
}
```

## Alterações nos Arquivos

### Controladores
- **OrganizadorController.php**: Implementação dos filtros em todos os métodos de listagem

### Serviços
- **DatabaseService.php**: Novos métodos para filtrar dados por curso:
  - `getProfessoresPorCurso($idCurso)`
  - `getAreasPesquisaPorCurso($idCurso)`
  - `getLinhasPesquisaPorCurso($idCurso)`
  - `getUsersPorCurso($idCurso)`

### Rotas
- **web.php**: Adicionada rota para usuários e debug do filtro

## Casos de Uso

### Organizador com Curso
- Vê apenas professores do seu curso
- Vê apenas áreas de pesquisa relacionadas ao seu curso
- Vê apenas linhas de pesquisa com professores do seu curso
- Vê apenas usuários do seu curso

### Admin (sem curso específico)
- Vê todos os dados sem filtro
- Mantém funcionalidade completa de administração

## Observações Importantes

1. **Compatibilidade**: O sistema mantém retrocompatibilidade com admins
2. **Performance**: Utiliza cache específico por curso
3. **Flexibilidade**: Fácil de estender para outras entidades
4. **Segurança**: Organizadores só acessam dados de seu curso

## Como Testar

1. **Login como organizador com curso**:
   - Acesse `/organizador/info-filtro` para verificar se o filtro está ativo
   - Teste os endpoints de listagem

2. **Login como admin**:
   - Verifique se todos os dados são retornados sem filtro

3. **Verificar Cache**:
   - Os dados devem ser cacheados separadamente por curso
