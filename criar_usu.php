<?php
include('bd.php'); 

$email = 'usuario@gmail.com';
$senha = password_hash('senha', PASSWORD_DEFAULT); 

$stmt = $pdo->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
$stmt->execute([$email, $senha]);

echo "Usuário criado com sucesso!";