<?php
require_once 'conexion.php';

$id = $_GET['id'] ?? null;
$gasto = null;
$errores = [];
$exito = '';

if (!$id) {
    die("ID no especificado.");
}

// Obtener los datos del gasto
$stmt = $pdo->prepare("SELECT * FROM gastos WHERE codigoGasto = :id");
$stmt->execute([':id' => $id]);
$gasto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gasto) {
    die("Gasto no encontrado.");
}

// Si se confirma la eliminación
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $stmt = $pdo->prepare("DELETE FROM gastos WHERE codigoGasto = :id");
        $stmt->execute([':id' => $id]);
        header("Location: index.php?eliminado=1");
        exit;
    } catch (PDOException $e) {
        $errores[] = "Error al eliminar el gasto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Gasto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3 mb-2 bg-warning-subtle text-warning-emphasis">

<div class="container mt-5">
    <h2 class="mb-4 text-danger">¿Estás seguro que deseas eliminar este gasto?</h2>

    <div class="card p-4 mb-4">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($gasto['nombre']) ?></p>
        <p><strong>Tipo de Gasto:</strong> <?= htmlspecialchars($gasto['tipoGasto']) ?></p>
        <p><strong>Valor:</strong> $<?= number_format($gasto['valorGasto'], 2) ?></p>
    </div>

    <form method="POST" class="d-flex gap-2">
        <button type="submit" class="btn btn-danger">Sí, eliminar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

</body>
</html>
