<?php
// ================================================================
// ACAFEG — Processador do formulário de contato
// ================================================================

header('Content-Type: application/json; charset=utf-8');

// ── Configurações ────────────────────────────────────────────────
define('EMAIL_DESTINO', 'gustavo.jesuino1129@gmail.com');
define('EMAIL_REMETENTE', 'noreply@acafeg.com.br');
define('NOME_SITE', 'ACAFEG');

// ── Só aceita POST ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
    exit;
}

// ── Função de sanitização ────────────────────────────────────────
function limpar(string $valor): string {
    return htmlspecialchars(strip_tags(trim($valor)), ENT_QUOTES, 'UTF-8');
}

// ── Coleta e sanitiza os campos ──────────────────────────────────
$assunto  = limpar($_POST['assunto']  ?? '');
$nome     = limpar($_POST['nome']     ?? '');
$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$telefone = limpar($_POST['telefone'] ?? '');
$cidade   = limpar($_POST['cidade']   ?? '');
$mensagem = limpar($_POST['mensagem'] ?? '');
$lgpd     = isset($_POST['lgpd']) ? true : false;

// ── Validações ───────────────────────────────────────────────────
$erros = [];

if (empty($nome)) {
    $erros['nome'] = 'Por favor, informe seu nome.';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros['email'] = 'Informe um e-mail válido.';
}
if (empty($cidade)) {
    $erros['cidade'] = 'Informe sua cidade.';
}
if (empty($mensagem)) {
    $erros['mensagem'] = 'Por favor, escreva uma mensagem.';
}
if (!$lgpd) {
    $erros['lgpd'] = 'Você precisa aceitar a Política de Privacidade para continuar.';
}

if (!empty($erros)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erros' => $erros]);
    exit;
}

// ── Proteção básica anti-spam (header injection) ─────────────────
foreach ([$nome, $assunto, $cidade] as $campo) {
    if (preg_match('/[\r\n]/', $campo)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'erro' => 'Dados inválidos.']);
        exit;
    }
}

// ── Monta o e-mail ───────────────────────────────────────────────
$assuntoEmail = NOME_SITE . ' — Nova mensagem: ' . ($assunto ?: 'Contato geral');

$corpo = "
<!DOCTYPE html>
<html lang='pt-BR'>
<head>
  <meta charset='UTF-8'>
  <style>
    body        { font-family: Georgia, serif; background: #f4f0eb; margin: 0; padding: 24px; }
    .card       { background: #fff; max-width: 600px; margin: 0 auto; border-radius: 4px;
                  overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
    .topo       { background: #231f20; padding: 28px 32px; }
    .logo       { color: #fff; font-size: 22px; letter-spacing: 4px; text-transform: uppercase; }
    .logo span  { color: #c9a96e; }
    .corpo      { padding: 32px; }
    .chip       { display:inline-block; background:#f4f0eb; color:#6b4b2a;
                  font-size:12px; padding:4px 12px; border-radius:99px;
                  letter-spacing:1px; text-transform:uppercase; margin-bottom:20px; }
    .linha      { display:flex; gap:8px; margin-bottom:12px; font-size:15px; color:#333; }
    .rotulo     { font-weight:bold; color:#6b4b2a; min-width:90px; }
    .divisor    { border:none; border-top:1px solid #e8e0d4; margin:20px 0; }
    .msg        { background:#faf7f3; border-left:3px solid #c9a96e; padding:16px 20px;
                  border-radius:0 4px 4px 0; font-size:15px; line-height:1.7; color:#333; }
    .rodape     { padding:16px 32px; background:#faf7f3; font-size:12px;
                  color:#999; border-top:1px solid #e8e0d4; }
  </style>
</head>
<body>
  <div class='card'>
    <div class='topo'>
      <div class='logo'>ACA<span>FEG</span></div>
    </div>
    <div class='corpo'>
      <span class='chip'>" . htmlspecialchars($assunto ?: 'Contato geral') . "</span>

      <div class='linha'><span class='rotulo'>Nome</span><span>" . htmlspecialchars($nome) . "</span></div>
      <div class='linha'><span class='rotulo'>E-mail</span><span><a href='mailto:" . htmlspecialchars($email) . "' style='color:#6b4b2a'>" . htmlspecialchars($email) . "</a></span></div>
      <div class='linha'><span class='rotulo'>Telefone</span><span>" . ($telefone ?: '—') . "</span></div>
      <div class='linha'><span class='rotulo'>Cidade</span><span>" . htmlspecialchars($cidade) . "</span></div>

      <hr class='divisor'>

      <p style='font-size:13px;color:#999;margin:0 0 8px'>Mensagem</p>
      <div class='msg'>" . nl2br(htmlspecialchars($mensagem)) . "</div>
    </div>
    <div class='rodape'>
      Mensagem enviada em " . date('d/m/Y \à\s H:i') . " pelo formulário do site ACAFEG.
    </div>
  </div>
</body>
</html>
";

// ── Cabeçalhos do e-mail ─────────────────────────────────────────
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: " . NOME_SITE . " <" . EMAIL_REMETENTE . ">\r\n";
$headers .= "Reply-To: " . $nome . " <" . $email . ">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// ── Envia ────────────────────────────────────────────────────────
$enviado = mail(EMAIL_DESTINO, $assuntoEmail, $corpo, $headers);

if ($enviado) {
    echo json_encode(['ok' => true, 'mensagem' => 'E-mail enviado com sucesso.']);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Falha ao enviar o e-mail. Tente novamente.']);
}
