<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Exception;

class EmailService
{
    /**
     * Enviar e-mail usando template dinâmico
     *
     * @param string $destinatario
     * @param string $template Nome do template (sem 'emails.' e '.blade.php')
     * @param array $dados Dados para o template
     * @param string|null $assunto Assunto personalizado (opcional)
     * @param string $remetente Email do remetente
     * @param string $nomeRemetente Nome do remetente
     * @return array
     */
    public function enviarEmail(
        string $destinatario,
        string $template,
        array $dados = [],
        ?string $assunto = null,
        string $remetente = 'abel@ecomp.uefs.br',
        string $nomeRemetente = 'PesqHub - UEFS'
    ): array {
        try {
            // Validar e-mail do destinatário
            if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('E-mail do destinatário inválido');
            }

            // Construir caminho do template
            $templatePath = 'emails.' . $template;
            
            // Definir assunto se não fornecido
            if (!$assunto) {
                $assunto = $this->definirAssuntoPorTemplate($template);
            }
            
            // Adicionar assunto aos dados do template
            $dados['assunto'] = $assunto;
            
            // Criar instância de Mailable dinâmica
            $mailable = new class($templatePath, $dados, $remetente, $nomeRemetente, $assunto) extends Mailable {
                public $templatePath;
                public $dados;
                public $remetenteEmail;
                public $remetenteNome;
                public $assunto;

                public function __construct($templatePath, $dados, $remetenteEmail, $remetenteNome, $assunto)
                {
                    $this->templatePath = $templatePath;
                    $this->dados = $dados;
                    $this->remetenteEmail = $remetenteEmail;
                    $this->remetenteNome = $remetenteNome;
                    $this->assunto = $assunto;
                }

                public function build()
                {
                    return $this
                        ->view($this->templatePath)
                        ->with($this->dados)
                        ->from($this->remetenteEmail, $this->remetenteNome)
                        ->subject($this->assunto);
                }
            };

            // Enviar e-mail
            Mail::to($destinatario)->send($mailable);

            return [
                'success' => true,
                'message' => '✅ E-mail enviado com sucesso!',
                'destinatario' => $destinatario,
                'template' => $template,
                'assunto' => $assunto,
                'timestamp' => time()
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => '❌ Falha no envio: ' . $e->getMessage(),
                'error_code' => $e->getCode(),
                'timestamp' => time()
            ];
        }
    }

    /**
     * Definir assunto baseado no template
     *
     * @param string $template
     * @return string
     */
    private function definirAssuntoPorTemplate(string $template): string
    {
        $assuntos = [
            'teste-sistema' => '🧪 Teste do Sistema de E-mails - PesqHub UEFS',
            'contato-com-professor' => '📧 Contato via PesqHub UEFS',
            'contato-professor' => '📧 Contato via PesqHub UEFS',
            'notificacao' => '🔔 Notificação - PesqHub UEFS',
            'boas-vindas' => '👋 Bem-vindo ao PesqHub UEFS',
        ];

        return $assuntos[$template] ?? '📩 Mensagem do PesqHub UEFS';
    }

    /**
     * Enviar e-mail de teste
     *
     * @param string $destinatario
     * @param string $template
     * @return array
     */
    public function enviarTeste(string $destinatario, string $template = 'teste-sistema'): array
    {
        $dadosTemplate = [
            'nome' => 'Usuário Teste',
            'corpo' => 'Este é um e-mail de teste enviado às ' . date('H:i:s') . ' em ' . date('d/m/Y'),
            'mensagem' => 'Teste do sistema de e-mails do PesqHub UEFS'
        ];
        
        $assunto = '🧪 Teste do Sistema - ' . date('H:i:s');
        
        return $this->enviarEmail($destinatario, $template, $dadosTemplate, $assunto);
    }

    /**
     * Enviar e-mail de contato com professor
     *
     * @param string $emailProfessor E-mail do professor destinatário
     * @param string $nomeProfessor Nome do professor
     * @param string $nomeEstudante Nome do estudante remetente
     * @param string $emailEstudante E-mail do estudante remetente
     * @param string $mensagem Mensagem do estudante
     * @param string|null $cursoEstudante Curso do estudante (opcional)
     * @param string|null $assuntoPersonalizado Assunto personalizado (opcional)
     * @return array
     */
    public function enviarContatoProfessor(
        string $emailProfessor,
        string $nomeProfessor,
        string $nomeEstudante,
        string $emailEstudante,
        string $mensagem,
        ?string $cursoEstudante = null,
        ?string $assuntoPersonalizado = null
    ): array {
        $dadosTemplate = [
            'nome_professor' => $nomeProfessor,
            'nome_estudante' => $nomeEstudante,
            'email_estudante' => $emailEstudante,
            'mensagem' => $mensagem
        ];

        // Adicionar curso se fornecido
        if ($cursoEstudante) {
            $dadosTemplate['curso_estudante'] = $cursoEstudante;
        }

        // Definir assunto
        $assunto = $assuntoPersonalizado ?? 'Contato de ' . $nomeEstudante . ' via PesqHub UEFS';
        $dadosTemplate['assunto'] = $assunto;

        return $this->enviarEmail(
            $emailProfessor,
            'contato-com-professor',
            $dadosTemplate,
            $assunto,
            'abel@ecomp.uefs.br', // Sempre usar o e-mail oficial
            'PesqHub UEFS - Sistema de Contatos' // Nome do remetente oficial
        );
    }
    
    /**
     * Verificar se um template existe
     *
     * @param string $template
     * @return bool
     */
    public function templateExiste(string $template): bool
    {
        $templatePath = resource_path('views/emails/' . $template . '.blade.php');
        return file_exists($templatePath);
    }
    
    /**
     * Listar templates disponíveis
     *
     * @return array
     */
    public function listarTemplates(): array
    {
        $emailsPath = resource_path('views/emails');
        
        if (!is_dir($emailsPath)) {
            return [];
        }
        
        $templates = [];
        $files = scandir($emailsPath);
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php' && strpos($file, '.blade.php') !== false) {
                $templateName = str_replace('.blade.php', '', $file);
                if ($templateName !== 'template') { // Excluir template base
                    $templates[] = $templateName;
                }
            }
        }
        
        return $templates;
    }
}
