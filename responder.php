<?php
// Incluye la conexión a la base de datos
require 'db_conexion.php';

require __DIR__ . '/vendor/autoload.php'; // Asegúrate de que la ruta al autoload.php sea correcta

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Ahora puedes acceder a tu variable de entorno
$apiKey = $_ENV['OPENAI_API_KEY'];

$preguntaDelUsuario = $_POST['pregunta'];

// Primero, busca en la base de datos local
//$sql = "SELECT respuesta FROM FAQs WHERE pregunta LIKE CONCAT('%', ?, '%')";
$sql = "SELECT respuesta FROM FAQs WHERE MATCH(pregunta) AGAINST(? IN NATURAL LANGUAGE MODE)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $preguntaDelUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    echo $fila['respuesta'];
} else {
    // Si la pregunta no está en la base de datos, consulta a la API de OpenAI
    $ch = curl_init();
    
    $data = [
        'model' => 'text-davinci-003',
        'prompt' => $preguntaDelUsuario,
        'temperature' => 0.7,
        'max_tokens' => 150
    ];

    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v4/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer {$apiKey}"
    ]);
    
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error en la solicitud a OpenAI: ' . curl_error($ch);
    } else {
        $responseData = json_decode($response, true);
        if (isset($responseData['choices'][0]['text'])) {
            echo $responseData['choices'][0]['text'];
        } else {
            echo "No se pudo obtener una respuesta del asistente de ayuda de End-Tech.";
        }
    }
    
    curl_close($ch);
}

if (isset($stmt)) {
    $stmt->close();
}
$conexion->close();
?>
