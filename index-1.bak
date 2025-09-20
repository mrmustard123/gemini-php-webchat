<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gemini Webchat (PHP backend)</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Pequeños ajustes adicionales */
    .chat-box::-webkit-scrollbar { width: 8px; }
    .chat-box::-webkit-scrollbar-thumb { border-radius: 9999px; background: rgba(0,0,0,0.12); }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
  <div class="w-full max-w-3xl">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
      <header class="px-6 py-4 bg-gray-800 text-white text-center font-semibold">Gemini Webchat — PHP 8 backend</header>

      <main class="p-6 flex flex-col" style="height: 70vh;">
        <div id="chat" class="chat-box flex-1 overflow-y-auto space-y-4 px-2" aria-live="polite"></div>

        <form id="chat-form" class="mt-4 flex gap-3" autocomplete="off">
          <input id="message" name="message" type="text" required
                 class="flex-1 border border-gray-300 rounded-full px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                 placeholder="Escribí tu mensaje..." />
          <button id="sendBtn" type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-full hover:bg-blue-700">Enviar</button>
        </form>

        <div id="status" class="mt-3 text-sm text-gray-500"></div>
      </main>
    </div>

    <p class="mt-4 text-sm text-gray-600">Nota: este frontend hace POST a <code>index.php</code> y espera JSON con la conversación en <code>history</code>. Si necesitás, puedo generar el endpoint PHP también.</p>
  </div>

  <script>
    // Config
    const endpoint = 'chat.php'; // cambialo si tu endpoint PHP es otro (ej. api/chat.php)

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
        const isUser = (turn.role === 'user' || turn.role === 'USER');
        const wrapper = document.createElement('div');
        wrapper.className = 'flex ' + (isUser ? 'justify-end' : 'justify-start');

        const bubble = document.createElement('div');
        bubble.className = 'max-w-[80%] p-3 rounded-2xl break-words';
        bubble.innerHTML = escapeHtml(turn.parts?.[0]?.text || '');

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

    function escapeHtml(unsafe) {
      return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    async function sendMessage(message) {
      // optimista: añadir mensaje del usuario
      history.push({ role: 'user', parts: [{ text: message }] });
      render();
      setBusy(true);

      try {
        const formData = new FormData();
        formData.append('message', message);

        const res = await fetch(endpoint, {
          method: 'POST',
          body: formData,
          headers: {
            'Accept': 'application/json'
          }
        });

        if (!res.ok) {
          const txt = await res.text();
          throw new Error('HTTP ' + res.status + ' - ' + txt);
        }

        const json = await res.json();

        // El servidor debe devolver { history: [...] } con role/parts
        if (Array.isArray(json.history)) {
          history = json.history;
        } else if (json.reply) {
          // fallback simple: el servidor devuelve { reply: 'texto' }
          history.push({ role: 'model', parts: [{ text: json.reply }] });
        } else {
          history.push({ role: 'model', parts: [{ text: 'Respuesta inválida del servidor' }] });
        }

        render();
      } catch (err) {
        console.error(err);
        status.textContent = 'Error: ' + err.message;
      } finally {
        setBusy(false);
      }
    }

    function setBusy(flag) {
      input.disabled = flag;
      sendBtn.disabled = flag;
      status.textContent = flag ? 'Enviando...' : '';
    }

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const text = input.value.trim();
      if (!text) return;
      input.value = '';
      sendMessage(text);
    });

    // Al cargar, intentar recuperar historial existente desde el servidor
    (async function init() {
      try {
        const res = await fetch(endpoint, { method: 'GET', headers: { 'Accept': 'application/json' } });
        if (res.ok) {
          const json = await res.json();
          if (Array.isArray(json.history)) {
            history = json.history;
            render();
          }
        }
      } catch (err) {
        console.warn('No se pudo recuperar historial: ', err);
      }
    })();
  </script>
</body>
</html>
