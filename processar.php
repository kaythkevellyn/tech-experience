<?php

// 1. Verificação do método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    header('Content-Type: text/html; charset=UTF-8');

    echo '<h1>405 - Método não permitido</h1>';
    echo '<p>Esta página só aceita requisições pelo método POST.</p>';
    echo '<a href="index.html">Voltar ao formulário</a>';

    exit;
}

// 2. Função para contar caracteres sem depender da extensão mbstring
function contarCaracteres($texto) {

    // Usa mb_strlen se a extensão estiver disponível
    if (function_exists('mb_strlen')) {
        return mb_strlen($texto, 'UTF-8');
    }

    // Alternativa para contar caracteres UTF-8
    $quantidade = preg_match_all('/./us', $texto, $correspondencias);

    // Alternativa caso o texto tenha UTF-8 inválido
    return ($quantidade === false) ? strlen($texto) : $quantidade;
}

// 3. Normalização dos dados recebidos
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$experiencia = $_POST['experiencia'] ?? '';
$turno = $_POST['turno'] ?? '';
$oficinas = $_POST['oficinas'] ?? [];
$mensagem = $_POST['mensagem'] ?? '';

// Garantir que os campos simples sejam strings
$nome = is_string($nome) ? trim($nome) : '';
$email = is_string($email) ? trim($email) : '';
$experiencia = is_string($experiencia) ? trim($experiencia) : '';
$turno = is_string($turno) ? trim($turno) : '';
$mensagem = is_string($mensagem) ? trim($mensagem) : '';

// 4. Listas permitidas (Whitelists)
$experienciasPermitidas = [
    'iniciante',
    'intermediario',
    'avancado'
];

$turnosPermitidos = [
    'manha',
    'noite'
];

$oficinasPermitidas = [
    'html_css',
    'js_dom',
    'php_backend'
];

// 5. Array de erros
$erros = [];

// Validação do nome
if (contarCaracteres($nome) < 3) {
    $erros[] = 'O nome deve possuir pelo menos 3 caracteres.';
}

// Validação do e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Digite um endereço de e-mail válido.';
}

// Validação da experiência
if (
    !in_array(
        $experiencia,
        $experienciasPermitidas,
        true
    )
) {
    $erros[] = 'Selecione um nível de experiência válido.';
}

// Validação do turno
if (
    !in_array(
        $turno,
        $turnosPermitidos,
        true
    )
) {
    $erros[] = 'Selecione um turno válido.';
}

// Validação das oficinas
if (!is_array($oficinas)) {
    $erros[] = 'Os dados das oficinas são inválidos.';
} elseif (empty($oficinas)) {
    $erros[] = 'Selecione pelo menos uma oficina.';
} else {
    foreach ($oficinas as $oficina) {
        if (
            !is_string($oficina) ||
            !in_array(
                $oficina,
                $oficinasPermitidas,
                true
            )
        ) {
            $erros[] = 'Uma ou mais oficinas selecionadas são inválidas.';
            break;
        }
    }
}

// Validação da mensagem
if (contarCaracteres($mensagem) > 200) {
    $erros[] = 'A mensagem deve possuir no máximo 200 caracteres.';
}

// 6. Função para escapar os dados e proteger contra XSS
function escapar($dado) {
    return htmlspecialchars(
        (string) $dado,
        ENT_QUOTES,
        'UTF-8'
    );
}

// 7. Tradução dos valores para exibição
$nomesExperiencia = [
    'iniciante' => 'Iniciante',
    'intermediario' => 'Intermediário',
    'avancado' => 'Avançado'
];

$nomesTurno = [
    'manha' => 'Manhã',
    'noite' => 'Noite'
];

$nomesOficinas = [
    'html_css' => 'HTML e CSS',
    'js_dom' => 'JavaScript e DOM',
    'php_backend' => 'PHP e Backend'
];

// 8. Exibição da resposta HTML
header('Content-Type: text/html; charset=UTF-8');

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado da Inscrição - Tech Experience 2026</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>
    <h1>Tech Experience 2026</h1>
    <p>Resultado da inscrição</p>
</header>

<main class="container">

<section class="formulario">

<?php if (!empty($erros)): ?>
    <h2>Não foi possível realizar sua inscrição!</h2>
    <p>Verifique os seguintes problemas:</p>
    <ul>
        <?php foreach ($erros as $erro): ?>
            <li>
                <?= escapar($erro) ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <br>

    <a href="index.html">Voltar ao formulário</a>

<?php else: ?>
    <h2>Inscrição realizada com sucesso!</h2>
    <p>Seus dados foram recebidos e validados.</p>

    <hr>

    <br>

    <h3>Dados do participante:</h3>

    <p>
        <strong>Nome:</strong>
        <?= escapar($nome) ?>
    </p>

    <p>
        <strong>E-mail:</strong>
        <?= escapar($email) ?>
    </p>

    <p>
        <strong>Nível de experiência:</strong>
        <?= escapar($nomesExperiencia[$experiencia]) ?>
    </p>

    <p>
        <strong>Turno:</strong>
        <?= escapar($nomesTurno[$turno]) ?>
    </p>

    <p><strong>Oficinas selecionadas:</strong></p>

    <ul>

        <?php foreach ($oficinas as $oficina): ?>

            <li>
                <?= escapar($nomesOficinas[$oficina]) ?>
            </li>

        <?php endforeach; ?>

    </ul>

    <br>

    <p>
        <strong>Mensagem / Motivação:</strong>
    </p>

    <p>
        <?= nl2br(escapar($mensagem)) ?>
    </p>

    <br>

    <p>Obrigado por participar do Tech Experience 2026!</p>

    <br>

    <a href="index.html">Voltar ao formulário</a>

<?php endif; ?>
</section>

</main>
<footer>

    <p>Tech Experience 2026 - Evento Acadêmico</p>
    <p>Desenvolvimento Web</p>

</footer>
</body>
</html>