<?php
require 'init.php';

try {
    // Create pagamentos table if it doesn't exist
    $sql = "
        CREATE TABLE IF NOT EXISTS pagamentos (
            id SERIAL PRIMARY KEY,
            id_pagamento VARCHAR(255) UNIQUE NOT NULL,
            valor DECIMAL(10,2) NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE INDEX IF NOT EXISTS idx_pagamentos_id_pagamento ON pagamentos(id_pagamento);
        CREATE INDEX IF NOT EXISTS idx_pagamentos_status ON pagamentos(status);
    ";
    
    $pdo->exec($sql);
    
    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Tabela de pagamentos criada/verificada com sucesso"
    ]);
    
} catch (PDOException $e) {
    logError("Erro ao criar tabela: " . $e->getMessage());
    returnError(500, "Erro ao configurar banco de dados", $e->getMessage());
}
?>
