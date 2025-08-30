<?php
require 'init.php';

try {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        returnError(400, "Informe o ID do pagamento", "Parâmetro 'id' é obrigatório");
    }
    
    // Buscar no banco de dados
    $stmt = $pdo->prepare("SELECT id_pagamento, valor, status, created_at, updated_at FROM pagamentos WHERE id_pagamento = ?");
    $stmt->execute([$id]);
    $dados = $stmt->fetch();
    
    if (!$dados) {
        returnError(404, "Pagamento não encontrado", "ID do pagamento não existe em nossa base");
    }
    
    // Verificar status atualizado no Mercado Pago
    try {
        $payment = MercadoPago\Payment::find_by_id($id);
        
        // Atualizar status no banco se diferente
        if ($payment->status !== $dados['status']) {
            $update_stmt = $pdo->prepare("UPDATE pagamentos SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id_pagamento = ?");
            $update_stmt->execute([$payment->status, $id]);
            $dados['status'] = $payment->status;
        }
        
    } catch (Exception $e) {
        logError("Erro ao consultar pagamento no Mercado Pago", [
            "payment_id" => $id,
            "error" => $e->getMessage()
        ]);
        // Continue com os dados do banco
    }
    
    // Preparar resposta
    $response = [
        "id" => $dados['id_pagamento'],
        "valor" => floatval($dados['valor']),
        "status" => $dados['status'],
        "mensagemLiberada" => ($dados['status'] === "approved"),
        "created_at" => $dados['created_at'],
        "updated_at" => $dados['updated_at']
    ];
    
    // Adicionar informações específicas por status
    switch ($dados['status']) {
        case 'approved':
            $response['mensagem'] = 'Pagamento aprovado com sucesso!';
            break;
        case 'pending':
            $response['mensagem'] = 'Aguardando confirmação do pagamento PIX';
            break;
        case 'rejected':
            $response['mensagem'] = 'Pagamento rejeitado ou cancelado';
            break;
        case 'cancelled':
            $response['mensagem'] = 'Pagamento cancelado';
            break;
        default:
            $response['mensagem'] = 'Status: ' . $dados['status'];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    logError("Erro geral ao consultar status", [
        "id" => $id ?? 'null',
        "message" => $e->getMessage()
    ]);
    returnError(500, "Erro interno do servidor", "Tente novamente em alguns instantes");
}
?>
