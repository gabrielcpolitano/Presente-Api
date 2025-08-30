<?php
require __DIR__ . '/vendor/autoload.php';

// Seu Access Token do Mercado Pago (painel -> credenciais)
MercadoPago\SDK::setAccessToken("SEU_ACCESS_TOKEN");

// conexão banco
$pdo = new PDO("mysql:host=localhost;dbname=casamento;charset=utf8", "usuario", "senha");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
