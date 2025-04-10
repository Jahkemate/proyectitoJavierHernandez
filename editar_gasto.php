<?php
require_once 'conexion.php';

$id = $_GET['id'] ?? null;
$errores = [];
$exito = '';

// Obtener los datos del gasto a editar
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM gastos WHERE codigoGasto = :id");
    $stmt->execute([':id' => $id]);
    $gasto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$gasto) {
        die("Gasto no encontrado.");
    }
} else {
    die("ID inválido.");
}

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $tipoGasto = $_POST['tipoGasto'] ?? '';
    $valorGasto = $_POST['valorGasto'] ?? '';

    $tiposValidos = ['Alimentacion', 'Transporte', 'Salud', 'Cine'];

    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    }

    if (!in_array($tipoGasto, $tiposValidos)) {
        $errores[] = "Tipo de gasto no válido.";
    }

    if (!is_numeric($valorGasto) || $valorGasto <= 0) {
        $errores[] = "El valor debe ser numérico y mayor a 0.";
    }

    if (empty($errores)) {
        try {
            $sql = "UPDATE gastos SET nombre = :nombre, tipoGasto = :tipoGasto, valorGasto = :valorGasto WHERE codigoGasto = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':tipoGasto' => $tipoGasto,
                ':valorGasto' => $valorGasto,
                ':id' => $id
            ]);
            $exito = "¡Gasto actualizado con éxito!";
            // Recargar los datos actualizados
            $gasto['nombre'] = $nombre;
            $gasto['tipoGasto'] = $tipoGasto;
            $gasto['valorGasto'] = $valorGasto;
        } catch (PDOException $e) {
            $errores[] = "Error al actualizar los datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Gasto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3 mb-2 bg-warning-subtle text-warning-emphasis">

<div class="container mt-5">
    <h2 class="mb-4">Editar Gasto</h2>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($exito): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($exito) ?>
        </div>
    <?php endif; ?>
    <form method="POST" class="card p-4 shadow-sm">
    <div class="mb-3">
        <label class="form-label fw-bold">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($gasto['nombre']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Tipo de gasto</label>
        <select name="tipoGasto" class="form-select" required>
            <option value="">Seleccione</option>
            <?php foreach (['Alimentacion', 'Transporte', 'Salud', 'Cine'] as $tipo): ?>
                <option value="<?= $tipo ?>" <?= $gasto['tipoGasto'] === $tipo ? 'selected' : '' ?>><?= $tipo ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Valor</label>
        <input type="number" name="valorGasto" class="form-control" step="0.01" min="0.01" value="<?= htmlspecialchars($gasto['valorGasto']) ?>" required>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="index.php" class="btn btn-outline-warning">Volver</a>
    </div>
</form>

</div>

</body>
</html>
