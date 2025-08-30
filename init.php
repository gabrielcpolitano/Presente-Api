<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/vendor/autoload.php';

// Configure Mercado Pago with real credentials
MercadoPago\SDK::setAccessToken('APP_USR-8069564433357692-083003-24d6800f4e506c0095dc3b12e976dd63-617282063');

// PostgreSQL connection using provided credentials  
$database_url = 'postgresql://bqrdjune:nifvcxmmdsapgucosrrq@alpha.mkdb.sh:5432/zrtlmoxa';

try {
    // Parse database URL
    $db_info = parse_url($database_url);
    
    $host = $db_info['host'];
    $port = $db_info['port'] ?? 5432;
    $dbname = ltrim($db_info['path'], '/');
    $user = $db_info['user'];
    $password = $db_info['pass'];
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";
    
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "erro" => "Erro de conexão com banco de dados",
        "detalhes" => "Verifique as credenciais do banco"
    ]);
    exit;
}

// Function to log errors
function logError($message, $context = []) {
    $log_entry = date('Y-m-d H:i:s') . " - " . $message;
    if (!empty($context)) {
        $log_entry .= " - Context: " . json_encode($context);
    }
    error_log($log_entry);
}

// Function to return JSON error
function returnError($code, $message, $details = null) {
    http_response_code($code);
    $response = ["erro" => $message];
    if ($details) {
        $response["detalhes"] = $details;
    }
    echo json_encode($response);
    exit;
}
?>
