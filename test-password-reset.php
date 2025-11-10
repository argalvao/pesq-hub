<?php
// Teste de recuperação de senha
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\PasswordResetController;
use App\Services\DatabaseService;
use App\Services\EmailService;
use Illuminate\Http\Request;

try {
    $databaseService = new DatabaseService();
    $emailService = new EmailService();
    $controller = new PasswordResetController($databaseService, $emailService);
    
    // Criar request mockado
    $request = new Request();
    $request->merge(['email' => 'ana.estudante@gmail.com']);
    
    echo "Testando recuperação de senha para: ana.estudante@gmail.com\n";
    
    $response = $controller->sendToken($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Resposta: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
