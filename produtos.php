<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: index.php");
    exit();
}

// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "ecommerce");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Consulta para buscar os produtos
$sql = "SELECT id, nomeProduto, preco FROM produtos";
$result = $conn->query($sql);

// Adiciona produtos ao carrinho
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_produto'])) {
    $id_produto = $_POST['id_produto'];
    $id_usuario = $_SESSION['id']; // ID do usuário logado

    // Verifica se o produto já existe no carrinho do usuário
    $sql_check = "SELECT * FROM carrinho WHERE id_usuario = ? AND id_produto = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("ii", $id_usuario, $id_produto);
    $stmt->execute();
    $result_check = $stmt->get_result();

    // Se o produto já estiver no carrinho, atualiza a quantidade, senão insere um novo item
    if ($result_check->num_rows > 0) {
        $sql_update = "UPDATE carrinho SET quantidade = quantidade + 1 WHERE id_usuario = ? AND id_produto = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $id_usuario, $id_produto);
        $stmt_update->execute();
    } else {
        $sql_insert = "INSERT INTO carrinho (id_usuario, id_produto, quantidade) VALUES (?, ?, 1)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $id_usuario, $id_produto);
        $stmt_insert->execute();
    }

    header("Location: shop.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class='sticky'>
            <h1 class="mb-4">Produtos</h1>
            <div class="p-2">
                <a href="cart.php" class="btn btn-primary mt-4">Carrinho</a>
                <a href="logout.php" class="btn btn-primary mt-4">Sair</a>
            </div>
        </div>
        <div class="row">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($produto = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="imgs/<?= $produto['id'] ?>.jpg" class="card-img-top" alt="<?= $produto['nomeProduto'] ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($produto['nomeProduto']) ?></h5>
                                <p class="card-text">Preço: R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                                <form method="POST" action="">
                                    <input type="hidden" name="id_produto" value="<?= $produto['id'] ?>">
                                    <button type="submit" name="adicionar_carrinho" class="btn btn-primary">Adicionar ao Carrinho</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Nenhum produto encontrado.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>