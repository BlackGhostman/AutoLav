<?php
session_start();

// Si ya hay una sesión activa, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Lavacar</title>
    <!-- Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .shake {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
            transform: translate3d(0, 0, 0);
        }
        @keyframes shake {
            10%, 90% {
                transform: translate3d(-1px, 0, 0);
            }
            20%, 80% {
                transform: translate3d(2px, 0, 0);
            }
            30%, 50%, 70% {
                transform: translate3d(-4px, 0, 0);
            }
            40%, 60% {
                transform: translate3d(4px, 0, 0);
            }
        }
        .keypad-btn {
            transition: all 0.2s ease-in-out;
        }
        .keypad-btn:active {
            transform: scale(0.95);
            background-color: #1e40af; /* A darker blue on click */
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 to-slate-800 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-sm mx-auto p-4">
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl shadow-2xl shadow-blue-500/10 p-6 md:p-8 text-white">
            
            <!-- Header -->
            <div class="text-center mb-8">
                <img src="img/5125430911406550677.jpg" alt="Logo AUTOSPA BLUE LINE" class="w-32 h-auto mx-auto mb-4 rounded-lg">
                <h2 class="text-3xl font-bold text-white">AUTOSPA BLUE LINE</h2>
                <p class="text-slate-400 mt-1">Ingrese su PIN de acceso</p>
            </div>

            <!-- PIN Display (6 digits) -->
            <div id="pin-display-container" class="flex justify-center items-center space-x-2 mb-8">
                <div class="w-5 h-5 bg-slate-700 rounded-full"></div>
                <div class="w-5 h-5 bg-slate-700 rounded-full"></div>
                <div class="w-5 h-5 bg-slate-700 rounded-full"></div>
                <div class="w-5 h-5 bg-slate-700 rounded-full"></div>
                <div class="w-5 h-5 bg-slate-700 rounded-full"></div>
                <div class="w-5 h-5 bg-slate-700 rounded-full"></div>
            </div>
            
            <!-- Message Area -->
            <div id="message-area" class="text-center h-6 mb-4 text-sm font-medium"></div>

            <!-- Numeric Keypad -->
            <div class="grid grid-cols-3 gap-4">
                <button onclick="handleKeyPress('1')" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 text-2xl font-semibold">1</button>
                <button onclick="handleKeyPress('2')" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 text-2xl font-semibold">2</button>
                <button onclick="handleKeyPress('3')" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 text-2xl font-semibold">3</button>
                <button onclick="handleKeyPress('4')" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 text-2xl font-semibold">4</button>
                <button onclick="handleKeyPress('5')" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 text-2xl font-semibold">5</button>
                <button onclick="handleKeyPress('6')" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 text-2xl font-semibold">6</button>
                <button onclick="handleKeyPress('7')" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 text-2xl font-semibold">7</button>
                <button onclick="handleKeyPress('8')" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 text-2xl font-semibold">8</button>
                <button onclick="handleKeyPress('9')" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 text-2xl font-semibold">9</button>
                <button onclick="handleDelete()" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 002.828 0L21 12M3 12l6.414-6.414a2 2 0 012.828 0L21 12" />
                    </svg>
                </button>
                <button onclick="handleKeyPress('0')" class="keypad-btn bg-slate-700/50 hover:bg-slate-700 rounded-xl p-5 text-2xl font-semibold">0</button>
                <button onclick="handleLogin()" class="keypad-btn bg-blue-600 hover:bg-blue-500 rounded-xl p-5 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        const PIN_LENGTH = 6;
        let currentPin = "";
        const pinDisplayContainer = document.getElementById('pin-display-container');
        const pinDots = pinDisplayContainer.children;
        const messageArea = document.getElementById('message-area');

        function updatePinDisplay() {
            for (let i = 0; i < PIN_LENGTH; i++) {
                if (i < currentPin.length) {
                    pinDots[i].classList.remove('bg-slate-700');
                    pinDots[i].classList.add('bg-blue-400');
                } else {
                    pinDots[i].classList.add('bg-slate-700');
                    pinDots[i].classList.remove('bg-blue-400');
                }
            }
        }

        function handleKeyPress(key) {
            if (currentPin.length >= PIN_LENGTH) return;
            currentPin += key;
            updatePinDisplay();
        }

        function handleDelete() {
            if (currentPin.length > 0) {
                currentPin = currentPin.slice(0, -1);
                updatePinDisplay();
            }
        }
        
        function showMessage(text, type = 'error') {
            messageArea.textContent = text;
            messageArea.className = type === 'success' ? 'text-center h-6 mb-4 text-sm font-medium text-green-400' : 'text-center h-6 mb-4 text-sm font-medium text-red-400';
        }

        function resetLogin() {
            currentPin = "";
            updatePinDisplay();
            messageArea.textContent = "";
        }

        async function handleLogin() {
            if (currentPin.length < PIN_LENGTH) {
                showMessage(`El PIN debe tener ${PIN_LENGTH} dígitos`);
                pinDisplayContainer.classList.add('shake');
                setTimeout(() => pinDisplayContainer.classList.remove('shake'), 500);
                return;
            }

            try {
                const response = await fetch('validar_pin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ pin: currentPin })
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('¡Acceso concedido!', 'success');
                    // Redirigir al dashboard
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1000);
                } else {
                    showMessage(result.message || 'PIN incorrecto. Intente de nuevo.');
                    pinDisplayContainer.classList.add('shake');
                    setTimeout(() => {
                        pinDisplayContainer.classList.remove('shake');
                        resetLogin();
                    }, 1000);
                }
            } catch (error) {
                showMessage('Error de conexión con el servidor.');
                 pinDisplayContainer.classList.add('shake');
                    setTimeout(() => {
                        pinDisplayContainer.classList.remove('shake');
                        resetLogin();
                    }, 1000);
            }
        }

        document.addEventListener('DOMContentLoaded', updatePinDisplay);
    </script>
</body>
</html>
