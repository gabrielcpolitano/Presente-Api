<?php
require 'init.php';

// valores fixos
$valoresFixos = [20, 40, 60, 80, 100];

$valor = isset($_POST['valor']) ? floatval($_POST['valor']) : 0;

// validação
if ($valor <= 0) {
    http_response_code(400);
    echo json_encode(["erro" => "Informe um valor válido"]);
    exit;
}

// se não for fixo, precisa ser >= 100
if (!in_array($valor, $valoresFixos) && $valor < 100) {
    http_response_code(400);
    echo json_encode(["erro" => "Valor mínimo para presente personalizado é R$100"]);
    exit;
}

// cria pagamento Pix
$payment = new MercadoPago\Payment();
$payment->transaction_amount = $valor;
$payment->description = "Presente de casamento";
$payment->payment_method_id = "pix";
$payment->payer = ["email" => "convidado@example.com"];
$payment->notification_url = "https://seusite.com/api/webhook.php";
$payment->save();

// salva no banco
$stmt = $pdo->prepare("INSERT INTO pagamentos (id_pagamento, valor, status) VALUES (?, ?, ?)");
$stmt->execute([$payment->id, $valor, $payment->status]);

// resposta
echo json_encode([
    "id" => $payment->id,
    "status" => $payment->status,
    "valor" => $valor,
    "qr_code" => $payment->point_of_interaction->transaction_data->qr_code,
    "qr_code_base64" => $payment->point_of_interaction->transaction_data->qr_code_base64,
    "ticket_url" => $payment->point_of_interaction->transaction_data->ticket_url
]);
