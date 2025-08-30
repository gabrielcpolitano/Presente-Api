<?php
require 'init.php';

try {
    // Log da requisição recebida
    $input = file_get_contents("php://input");
    $headers = getallheaders();
    
    logError("Webhook recebido", [
        "headers" => $headers,
        "body" => $input
    ]);
    
    if (empty($input)) {
        returnError(400, "Payload vazio", "Nenhum dado foi enviado");
    }
    
    $json = json_decode($input, true);
    
    if (!$json) {
        returnError(400, "JSON inválido", "Não foi possível decodificar o payload");
    }
    
    // Verificar se é uma notificação de pagamento
    if (!isset($json['data']['id']) || !isset($json['type'])) {
        returnError(400, "Payload inválido", "Campos obrigatórios não encontrados");
    }
    
    $payment_id = $json['data']['id'];
    $notification_type = $json['type'];
    
    // Processar apenas notificações de pagamento
    if ($notification_type !== 'payment') {
        http_response_code(200);
        echo json_encode(["status" => "ignored", "reason" => "Not a payment notification"]);
        exit;
    }
    
    // Buscar informações do pagamento no Mercado Pago
    try {
        $payment = MercadoPago\Payment::find_by_id($payment_id);
        
        if (!$payment) {
            returnError(404, "Pagamento não encontrado no Mercado Pago", "ID: " . $payment_id);
        }
        
        // Atualizar status no banco de dados
        $stmt = $pdo->prepare("UPDATE pagamentos SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id_pagamento = ?");
        $result = $stmt->execute([$payment->status, $payment->id]);
        
        if ($stmt->rowCount() === 0) {
            logError("Pagamento não encontrado no banco local", [
                "payment_id" => $payment->id,
                "mp_status" => $payment->status
            ]);
        }
        
        // Log do processamento
        logError("Webhook processado com sucesso", [
            "payment_id" => $payment->id,
            "old_status" => $json['data']['status'] ?? 'unknown',
            "new_status" => $payment->status,
            "rows_affected" => $stmt->rowCount()
        ]);
        
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "payment_id" => $payment->id,
            "new_status" => $payment->status,
            "updated" => $stmt->rowCount() > 0
        ]);
        
    } catch (Exception $e) {
        logError("Erro ao processar webhook do Mercado Pago", [
            "payment_id" => $payment_id,
            "error" => $e->getMessage()
        ]);
        returnError(500, "Erro ao consultar pagamento", $e->getMessage());
    }
    
} catch (Exception $e) {
    logError("Erro geral no webhook", [
        "message" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ]);
    returnError(500, "Erro interno do servidor", $e->getMessage());
}
?>
