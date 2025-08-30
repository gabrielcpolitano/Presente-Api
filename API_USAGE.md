# API PIX - Instruções de Uso

## Configuração

A API está configurada com suas credenciais:
- **MercadoPago Token**: APP_USR-8069564433357692-083003-24d6800f4e506c0095dc3b12e976dd63-617282063
- **Banco PostgreSQL**: postgresql://bqrdjune:nifvcxmmdsapgucosrrq@alpha.mkdb.sh:5432/zrtlmoxa
- **Webhook**: centralizando.com

## Endpoints Disponíveis

### 1. Criar Pagamento PIX
**POST** `/criar_pagamento.php`

```json
{
  "valor": 20
}
```

**Resposta:**
```json
{
  "id": "124293507120",
  "status": "pending",
  "valor": 20,
  "created_at": "2025-08-30 22:53:23",
  "qr_code": "00020126360014br.gov.bcb.pix...",
  "qr_code_base64": "iVBORw0KGgoAAAANSUhEUgAAB...",
  "ticket_url": "https://www.mercadopago.com.br/payments/..."
}
```

### 2. Consultar Status do Pagamento
**GET** `/consultar_status.php?id=124293507120`

**Resposta:**
```json
{
  "id": "124293507120",
  "valor": 20,
  "status": "pending",
  "mensagemLiberada": false,
  "created_at": "2025-08-30 22:53:23.974483",
  "updated_at": "2025-08-30 22:53:23.974483",
  "mensagem": "Aguardando confirmação do pagamento PIX"
}
```

### 3. Webhook (para MercadoPago)
**POST** `/webhook.php`

Recebe notificações automáticas do MercadoPago quando o status do pagamento muda.

## Valores Permitidos

- **Valores fixos**: R$ 20, 40, 60, 80, 100
- **Valores personalizados**: Mínimo R$ 100

## Para Hospedagem

1. Faça upload dos arquivos para seu servidor
2. Configure o servidor web (Apache/Nginx) para servir os arquivos PHP
3. Configure o URL do webhook no MercadoPago: `https://centralizando.com/api/webhook`
4. Execute o arquivo `setup_database.php` uma vez para criar as tabelas

## Estrutura dos Arquivos

- `init.php` - Configurações gerais e conexão com banco
- `criar_pagamento.php` - Endpoint para criar pagamentos
- `consultar_status.php` - Endpoint para consultar status
- `webhook.php` - Endpoint para receber notificações
- `setup_database.php` - Script para criar tabelas do banco
- `.htaccess` - Configuração de URLs amigáveis (opcional)