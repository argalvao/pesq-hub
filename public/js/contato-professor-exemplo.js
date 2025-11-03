/**
 * Exemplo de uso do sistema de contato com professores
 * Este arquivo mostra como integrar o sistema de e-mail no frontend
 */

// Exemplo 1: Usando a rota específica /contact-organizador (recomendado)
async function enviarContatoProfessor(dadosContato) {
    try {
        const response = await fetch('/contact-professor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                email_professor: dadosContato.emailProfessor,
                nome_professor: dadosContato.nomeProfessor,
                nome_estudante: dadosContato.nomeEstudante,
                email_estudante: dadosContato.emailEstudante,
                mensagem: dadosContato.mensagem,
                curso_estudante: dadosContato.cursoEstudante, // opcional
                assunto: dadosContato.assunto // opcional
            })
        });

        const resultado = await response.json();

        if (resultado.success) {
            console.log('✅ E-mail enviado com sucesso!', resultado);
            // Mostrar notificação de sucesso
            mostrarNotificacao('E-mail enviado com sucesso!', 'sucesso');
        } else {
            console.error('❌ Erro ao enviar e-mail:', resultado.message);
            // Mostrar notificação de erro
            mostrarNotificacao(resultado.message, 'erro');
        }

        return resultado;
    } catch (error) {
        console.error('❌ Erro na requisição:', error);
        mostrarNotificacao('Erro de conexão. Tente novamente.', 'erro');
        return { success: false, error: error.message };
    }
}

// Exemplo 2: Usando a rota genérica /send-email
async function enviarContatoProfessorGenerico(dadosContato) {
    try {
        const response = await fetch('/send-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                destinatario: dadosContato.emailProfessor,
                template: 'contato-com-organizador',
                assunto: dadosContato.assunto || `Contato de ${dadosContato.nomeEstudante} via PesqHub UEFS`,
                remetente: dadosContato.emailEstudante,
                nome_remetente: `${dadosContato.nomeEstudante} - PesqHub UEFS`,
                dados: {
                    nome_professor: dadosContato.nomeProfessor,
                    nome_estudante: dadosContato.nomeEstudante,
                    email_estudante: dadosContato.emailEstudante,
                    mensagem: dadosContato.mensagem,
                    curso_estudante: dadosContato.cursoEstudante
                }
            })
        });

        const resultado = await response.json();
        return resultado;
    } catch (error) {
        console.error('❌ Erro na requisição:', error);
        return { success: false, error: error.message };
    }
}

// Exemplo de uso no modal/formulário
function configurarModalContato() {
    const modalContato = document.getElementById('modalContato');
    const formContato = document.getElementById('formContato');

    formContato.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Coletar dados do formulário
        const dadosContato = {
            emailProfessor: document.getElementById('emailProfessor').value,
            nomeProfessor: document.getElementById('nomeProfessor').value,
            nomeEstudante: document.getElementById('nomeEstudante').value,
            emailEstudante: document.getElementById('emailEstudante').value,
            mensagem: document.getElementById('mensagem').value,
            cursoEstudante: document.getElementById('cursoEstudante').value,
            assunto: document.getElementById('assunto').value
        };

        // Mostrar loading
        const btnEnviar = document.getElementById('btnEnviar');
        btnEnviar.textContent = 'Enviando...';
        btnEnviar.disabled = true;

        // Enviar e-mail
        const resultado = await enviarContatoProfessor(dadosContato);

        // Restaurar botão
        btnEnviar.textContent = 'Enviar Mensagem';
        btnEnviar.disabled = false;

        if (resultado.success) {
            // Fechar modal e limpar formulário
            modalContato.style.display = 'none';
            formContato.reset();
        }
    });
}

// Função auxiliar para notificações
function mostrarNotificacao(mensagem, tipo) {
    // Implementar sua lógica de notificação aqui
    if (tipo === 'sucesso') {
        alert('✅ ' + mensagem);
    } else {
        alert('❌ ' + mensagem);
    }
}

// Exemplo de estrutura de dados esperada
const exemploContato = {
    emailProfessor: 'organizador@uefs.br',
    nomeProfessor: 'Dr. João Silva',
    nomeEstudante: 'Maria Santos',
    emailEstudante: 'maria.santos@estudante.uefs.br',
    mensagem: 'Olá organizador, gostaria de saber mais sobre suas pesquisas na área de...',
    cursoEstudante: 'Engenharia de Computação', // opcional
    assunto: 'Interesse em pesquisa' // opcional
};

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', configurarModalContato);
