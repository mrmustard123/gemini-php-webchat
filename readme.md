# Gemini PHP WebChat

A simple **web-based chat interface** built with **HTML, TailwindCSS, JavaScript, and PHP**.  
It provides a lightweight frontend for chat messages and a PHP backend that handles user input, stores chat sessions in JSON, and simulates AI responses.

---

## ✨  Features

- Clean and minimal **chat UI** (bubbles for user and assistant).
- **Scrollable conversation window** with automatic scroll to the latest message.
- **Persistent chat sessions** saved in `chat_session.json`.
- **AJAX communication** between frontend (HTML/JS) and backend (PHP).
- Ready to be extended with **LLM APIs** (e.g., Gemini, OpenAI).

---

## 📂 Project Structure

/project-root

¦
+-- index.php # Main chat UI (HTML + Tailwind + JS)

+-- chat.php # PHP endpoint (receives messages, updates session)

+-- chat_session.json # Stores conversation history

+-- README.md # Project documentation

---

## ⚙️ Requirements

- PHP **>= 8.0** (tested with PHP 8.1.0)
- A local server like **WampServer**, **XAMPP**, or **PHP built-in server**
- (Optional) An IDE like **NetBeans** with XDebug for debugging

---

## 🚀 Setup & Run

1. Clone or copy the project into your PHP server directory (`www` or `htdocs`).
2. Start your local server (Wamp, XAMPP, or `php -S localhost:8000`).
3. Open the chat in your browser:
http://localhost/index.php

4. Start chatting! Messages are sent to the backend (`chat.php`) and stored in `chat_session.json`.

---

## 🛠️ How It Works

1. **Frontend (`index.php`)**  
- Displays a chat UI with message bubbles.  
- Captures user input and sends it via `fetch()` (AJAX POST request) to `chat.php`.  

2. **Backend (`chat-php-fixed-final.php`)**  
- Reads the JSON body using:
  ```php
  $input = json_decode(file_get_contents('php://input'), true);
  ```
- Appends the user message to `chat_session.json`.  
- Generates a placeholder assistant response (can be replaced with API call).  
- Returns the assistant message as JSON.

3. **Persistence**  
- Conversation history is saved in `chat_session.json`.  
- On each refresh, past messages are loaded and displayed.

---

## 📌 Example

**User:** Hello Gemini!  
**Assistant:** (Simulated response) Hi there! This is a placeholder response.

---

## 🔮 Next Steps

- Connect to **Gemini API** (Google) or **OpenAI API** to generate real AI responses.  
- Add **user authentication** for multiple chat sessions.  
- Improve **UI styling** and add message timestamps.  
- Deploy to a real server or cloud hosting.

---

## 📜 License

This project is provided under the **MIT License**.  
Feel free to use, modify, and distribute it.

---

