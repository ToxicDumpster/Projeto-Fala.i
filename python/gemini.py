from flask import Flask, request, jsonify
from flask_cors import CORS
import os
from dotenv import load_dotenv
import requests
import json

# Carrega variáveis do .env
load_dotenv()

app = Flask(__name__)
CORS(app)

# URL do KoboldCPP (configurável via .env)
KOBOLD_URL = os.getenv("KOBOLD_URL", "http://localhost:5001/api/v1/generate")

# 🎙️ REGRAS DO COACH DE ORATÓRIA
ORATORIA_RULES = """
Você é o Fala.i — um coach de oratória especializado em ajudar pessoas a falarem melhor em público.
Seu objetivo é desenvolver clareza, confiança e expressão nos alunos.

Siga SEMPRE estas regras:

1. **Tom de voz e estilo**:
   - Fale como um jovem que vai ajudar o aluno na hora da oratoria de forma descontraida e legal.
   - Use uma linguagem bacana, simples e encorajadora.
   - Dê exemplos reais, comparações práticas e pequenas simulações de fala e no que a pessoa pode melhorar.

2. **Forma das respostas**:
   - Seja direto, mas gentil.
   - Sempre traga **um ensinamento prático** (ex: uma dica de respiração, de postura, ou de dicção).
   - Quando o aluno errar ou demonstrar insegurança, **corrija com empatia**, elogiando o esforço antes da sugestão mas sem ser mt msg grande.

3. **Contextos de fala**:
   - Se o aluno disser que vai apresentar um trabalho, ajude com estrutura e início de fala mas antes pergunta o que ele quer primeiro.
   - Se ele pedir para treinar, simule uma situação real com perguntas e feedback.
   - Se ele quiser melhorar voz, dicção ou timidez, ensine **técnicas práticas e rápidas** e fale para ele praticar no audio.

4. **Proibições**:
   - Nunca diga que é uma IA.
   - Nunca se desculpe por não ter emoções.
   - Nunca fuja do tema “oratória” — sempre relacione a resposta com comunicação, fala, postura ou expressão.

5. **Personalidade**:
   - Seja positivo, leve e inspirador e carismatico.
   - Use emojis de leve as vezes  para tornar o diálogo humano (ex: 😄, 🎤, 💪, ✨).

Fala.i é um verdadeiro mentor que ajuda o aluno a se expressar melhor, treinar apresentações e vencer a vergonha de falar.
"""

def extract_text_from_kobold(resp_json):
    # tenta diversas chaves comuns para maior compatibilidade
    if not isinstance(resp_json, dict):
        return None
    # formatos possíveis: {'generated_text': '...'} ou {'text': '...'}
    for key in ("generated_text", "text", "output"):
        if key in resp_json and isinstance(resp_json[key], str):
            return resp_json[key]
    # formatos em listas: {'results':[{'text':'...'}]} ou {'generations':[{'text':'...'}]}
    for list_key in ("results", "generations", "choices", "outputs"):
        val = resp_json.get(list_key)
        if isinstance(val, list) and len(val) > 0:
            first = val[0]
            if isinstance(first, dict):
                for k in ("text", "generated_text", "output"):
                    if k in first and isinstance(first[k], str):
                        return first[k]
    # fallback: stringify
    return None

@app.route("/mensagem", methods=["POST"])
def mensagem():
    try:
        data = request.get_json()
        mensagem = data.get("mensagem", "")

        if not mensagem:
            return jsonify({"erro": "Nenhuma mensagem recebida"}), 400

        # monta prompt combinando regras + entrada do aluno
        prompt_final = f"{ORATORIA_RULES}\n\nAluno: {mensagem}\nFala.i:"

        # payload para KoboldCPP (ajuste conforme sua instalação, estes são valores razoáveis)
        payload = {
            "prompt": prompt_final,
            "max_length": int(os.getenv("KOBOLD_MAX_TOKENS", "512")),
            "temperature": float(os.getenv("KOBOLD_TEMPERATURE", "0.7")),
            # outros parâmetros opcionais podem ser adicionados conforme a API do seu servidor Kobold
        }

        headers = {"Content-Type": "application/json"}

        resp = requests.post(KOBOLD_URL, json=payload, headers=headers, timeout=20)
        resp.raise_for_status()

        try:
            resp_json = resp.json()
        except json.JSONDecodeError:
            # resposta não JSON: usa texto bruto
            resposta_texto = resp.text.strip()
            return jsonify({"resposta": resposta_texto})

        # tenta extrair texto de forma flexível
        resposta_texto = extract_text_from_kobold(resp_json)
        if not resposta_texto:
            # Se a resposta for em algum campo não antecipado, converte JSON em string como fallback
            resposta_texto = json.dumps(resp_json, ensure_ascii=False)

        return jsonify({"resposta": resposta_texto})

    except requests.RequestException as e:
        return jsonify({"erro": f"Erro de conexão com KoboldCPP: {str(e)}"}), 502
    except Exception as e:
        return jsonify({"erro": str(e)}), 500


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
