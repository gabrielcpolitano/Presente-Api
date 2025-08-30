<?php
require 'init.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(["erro" => "Informe o id"]);
    exit;
}

$stmt = $pdo->prepare("SELECT valor, status FROM pagamentos WHERE id_pagamento=?");
$stmt->execute([$id]);
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dados) {
    http_response_code(404);
    echo json_encode(["erro" => "Pagamento não encontrado"]);
    exit;
}

echo json_encode([
    "id" => $id,
    "valor" => $dados['valor'],
    "status" => $dados['status'],
    "mensagemLiberada" => ($dados['status'] === "approved")
]);
