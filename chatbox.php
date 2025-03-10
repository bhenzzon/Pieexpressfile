<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbox</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin-top: 450px; /* Adjusted to display above website body */
        }
        .chatbox {
            width: 500px;
            height: 400px;
            border: 1px solid #ccc;
            display: none;
            flex-direction: column;
            justify-content: left;
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .chatbox-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: darkgreen;
            color: white;
            font-weight: bold;
        }
        .close-button {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
        }
        .input-box {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ccc;
        }
        .input-box input {
            flex: 1;
            padding: 5px;
        }
        .input-box button {
            padding: 5px 10px;
            background: darkgreen;
            color: white;
            border: none;
            cursor: pointer;
        }
        .chat-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: darkgreen;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .suggested-messages {
            padding: 10px;
            border-top: 1px solid #ccc;
            background: #f1f1f1;
        }
        .suggested-messages button {
            background: lightgray;
            border: none;
            padding: 5px;
            margin: 2px;
            cursor: pointer;
        }
        .message-container {
            display: flex;
            flex-direction: column;
            justify-content: left;
        }
        .user-message {
            margin-left: 20%;
            background: lightgreen;
            padding: 5px;
            border-radius: 5px;
            margin: 2px 0;
        }
        .bot-message {
            margin-left: 1px;
            background: lightgray;
            padding: 5px;
            border-radius: 5px;
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <button class="chat-button" onclick="toggleChatbox()">Chat</button>
    <div class="chatbox" id="chatbox">
        <div class="chatbox-header">
            Chat with Pie Express
            <button class="close-button" onclick="toggleChatbox()">X</button>
        </div>
        <div class="messages" id="messages"></div>
        <div class="suggested-messages">
            <button onclick="quickMessage('Hello')">Hello</button>
            <button onclick="quickMessage('How to order?')">How to order?</button>
            <button onclick="quickMessage('I want to order')">I want to order</button>
            <button onclick="quickMessage('Price')">Price</button>
        </div>
        <div class="input-box">
            <input type="text" id="userInput" placeholder="Type a message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
    <script>
        function toggleChatbox() {
            let chatbox = document.getElementById('chatbox');
            chatbox.style.display = chatbox.style.display === 'flex' ? 'none' : 'flex';
        }

        function sendMessage() {
            let input = document.getElementById('userInput');
            let message = input.value.trim();
            if (message !== '') {
                appendMessage("You", message, "user-message");
                input.value = '';
                setTimeout(() => botReply(message), 1000);
            }
        }

        function quickMessage(text) {
            document.getElementById('userInput').value = text;
            sendMessage();
        }

        function botReply(userMessage) {
    let responses = {
        "hello": "Hi there! How can I help you?",
        "how to order?": "You can place an order through our website! <a href='https://pieexpressph.storehub.me/' target='_blank'>Visit here</a>.",
        "i want to order": "You can place an order through our website! <a href='https://pieexpressph.storehub.me/' target='_blank'>Visit here</a>.",
        "order": "You can place an order through our website! <a href='https://pieexpressph.storehub.me/' target='_blank'>Visit here</a>.",
        "price": "Our prices vary depending on the item. Check our menu!",
        "thanks": "It's our pleasure to serve you.",
        "default": "I'm not sure about that. Can you rephrase?"
    };
    
    let reply = responses[userMessage.toLowerCase()] || responses["default"];
    appendMessage("Pie", reply, "bot-message");
}
        function appendMessage(sender, message, className) {
            let messagesDiv = document.getElementById('messages');
            let newMessage = document.createElement('div');
            newMessage.className = className;
            newMessage.textContent = sender + ": " + message;
            messagesDiv.appendChild(newMessage);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
    </script>
</body>
</html>