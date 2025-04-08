<?php
header('Content-Type: application/json');
include 'db.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = explode('/', trim($_SERVER['PATH_INFO'], '/'));

switch ($request_uri[0]) {
    case 'listar':
        listarUsuarios();
        break;
    case 'insertar':
        if ($request_method == 'POST') {
            insertarUsuario();
        }
        break;
    case 'editar':
        if ($request_method == 'POST') {
            editarUsuario();
        }
        break;
    case 'eliminar':
        eliminarUsuario($request_uri[1]);
        break;
    default:
        echo json_encode(['message' => 'Ruta no encontrada']);
}

function listarUsuarios() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios);
}

function insertarUsuario() {
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, estado) VALUES (?, ?, ?)");
    $stmt->execute([$data['nombre'], $data['email'], $data['estado']]);
    echo json_encode(['message' => 'Usuario insertado']);
}

function editarUsuario() {
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, estado = ? WHERE id = ?");
    $stmt->execute([$data['nombre'], $data['email'], $data['estado'], $data['id']]);
    echo json_encode(['message' => 'Usuario actualizado']);
}

function eliminarUsuario($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['message' => 'Usuario eliminado']);
}
?>
