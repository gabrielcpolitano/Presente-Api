<?php
require 'init.php';

try {
    // Valores fixos permitidos
    $valoresFixos = [20, 40, 60, 80, 100];
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $valor = isset($_POST['valor']) ? floatval($_POST['valor']) : 0;
    } else {
        $valor = isset($input['valor']) ? floatval($input['valor']) : 0;
    }
    
    // Validação do valor
    if ($valor <= 0) {
        returnError(400, "Informe um valor válido", "O valor deve ser maior que zero");
    }
    
    // Se não for um valor fixo, precisa ser >= 100
    if (!in_array($valor, $valoresFixos) && $valor < 100) {
        returnError(400, "Valor mínimo para presente personalizado é R$100", 
                   "Valores permitidos: " . implode(", ", $valoresFixos) . " ou mínimo R$100");
    }
    
    // Criar pagamento PIX no Mercado Pago
    $payment = new MercadoPago\Payment();
    $payment->transaction_amount = $valor;
    $payment->description = "Presente de casamento";
    $payment->payment_method_id = "pix";
    
    // Configure payer
    $payer = new MercadoPago\Payer();
    $payer->email = "convidado@example.com";
    $payment->payer = $payer;
    
    // Configure notification URL
    $payment->notification_url = "https://centralizando.com/api/webhook";
    
    // Save payment
    if (!$payment->save()) {
        logError("Erro ao criar pagamento no Mercado Pago", [
            "valor" => $valor,
            "errors" => $payment->getLastApiResponse()
        ]);
        returnError(500, "Erro ao processar pagamento", "Tente novamente em alguns instantes");
    }
    
    // Salvar no banco de dados PostgreSQL
    $stmt = $pdo->prepare("INSERT INTO pagamentos (id_pagamento, valor, status) VALUES (?, ?, ?) ON CONFLICT (id_pagamento) DO UPDATE SET valor = EXCLUDED.valor, status = EXCLUDED.status, updated_at = CURRENT_TIMESTAMP");
    $stmt->execute([$payment->id, $valor, $payment->status]);
    
    // Preparar resposta
    $response = [
        "id" => $payment->id,
        "status" => $payment->status,
        "valor" => $valor,
        "created_at" => date('Y-m-d H:i:s')
    ];
    
    // Adicionar dados do PIX se disponíveis
    if (isset($payment->point_of_interaction->transaction_data)) {
        $transaction_data = $payment->point_of_interaction->transaction_data;
        
        if (isset($transaction_data->qr_code)) {
            $response["qr_code"] = $transaction_data->qr_code;
        }
        
        if (isset($transaction_data->qr_code_base64)) {
            $response["qr_code_base64"] = $transaction_data->qr_code_base64;
        }
        
        if (isset($transaction_data->ticket_url)) {
            $response["ticket_url"] = $transaction_data->ticket_url;
        }
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    logError("Erro geral ao criar pagamento", [
        "message" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ]);
    returnError(500, "Erro interno do servidor", "Tente novamente em alguns instantes");
}
?>
