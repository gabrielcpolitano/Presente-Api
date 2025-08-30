<?php
require 'init.php';

$data = file_get_contents("php://input");
$json = json_decode($data, true);

if (isset($json['data']['id'])) {
    $payment_id = $json['data']['id'];
    $payment = MercadoPago\Payment::find_by_id($payment_id);

    // atualiza banco
    $stmt = $pdo->prepare("UPDATE pagamentos SET status=? WHERE id_pagamento=?");
    $stmt->execute([$payment->status, $payment->id]);

    http_response_code(200);
    echo "OK";
} else {
    http_response_code(400);
    echo "Payload inválido";
}
