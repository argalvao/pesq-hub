@extends('emails.template')

@section('title', 'Contato de Estudante - PesqHub UEFS')

@section('content')
<div style="padding: 40px 20px;">
    <h2 style="color: #2c5282; margin-bottom: 30px; text-align: center;">
        📧 Novo Contato de Estudante
    </h2>
    
    <div style="background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); padding: 25px; border-radius: 8px; margin-bottom: 30px;">
        <h3 style="color: #2d3748; margin-bottom: 20px; font-size: 18px;">
            👨‍🎓 Informações do Estudante
        </h3>
        
        <div style="margin-bottom: 15px;">
            <strong style="color: #4a5568;">Nome:</strong>
            <span style="color: #2d3748;">{{ $nomeEstudante }}</span>
        </div>
        
        <div style="margin-bottom: 15px;">
            <strong style="color: #4a5568;">E-mail:</strong>
            <span style="color: #2d3748;">{{ $emailEstudante }}</span>
        </div>
        
        @if(isset($instituicao))
        <div style="margin-bottom: 15px;">
            <strong style="color: #4a5568;">Instituição:</strong>
            <span style="color: #2d3748;">{{ $instituicao }}</span>
        </div>
        @endif
        
        @if(isset($curso))
        <div style="margin-bottom: 15px;">
            <strong style="color: #4a5568;">Curso:</strong>
            <span style="color: #2d3748;">{{ $curso }}</span>
        </div>
        @endif
    </div>
    
    <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 25px; border-radius: 8px; margin-bottom: 30px;">
        <h3 style="color: #2d3748; margin-bottom: 20px; font-size: 18px;">
            💬 Mensagem
        </h3>
        
        <div style="color: #4a5568; line-height: 1.6; font-size: 16px;">
            {!! nl2br(e($mensagem)) !!}
        </div>
    </div>
    
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center;">
        <h3 style="margin-bottom: 15px; font-size: 16px;">
            📬 Como Responder
        </h3>
        
        <p style="margin-bottom: 20px; font-size: 14px; opacity: 0.9;">
            Para responder a este estudante, utilize o e-mail:
        </p>
        
        <div style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; font-family: monospace; font-size: 16px; font-weight: bold;">
            {{ $emailEstudante }}
        </div>
        
        <p style="margin-top: 15px; font-size: 12px; opacity: 0.8;">
            Este e-mail foi enviado através do sistema PesqHub UEFS
        </p>
    </div>
    
    @if(isset($dataEnvio))
    <div style="text-align: center; margin-top: 20px; color: #718096; font-size: 12px;">
        Enviado em {{ $dataEnvio }}
    </div>
    @endif
</div>
@endsection
