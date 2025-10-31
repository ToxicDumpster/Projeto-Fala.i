<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class ChatModel
{
    public static function gerarResposta($mensagem)
    {
        // Envia a mensagem para o servidor Flask que encaminha para o KoboldCPP
        $url = getenv('LOCAL_AI_URL') ?: 'http://localhost:5000/mensagem';

        $payload = json_encode(["mensagem" => $mensagem]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($resp === false) {
            return "Erro ao conectar ao serviço de geração de texto: " . $err;
        }

        $data = json_decode($resp, true);
        if (isset($data['resposta'])) {
            return $data['resposta'];
        } elseif (isset($data['erro'])) {
            return "Erro do servidor de IA: " . $data['erro'];
        } else {
            // fallback: retorna a resposta crua
            return is_string($resp) ? $resp : json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }
}
