<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: index.php");
    exit();
}

$carrinho = isset($_COOKIE['carrinho']) ? unserialize($_COOKIE['carrinho']) : [];

if (empty($carrinho)) {
    $produtos = [];
} else {
    $conn = new mysqli("localhost", "root", "", "ecommerce");
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    $idList = implode(",", array_map('intval', array_keys($carrinho))); 
    $sql = "SELECT id, nomeProduto, preco FROM produtos WHERE id IN ($idList)";
    $result = $conn->query($sql);

    $produtos = [];
    if ($result && $result->num_rows > 0) {
        while ($produto = $result->fetch_assoc()) {
            $produto['quantidade'] = $carrinho[$produto['id']];
            $produtos[] = $produto;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Carrinho</h1>
        <ul class="list-group">
            <?php foreach ($produtos as $produto): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($produto['nomeProduto']) ?></strong> -
                    Quantidade: <?= htmlspecialchars($produto['quantidade']) ?> -
                    Preço unitário: R$ <?= number_format($produto['preco'], 2, ',', '.') ?> -
                    Total: R$ <?= number_format($produto['quantidade'] * $produto['preco'], 2, ',', '.') ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="p-2">
            <a href="shop.php" class="btn btn-primary mt-4">Continuar comprando</a>
            <a href="logout.php" class="btn btn-primary mt-4">Sair</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>