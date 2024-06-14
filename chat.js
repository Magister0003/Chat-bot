document.addEventListener('DOMContentLoaded', function() {
    mostrarMensajeBienvenida();

    document.getElementById('chat-input').addEventListener('keydown', function(event) {
        console.log(event.key); // Para propósitos de diagnóstico
        if (event.key === 'Enter' && !event.shiftKey) { // Asegúrate de capturar Enter sin Shift
            console.log('Enter presionado sin Shift');
            event.preventDefault(); // Detiene el comportamiento predeterminado solo si es Enter sin Shift
            enviarMensaje();
        }
    });
});

function enviarMensaje() {
    var input = document.getElementById('chat-input');
    var mensaje = input.value.trim();
    if (mensaje) {
        mostrarMensaje(mensaje, 'usuario');
        input.value = ''; // Limpiar el campo de entrada

        // Envía el mensaje al backend PHP y recibe la respuesta
        fetch('responder.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'pregunta=' + encodeURIComponent(mensaje)
        })
        .then(response => response.text())
        .then(text => mostrarMensaje(text, 'bot'))
        .catch(error => console.error('Error:', error));
    }
}

function mostrarMensaje(mensaje, tipo) {
    var chatBox = document.getElementById('chat-box');
    var msgDiv = document.createElement('div');
    msgDiv.textContent = mensaje;
    msgDiv.className = tipo === 'usuario' ? 'mensaje-usuario' : 'mensaje-bot';
    chatBox.appendChild(msgDiv);
    chatBox.scrollTop = chatBox.scrollHeight; // Auto-scroll al último mensaje
}

function mostrarMensajeBienvenida() {
    const mensajeBienvenida = "¡Hola! Soy el asistente virtual de End-Tech. ¿En qué puedo ayudarte hoy?";
    mostrarMensaje(mensajeBienvenida, 'bot');
}
