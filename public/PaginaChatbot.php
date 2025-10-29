<?php

require_once "../Controller/GeminiController.php";
// Garante que variáveis existem
if($_SERVER['REQUEST_METHOD'] === "POST"){
    $pergunta = $_POST['pergunta'] ?? "";
    $respostaApi = enviarMensagemGemini($pergunta);
    $resposta = $respostaApi['resposta'] ?? "Erro ao obter resposta.";

}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Script para aplicar tema INSTANTANEAMENTE -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Young+Serif&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@200;300;400;600;700;900&family=Young+Serif&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="assets/img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="static/PaginaChatbot/PaginaChatbot.css">
    <title>Chat Fala.i</title>
</head>

<body>
    <nav>
        <div class="nav-left">
            <img src="assets/img/logo.png" alt="Logo do Chatbot" id="logo" class="logo" width="60px">
        </div>
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
        <ul class="nav-menu" id="nav-menu">
            <li><a href="PaginaInicial.php">Serviços</a></li>
            <li><a href="PaginaCadastro.php">Quem somos</a></li>
            <li><a href="PaginaInicial.php">Tutorial</a></li>
            <li><a href="PaginaInicial.php">Contato</a></li>
        </ul>
        <div class="nav-right">
            <button id="toggle-dark-mode" class="toggle-mode" title="Alternar modo escuro">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
            <form action="/minha_conta">
                <button class="account-btn" type="submit">
                    <i class="fas fa-user"></i>
                    <span>Minha conta</span>
                </button>
            </form>
        </div>
    </nav>

    <div class="tudo">
        <div class="menu-container">
            <div class="icon-menu">
                <button class="bars" onclick="abrirmenu()"><i class="fas fa-bars"></i></button>
                <button class="add">&plus;</button>
            </div>
            <div class="menu" id="menu"></div>
        </div>

        <div class="container">
            <div class="main-container">
                <?php if (!empty($resposta)): ?>
    <div class="resposta-animada">
        <strong>Fala.i:</strong>
        <p><?= htmlspecialchars($resposta) ?></p>
    </div>
<?php endif; ?>

            </div>

            <div class="chat">
                <form " method="POST" id="form">
                    <textarea class="pergunta" name="pergunta" id="pergunta" placeholder="Fala aí!"><?= htmlspecialchars($pergunta) ?></textarea>
                    <div class="voice-controls">
                <button type="button" id="btn-falar" class="falar"><i class="fas fa-microphone"></i> Falar</button>
                <button type="button" id="btn-parar" class="parar" disabled><i class="fas fa-stop"></i> Parar</button>
                </div>

                    <button type="submit" class="enviar"><i class="bi bi-arrow-up-circle-fill"></i></button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer-inicio fade-in">
        <div class="footer-inicio">
            <div class="logo-footer">
                <img src="assets/img/logo.png" alt="" id="logo-footer">
                <p>FALA.I</p>
            </div>
            <hr>
            <div class="informacoes-footer">
                <p>Endereço de e-mail: fala.i.contact@gmail.com</p><br>
                <p>Telefone: +55 (11) 98369-9658</p>
            </div>
            <div class="icons-footer">
                <a href="https://instagram.com" target="_blank" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://facebook.com" target="_blank" aria-label="Facebook">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="https://github.com" target="_blank" aria-label="GitHub">
                    <i class="fab fa-github"></i>
                </a>
            </div>
            <div class="caminhos-footer">
                <a href="/termos">Ver Termos</a>
                <p>|</p>
                <a href="/minha_conta">Minha Conta</a>
                <p>|</p>
                <a href="/agenda">Agenda</a>
                <p>|</p>
                <a href="#sessao-dicas">Dicas</a>
                <p>|</p>
                <a href="#sessao-videos">Vídeos</a>
            </div>

            <div class="copright">
                <p>©2025 Fala.i. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script> new window.VLibras.Widget('https://vlibras.gov.br/app'); </script>

    <script src="static/PaginaChatbot/PaginaChatbot.js"></script>

    <script>
        function abrirmenu() {
            const menu = document.getElementById("menu")
            menu.classList.toggle("ativo")
        }

        const menuContainer = document.querySelector(".menu-container")
        const menu = document.getElementById("menu")
        const addButton = document.querySelector(".add")

        menuContainer.addEventListener("mouseenter", () => menu.classList.add("ativo"))
        menuContainer.addEventListener("mouseleave", () => menu.classList.remove("ativo"))
        addButton.addEventListener("mouseenter", () => menu.classList.add("ativo"))
        addButton.addEventListener("mouseleave", () => menu.classList.remove("ativo"))

        const textarea = document.getElementById("pergunta")
        const form = document.getElementById("form")

        textarea.addEventListener("keydown", (event) => {
            if (event.key === "Enter" && !event.shiftKey) {
                event.preventDefault()
                form.dispatchEvent(new Event("submit"))
            }
        })

        function toggleMobileMenu() {
            const navMenu = document.getElementById("nav-menu")
            navMenu.classList.toggle("mobile-active")
        }

        class ThemeManager {
            constructor() { this.init() }
            init() {
                const currentTheme = document.documentElement.getAttribute("data-theme") || "light"
                this.updateToggleIcon(currentTheme)
                this.setupToggleButton()
            }
            setTheme(theme) {
                document.documentElement.setAttribute("data-theme", theme)
                localStorage.setItem("theme", theme)
                this.updateToggleIcon(theme)
            }
            updateToggleIcon(theme) {
                const icon = document.getElementById("theme-icon")
                if (icon) icon.className = theme === "dark" ? "fas fa-sun" : "fas fa-moon"
            }
            toggleTheme() {
                const currentTheme = document.documentElement.getAttribute("data-theme")
                const newTheme = currentTheme === "dark" ? "light" : "dark"
                this.setTheme(newTheme)
            }
            setupToggleButton() {
                const toggleButton = document.getElementById("toggle-dark-mode")
                if (toggleButton) toggleButton.addEventListener("click", () => this.toggleTheme())
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            new ThemeManager()
            observeElements()
        })

        function observeElements() {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) entry.target.classList.add("fade-animate")
                    })
                }, { threshold: 0.1 }
            )
            document.querySelectorAll(".fade-in").forEach((el) => observer.observe(el))
        } 
    </script>

    <script>
let recognition;
let isRecording = false;

if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.lang = 'pt-BR';
    recognition.interimResults = false;
    recognition.continuous = false;

   recognition.onresult = async function(event) {
    const transcript = event.results[0][0].transcript;
    stopRecording();

    // Envia a transcrição direto pro Gemini
    await enviarMensagemGemini(transcript);
    };

    recognition.onerror = function(event) {
        console.error("Erro no reconhecimento:", event.error);
        stopRecording();
    };
}

const btnFalar = document.getElementById("btn-falar");
const btnParar = document.getElementById("btn-parar");

btnFalar.addEventListener("click", () => {
    if (!recognition) return alert("Seu navegador não suporta reconhecimento de voz ");

    recognition.start();
    isRecording = true;
    btnFalar.disabled = true;
    btnParar.disabled = false;
});

btnParar.addEventListener("click", () => stopRecording());

function stopRecording() {
    if (recognition && isRecording) {
        recognition.stop();
        isRecording = false;
        btnFalar.disabled = false;
        btnParar.disabled = true;
    }
}

async function enviarMensagemGemini(texto) {
    try {
        const response = await fetch("http://localhost:5000/mensagem", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ mensagem: texto })
        });

        const data = await response.json();
        const resposta = data.resposta || "Erro na resposta do servidor.";

        // Mostra no chat
        const mainContainer = document.querySelector(".main-container");
        const divResposta = document.createElement("div");
        divResposta.className = "resposta-animada";
        divResposta.innerHTML = `<strong>Fala.i:</strong><p>${resposta}</p>`;
        mainContainer.appendChild(divResposta);

    } catch (err) {
        console.error("Erro ao enviar para Gemini:", err);
    }
}
</script>

</body>
</html>
