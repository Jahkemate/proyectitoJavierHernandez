<?php
require_once 'conexion.php';

$errores = [];
$exito = '';

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
        $errores[] = "El valor del gasto debe ser numérico y mayor a 0.";
    }

    if (empty($errores)) {
        try {
            $sql = "INSERT INTO gastos (nombre, tipoGasto, valorGasto) VALUES (:nombre, :tipoGasto, :valorGasto)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':tipoGasto' => $tipoGasto,
                ':valorGasto' => $valorGasto
            ]);
            $exito = "¡Gasto registrado con éxito!";
        } catch (PDOException $e) {
            $errores[] = "Error al guardar en la base de datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Gasto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3 mb-2 bg-warning-subtle text-warning-emphasis">

<div class="container mt-5">
    <h2 class="mb-4">Registrar Gastos Familiares</h2>

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

    <form method="POST" action="" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Nombre de la persona</label>
            <input type="text" name="nombre" class="form-control" maxlength="80" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de gasto</label>
            <select name="tipoGasto" class="form-select" required>
                <option value="">Seleccione</option>
                <option value="Alimentacion">Alimentación</option>
                <option value="Transporte">Transporte</option>
                <option value="Salud">Salud</option>
                <option value="Cine">Cine</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Valor del gasto</label>
            <input type="number" name="valorGasto" class="form-control" step="0.01" min="0.01" required>
        </div>

        <button type="submit" class="btn btn-success">Registrar Gasto</button>
    </form>
</div>

</body>
</html>
