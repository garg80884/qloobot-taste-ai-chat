function appendMessage(sender, text) {
  const chatBox = document.getElementById('chat-box');
  const div = document.createElement('div');
  div.className = 'message ' + sender;
  div.textContent = sender.toUpperCase() + ': ' + text;
  chatBox.appendChild(div);
  chatBox.scrollTop = chatBox.scrollHeight;
}

function sendMessage() {
  const input = document.getElementById('userInput');
  const userMessage = input.value.trim();
  if (!userMessage) return;

  appendMessage('user', userMessage);
  input.value = '';

  fetch('api/gpt.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ message: userMessage })
  })
  .then(res => res.json())
  .then(data => {
    appendMessage('bot', data.reply);
  })
  .catch(err => {
    appendMessage('bot', 'Oops! Something went wrong.');
    console.error(err);
  });
}
