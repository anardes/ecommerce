<?php
session_start();
ob_start();

if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header("Location: produtos.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecommerce");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$msgLogin = $msgRegistro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $senha = md5($_POST['senha']); 

    $stmt = $conn->prepare("SELECT email FROM usuarios WHERE email = ? AND senha = ?");
    $stmt->bind_param("ss", $email, $senha);

    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['logado'] = true;
        $_SESSION['email'] = $email;
        header("Location: produtos.php");
        exit();
    } else {
        $msgLogin = "Email ou senha incorretos.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['registrar'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = md5($_POST['senha']); 

    $sqlVer = "SELECT email FROM usuarios WHERE email = '$email'";
    $resultVer = $conn->query($sqlVer);

    if ($resultVer->num_rows > 0) {
        $msgRegistro = "Este email já está cadastrado.";
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (email, nome, sobrenome, senha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sss", $email, $nome, $senha);

        if ($stmt->execute()) {
            $_SESSION['logado'] = true;
            $_SESSION['email'] = $email;
            header("Location: produtos.php");
            exit();
        } else {
            $msgRegistro = "Erro ao registrar: " . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Seguro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="tab-login" data-bs-toggle="pill" href="#pills-login" role="tab"
                    aria-controls="pills-login" aria-selected="true">Login</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab-register" data-bs-toggle="pill" href="#pills-register" role="tab"
                    aria-controls="pills-register" aria-selected="false">Registrar</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="tab-login">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="loginPassword" name="senha" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Entrar</button>
                </form>
                <?php if ($msgLogin) : ?>
                    <div class="alert alert-danger mt-3"><?= $msgLogin ?></div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="pills-register" role="tabpanel" aria-labelledby="tab-register">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="registerName" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="registerName" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="registerEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="registerPassword" name="senha" required>
                    </div>
                    <button type="submit" name="registrar" class="btn btn-primary">Registrar</button>
                </form>
                <?php if ($msgRegistro) : ?>
                    <div class="alert alert-info mt-3"><?= $msgRegistro ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>