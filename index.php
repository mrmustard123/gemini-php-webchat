<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gemini Webchat (PHP backend)</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .chat-box::-webkit-scrollbar { width: 8px; }
    .chat-box::-webkit-scrollbar-thumb { border-radius: 9999px; background: rgba(0,0,0,0.12); }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
  <div class="w-full h-screen">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden" style="height: 95vh;">
      <header class="px-6 py-4 bg-gray-800 text-white text-center font-semibold">
        Gemini Webchat — PHP 8 backend
      </header>

      <main class="p-6 flex flex-col" style="height: 80vh;">
        <div id="chat" class="chat-box flex-1 overflow-y-auto space-y-4 px-2" aria-live="polite"></div>

        <form id="chat-form" class="mt-4 flex gap-3" autocomplete="off">
          <input id="message" name="message" type="text" required
                 class="flex-1 border border-gray-300 rounded-full px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                 placeholder="Escribí tu mensaje..." />
          <button id="sendBtn" type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-full hover:bg-blue-700 disabled:opacity-50">
            Enviar
          </button>
        </form>

        <div id="status" class="mt-3 text-sm text-gray-500"></div>
      </main>
    </div>

    <p class="mt-4 text-sm text-gray-600">
      Webchat conectado a Gemini API via PHP backend.
    </p>      
  </div>

  <script>
    // Config
    //const endpoint = 'chat-php-without-curl.php';
    const endpoint = 'chat.php';
    //const endpoint = 'chat-php-fixed-final.php'; 

    // Estado local
    let history = [];

    const chatEl = document.getElementById('chat');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('message');
    const sendBtn = document.getElementById('sendBtn');
    const status = document.getElementById('status');

    function render() {
      chatEl.innerHTML = '';
      history.forEach(turn => {
        const isUser = (turn.role === 'user');
        const wrapper = document.createElement('div');
        wrapper.className = 'flex ' + (isUser ? 'justify-end' : 'justify-start');

        const bubble = document.createElement('div');
        bubble.className = 'max-w-[80%] p-3 rounded-2xl break-words whitespace-pre-wrap';
        
        // Obtener el texto del mensaje
        const messageText = turn.parts?.[0]?.text || turn.content || '';
        bubble.textContent = messageText;

        if (isUser) {
          bubble.classList.add('bg-blue-600', 'text-white', 'rounded-br-none');
        } else {
          bubble.classList.add('bg-gray-100', 'text-gray-900', 'rounded-bl-none');
        }

        wrapper.appendChild(bubble);
        chatEl.appendChild(wrapper);
      });
      chatEl.scrollTop = chatEl.scrollHeight;
    }

    async function sendMessage(message) {
      // Optimista: añadir mensaje del usuario
      const userMessage = { 
        role: 'user', 
        parts: [{ text: message }] 
      };
      history.push(userMessage);
      render();
      setBusy(true);

      try {
        // Enviar como JSON (no FormData)
        const res = await fetch(endpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ message: message })
        });

        if (!res.ok) {
          throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }

        const json = await res.json();

        if (json.error) {
          throw new Error(json.error);
        }

        // Actualizar historial con la respuesta del servidor
        if (Array.isArray(json.history)) {
          history = json.history;
        } else if (json.reply) {
          // Fallback: añadir solo la respuesta
          const modelMessage = {
            role: 'model',
            parts: [{ text: json.reply }]
          };
          history.push(modelMessage);
        } else {
          throw new Error('Respuesta inesperada del servidor');
        }

        render();
        status.textContent = '';
      } catch (err) {
        console.error('Error:', err);
        status.textContent = 'Error: ' + err.message;
        status.className = 'mt-3 text-sm text-red-500';
        
        // Remover el último mensaje del usuario si hubo error
        if (history.length > 0 && history[history.length - 1].role === 'user') {
          history.pop();
          render();
        }
      } finally {
        setBusy(false);
      }
    }

    function setBusy(flag) {
      input.disabled = flag;
      sendBtn.disabled = flag;
      if (flag) {
        status.textContent = 'Enviando...';
        status.className = 'mt-3 text-sm text-blue-500';
      }
    }

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const text = input.value.trim();
      if (!text) return;
      input.value = '';
      sendMessage(text);
    });

    // Recuperar historial al cargar
    async function loadHistory() {
      try {
        const res = await fetch(endpoint, { 
          method: 'GET', 
          headers: { 'Accept': 'application/json' } 
        });
        
        if (res.ok) {
          const json = await res.json();
          if (Array.isArray(json.history)) {
            history = json.history;
            render();
          }
        }
      } catch (err) {
        console.warn('No se pudo recuperar historial:', err.message);
      }
    }

    // Inicializar
    loadHistory();
  </script>
</body>
</html>