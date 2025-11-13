<?php
include "conexao.php";

// ⚠️ CORREÇÃO MÍNIMA: Inicializa as variáveis para evitar "Undefined variable" e "Deprecated" warnings.
// O código abaixo garante que $usuario e $senha tenham um valor (mesmo que vazio) antes de serem usados.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ATENÇÃO: Os dados do usuário SÃO RECEBIDOS AQUI.
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
} else {
    // Se a página for acessada diretamente, as variáveis são vazias.
    $usuario = '';
    $senha = '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Resultado do Login - VULNERÁVEL</title>
<style>
/* Estilos CSS */
body { font-family: Arial; padding: 20px; background:#f2f2f2; }
.ok  { color: green; font-weight: bold; }
.err { color: red; font-weight: bold; }
.code {
  background: #fff;
  padding: 10px;
  border-left: 4px solid #ff0000; /* Cor vermelha para indicar vulnerabilidade */
  margin-top: 20px;
  font-family: Consolas, monospace;
}
table {
  background: white;
  border-collapse: collapse;
  margin-top: 20px;
}
table td, table th {
  border: 1px solid #ccc;
  padding: 8px;
}
</style>
</head>
<body>

<h2>Resultado do Login - VULNERÁVEL</h2>

<?php

// A vulnerabilidade reside aqui: A entrada do usuário é processada e usada na query SQL.

// remove aspas após '--' para evitar erro de sintaxe
// NOTA: Esta tentativa de 'limpeza' (sanitização) é INEFICAZ e foi mantida
// para demonstrar a falha em métodos de defesa incompletos.
$usuario = preg_replace("/--'.*/", "-- ", $usuario);
$senha = preg_replace("/--.*/", "-- ", $senha);


// ❌ PONTO CRÍTICO DE VULNERABILIDADE: Concatenação direta da string SQL
$sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND senha='$senha'";


echo "<div class='code'><b>Consulta executada (VULNERÁVEL):</b><br>$sql</div>";

$result = $conn->query($sql);

if (!$result) {
  die("<p class='err'>Erro na query: " . $conn->error . "</p>");
}

if ($result->num_rows > 0) {
  echo "<p class='ok'>Login autorizado! ✔️</p>";
  echo "<p>Registros retornados: <b>{$result->num_rows}</b></p>";
} else {
  echo "<p class='err'>Usuário ou senha incorretos ❌</p>";
  echo "<p>Registros retornados: <b>0</b></p>";
}

// LISTAR OS USUÁRIOS RETORNADOS
if ($result->num_rows > 0) {
  echo "<h3>Usuários retornados:</h3>";
  echo "<table><tr><th>ID</th><th>Usuário</th><th>Senha</th></tr>";

  while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['usuario']}</td>
        <td>{$row['senha']}</td>
       </tr>";
  }

  echo "</table>";
}

// DETECTAR SQL INJECTION (Lógica de detecção original mantida)
$injection_detectado = false;

$payloads = ["'", "\"", "--", "OR", "or", "1=1", "='1'='1'"];

foreach ($payloads as $p) {
  if (str_contains($usuario, $p)) {
    $injection_detectado = true;
    break;
  }
}

// SE TIVER INJECTION → EXIBE A TABELA TODA
if ($injection_detectado) {
  echo "<h3 style='color:blue;'>SQL Injection detectado — exibindo tabela completa!</h3>";

  $sqlAll = "SELECT * FROM usuarios";
  $resultAll = $conn->query($sqlAll);

  echo "<table><tr><th>ID</th><th>Usuário</th><th>Senha</th></tr>";

  while ($row = $resultAll->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['usuario']}</td>
        <td>{$row['senha']}</td>
       </tr>";
  }

  echo "</table>";
}

?>

<p><br><a href="index.html">Voltar</a></p>

</body>
</html>