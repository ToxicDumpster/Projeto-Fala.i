<?php
function enviarMensagemGemini($mensagem) {
    // URL do Kobold (configurável via env)
    $koboldUrl = getenv('KOBOLD_URL') ?: 'http://localhost:5001/api/v1/generate';

    // Prompt básico + mantém personalidade mínima (pode ajustar)
    $ORATORIA_RULES = "Você é o Fala.i — um coach de oratória. Responda com dicas práticas, tom jovem e encorajador.\nAluno: ";

    $prompt = $ORATORIA_RULES . $mensagem . "\nFala.i:";

    $payload = json_encode([
        "prompt" => $prompt,
        "max_length" => intval(getenv('KOBOLD_MAX_TOKENS') ?: 512),
        "temperature" => floatval(getenv('KOBOLD_TEMPERATURE') ?: 0.7)
    ]);

    $ch = curl_init($koboldUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp === false) {
        return ['erro' => "Erro de conexão com Kobold: " . $err];
    }

    // tenta decodificar JSON
    $data = json_decode($resp, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // se não for JSON, devolve texto cru
        $texto = trim($resp);
        return ['resposta' => $texto];
    }

    // tenta extrair campos comuns
    if (isset($data['generated_text']) && is_string($data['generated_text'])) {
        return ['resposta' => $data['generated_text']];
    }
    if (isset($data['text']) && is_string($data['text'])) {
        return ['resposta' => $data['text']];
    }
    // campos em arrays (results / generations / choices / outputs)
    foreach (['results','generations','choices','outputs'] as $k) {
        if (!empty($data[$k]) && is_array($data[$k])) {
            $first = $data[$k][0];
            if (is_array($first)) {
                foreach (['text','generated_text','output'] as $tk) {
                    if (isset($first[$tk]) && is_string($first[$tk])) {
                        return ['resposta' => $first[$tk]];
                    }
                }
            } elseif (is_string($first)) {
                return ['resposta' => $first];
            }
        }
    }

    // fallback: transforma o JSON em string legível
    return ['resposta' => json_encode($data, JSON_UNESCAPED_UNICODE)];
}
