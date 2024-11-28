<?php
session_start();
include('bd.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Preparando a consulta para buscar o usuário pelo e-mail
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    // Verificando se o usuário existe e se a senha está correta
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['id_usuario'] = $usuario['id'];
        setcookie("id_usuario", $usuario['id'], time() + 3600); // Cookie com ID do usuário
        header('Location: index.php');
        exit(); // Garantir que o código não continue após o redirecionamento
    } else {
        echo "Credenciais inválidas.";
    }
}
?>

<form method="POST">
    Email: <input type="email" name="email">
    Senha: <input type="password" name="senha">
    <button type="submit">Login</button>
</form>
