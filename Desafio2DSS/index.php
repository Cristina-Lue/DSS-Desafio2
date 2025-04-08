<?php
// Inicializa variables
$nombre = $apellidos = $numero_carnet = $correo = $edad = "";
$errores = [];

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $apellidos = trim($_POST["apellidos"]);
    $numero_carnet = trim($_POST["numero_carnet"]);
    $correo = trim($_POST["correo"]);
    $edad = trim($_POST["edad"]);

    // Validación de nombre
    if (!preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
        $errores['nombre'] = "El nombre solo puede contener letras y espacios.";
    }

    // Validación de apellidos
    if (!preg_match("/^[a-zA-Z\s]+$/", $apellidos)) {
        $errores['apellidos'] = "Los apellidos solo pueden contener letras y espacios.";
    }

    // Validación de número de carnet
    if (!preg_match("/^[A-Z]{2}\d{6}$/", $numero_carnet)) {
        $errores['numero_carnet'] = "El número de carnet debe tener el formato 2 letras y 6 números.";
    } else {
        // Verificar si el carnet ya existe
        $existe = false;
        if (file_exists("registros.csv") && ($gestor = fopen("registros.csv", "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
                if (isset($datos[2]) && $datos[2] === $numero_carnet) { // Verifica que el índice 2 existe
                    $existe = true;
                    break;
                }
            }
            fclose($gestor);
        }

        if ($existe) {
            $errores['numero_carnet'] = "El número de carnet ya existe.";
        }
    }

    // Validación de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = "El correo electrónico no es válido.";
    }

    // Validación de edad
    if (!preg_match("/^\d+$/", $edad) || $edad < 18 || $edad > 99) {
        $errores['edad'] = "La edad debe ser un número entre 18 y 99.";
    }

    // Guardar en el archivo si no hay errores
    if (empty($errores)) {
        $archivo = fopen("registros.csv", "a"); // Abre el archivo en modo append
        if ($archivo !== FALSE) {
            fputcsv($archivo, [$nombre, $apellidos, $numero_carnet, $correo, $edad]); // Escribe los datos
            fclose($archivo); // Cierra el archivo
            echo "<p>Datos guardados correctamente.</p>";
        } else {
            echo "<p>Error al abrir el archivo para escribir.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Formulario de Registro</h1>
    <form method="post" action="">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
        <span style="color:red;"><?php echo $errores['nombre'] ?? ''; ?></span><br>

        <label for="apellidos">Apellidos:</label>
        <input type="text" name="apellidos" value="<?php echo htmlspecialchars($apellidos); ?>">
        <span style="color:red;"><?php echo $errores['apellidos'] ?? ''; ?></span><br>

        <label for="numero_carnet">Número de Carnet:</label>
        <input type="text" name="numero_carnet" value="<?php echo htmlspecialchars($numero_carnet); ?>">
        <span style="color:red;"><?php echo $errores['numero_carnet'] ?? ''; ?></span><br>

        <label for="correo">Correo Electrónico:</label>
        <input type="text" name="correo" value="<?php echo htmlspecialchars($correo); ?>">
        <span style="color:red;"><?php echo $errores['correo'] ?? ''; ?></span><br>

        <label for="edad">Edad:</label>
        <input type="text" name="edad" value="<?php echo htmlspecialchars($edad); ?>">
        <span style="color:red;"><?php echo $errores['edad'] ?? ''; ?></span><br>

        <input type="submit" value="Registrar">
    </form>
</body>
</html>
