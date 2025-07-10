<?php
// Usar los namespaces de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// --- Carga de la biblioteca PHPMailer ---
// Esto funciona porque la carpeta PHPMailer está dentro de public_html
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

// --- Procesamiento del Formulario ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $mail = new PHPMailer(true);

    try {
        // --- Configuración del Servidor SMTP ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'orlando.gallegos@melysjanitorial.com';
        $mail->Password   = 'rckxdbvsmmtpjlxs'; // Tu contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Configuración de los correos
        $mail->setFrom('orlando.gallegos@melysjanitorial.com', 'Sitio Web Melys Janitorial');
        $mail->addAddress('orlando.gallegos@melysjanitorial.com'); 

        // --- Estilos CSS para el correo ---
        $style = "<style>
                    body { font-family: Arial, sans-serif; color: #333333; }
                    .container { padding: 20px; border: 1px solid #dddddd; border-radius: 5px; max-width: 600px; margin: auto; }
                    h2 { color: #12418A; }
                    table { width: 100%; border-collapse: collapse; }
                    td { padding: 10px; border-bottom: 1px solid #eeeeee; }
                    .label { background-color: #f7f7f7; font-weight: bold; width: 35%; }
                  </style>";

        // --- Lógica para Diferenciar entre Formularios ---
        
        // Formulario de Carreras
        if (isset($_POST["firma_electronica"])) {
            $applicant_name = strip_tags(trim($_POST["nombre"])) . " " . strip_tags(trim($_POST["apellido"]));
            $applicant_email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
            $mail->addReplyTo($applicant_email, $applicant_name);
            $mail->Subject = 'Nueva Solicitud de Empleo - ' . $applicant_name;
            $mail->isHTML(true);
            
            $body = "<html><head>$style</head><body><div class='container'>";
            $body .= "<h2>Nueva Solicitud de Empleo</h2><table>";
            foreach ($_POST as $key => $value) {
                if (!empty($value)) {
                    $label = htmlspecialchars(ucwords(str_replace('_', ' ', $key)));
                    $body .= "<tr><td class='label'>$label:</td><td>" . nl2br(htmlspecialchars($value)) . "</td></tr>";
                }
            }
            $body .= "</table></div></body></html>";
            $mail->Body = $body;
            
            $mail->send();
            http_response_code(200);
            echo "✅ ¡Gracias! Hemos recibido tu solicitud.";

        } 
        // Formulario de Contacto
        else {
            $contact_name = strip_tags(trim($_POST["name"]));
            $contact_email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
            $mail->addReplyTo($contact_email, $contact_name);
            $mail->Subject = "Nuevo Mensaje de Contacto: " . strip_tags(trim($_POST["subject"]));
            $mail->isHTML(true);

            $body = "<html><head>$style</head><body><div class='container'>";
            $body .= "<h2>Nuevo Mensaje de Contacto</h2><table>";
            $body .= "<tr><td class='label'>Nombre:</td><td>" . htmlspecialchars($contact_name) . "</td></tr>";
            $body .= "<tr><td class='label'>Email:</td><td>" . htmlspecialchars($contact_email) . "</td></tr>";
            $body .= "<tr><td class='label'>Teléfono:</td><td>" . htmlspecialchars(strip_tags(trim($_POST["phone"]))) . "</td></tr>";
            $body .= "<tr><td class='label'>Fecha de Cita:</td><td>" . htmlspecialchars(strip_tags(trim($_POST["date"]))) . "</td></tr>";
            $body .= "<tr><td class='label'>Asunto:</td><td>" . htmlspecialchars(strip_tags(trim($_POST["subject"]))) . "</td></tr>";
            $body .= "</table></div></body></html>";
            $mail->Body = $body;

            $mail->send();
            http_response_code(200);
            echo "✅ Thank you! We will contact you shortly.";
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error: No se pudo enviar el mensaje en este momento.";
        // Para depurar en el futuro, puedes registrar el error en lugar de mostrarlo
        // error_log("Mailer Error: {$mail->ErrorInfo}");
    }
} else {
    http_response_code(403);
    echo "Acceso no permitido.";
}
?>