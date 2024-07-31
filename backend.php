<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyectos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action == 'addProject') {
        $nombre = $_POST['nombre'];
        $stmt = $conn->prepare("INSERT INTO proyectos (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->close();
    }

    if ($action == 'addHours') {
        $proyecto_id = $_POST['proyecto_id'];
        $fecha = date('Y-m-d');
        $horas = $_POST['horas'];

        // Verificar si ya existe una entrada para el proyecto y el día actual
        $stmt = $conn->prepare("SELECT horas FROM horas_dia WHERE proyecto_id = ? AND fecha = ?");
        $stmt->bind_param("is", $proyecto_id, $fecha);
        $stmt->execute();
        $stmt->bind_result($existing_hours);
        $stmt->fetch();
        $stmt->close();

        if ($existing_hours !== null) {
            // Actualizar las horas existentes
            $stmt = $conn->prepare("UPDATE horas_dia SET horas = horas + ? WHERE proyecto_id = ? AND fecha = ?");
            $stmt->bind_param("iis", $horas, $proyecto_id, $fecha);
            $stmt->execute();
            $stmt->close();
        } else {
            // Insertar nueva entrada
            $stmt = $conn->prepare("INSERT INTO horas_dia (proyecto_id, fecha, horas) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $proyecto_id, $fecha, $horas);
            $stmt->execute();
            $stmt->close();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'];

    if ($action == 'getProjects') {
        $result = $conn->query("SELECT * FROM proyectos");
        $projects = [];
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
        echo json_encode($projects);
    }

    if ($action == 'getHours') {
        $proyecto_id = $_GET['proyecto_id'];
        $fecha = date('Y-m-d');
        $stmt = $conn->prepare("SELECT horas FROM horas_dia WHERE proyecto_id = ? AND fecha = ?");
        $stmt->bind_param("is", $proyecto_id, $fecha);
        $stmt->execute();
        $stmt->bind_result($horas);
        $stmt->fetch();
        echo json_encode(['horas' => $horas ? $horas : 0]);
        $stmt->close();
    }
}

$conn->close();
?>
