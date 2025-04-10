<?php
require_once 'conexion.php';

$errores = [];

// --- Registro de gasto ---
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

            // Redirigir después del POST para evitar duplicación al recargar
            header("Location: " . $_SERVER['PHP_SELF'] . "?exito=1");
            exit;

        } catch (PDOException $e) {
            $errores[] = "Error al guardar en la base de datos.";
        }
    }
}

// --- Consulta de gastos ---
try {
    $stmt = $pdo->query("SELECT nombre, tipoGasto, valorGasto FROM gastos ORDER BY codigoGasto DESC");
    $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = array_sum(array_column($gastos, 'valorGasto'));
} catch (PDOException $e) {
    die("Error al obtener los datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro y Listado de Gastos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3 mb-2 bg-warning-subtle text-warning-emphasis">

<div class="container mt-5">
    <h2 class="mb-4">Registrar Gastos Familiares</h2>

    <!-- Mostrar errores -->
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <!-- Mostrar mensaje de éxito -->
    <?php elseif (isset($_GET['exito']) && $_GET['exito'] == '1'): ?>
        <div class="alert alert-success">
            ¡Gasto registrado con éxito!
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form method="POST" action="" class="card p-4 shadow-sm mb-5">
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

    <!-- Tabla de gastos -->
    <h3 class="mb-3">Gastos Registrados</h3>

    <?php if (count($gastos) > 0): ?>
        <table class="table table-success table-bordered table-striped shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Tipo de Gasto</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos as $gasto): ?>
                    <tr>
                        <td><?= htmlspecialchars($gasto['nombre']) ?></td>
                        <td><?= htmlspecialchars($gasto['tipoGasto']) ?></td>
                        <td>$<?= number_format($gasto['valorGasto'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-primary fw-bold">
                    <td colspan="2" class="text-end">Total Acumulado:</td>
                    <td>$<?= number_format($total, 2) ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No hay gastos registrados aún.</div>
    <?php endif; ?>
</div>

</body>
</html>
