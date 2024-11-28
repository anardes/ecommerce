CREATE DATABASE ecommerce;
USE ecommerce;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(32)
);

CREATE TABLE produtos (
    id INT PRIMARY KEY,
    nomeProduto VARCHAR(100),
    preco DECIMAL(10, 2)
);

CREATE TABLE carrinho (
    id INT PRIMARY KEY,
    id_usuario INT,
    id_produto INT,
    quantidade INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_produto) REFERENCES produtos(id)
);

INSERT INTO produtos (nomeProduto, id, preco) VALUES
    ('Colar prata coração', 1, 40.50),
    ('Colar dourado coração', 2, 40.50),
    ('Brinco pérola',3, 29.90);