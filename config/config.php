<?php

function getConnection(){ 
    // Use variáveis de ambiente se disponíveis (útil para produção/local)
    $hostname = getenv('DB_HOST') ?: "localhost";
    $username = getenv('DB_USER') ?: "root";
    $password = getenv('DB_PASS') ?: "";
    $database = getenv('DB_NAME') ?: "tcc";

    // Fazer o mysqli lançar exceções para podermos capturá-las
    mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);

    try {
        $connect = new mysqli($hostname, $username, $password, $database);
        $connect->set_charset('utf8mb4');
        return $connect;
    } catch (mysqli_sql_exception $e) {
        // Mensagem curta e orientações práticas (em PT-BR)
        die(
            "Erro ao conectar ao banco de dados: " . $e->getMessage()
            . "\n\nVerifique:\n"
            . "- Se o serviço MySQL/MariaDB está rodando (ex.: XAMPP Control Panel → Start 'MySQL').\n"
            . "- Credenciais em config/config.php ou variáveis de ambiente DB_HOST/DB_USER/DB_PASS/DB_NAME.\n"
            . "- Se o banco de dados 'tcc' existe (use phpMyAdmin ou mysql CLI).\n\n"
            . "Porta padrão: 3306. Se estiver usando outra porta, ajuste a conexão."
        );
    }
}
