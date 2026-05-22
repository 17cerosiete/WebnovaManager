<?php
require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id === 0) {
    header('Location: list.php');
    exit();
}

$result = $conn->query("SELECT * FROM widgets WHERE id = $id");
$widget = $result->fetch_assoc();

if (!$widget) {
    header('Location: list.php?error=notfound');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Widget - WebNova Manager</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        body { font-family: sans-serif; background: #f3f4f6; padding: 2rem; }
        .card { background: white; padding: 2rem; border-radius: 1rem; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 0.5rem; }
        .btn { padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer; text-decoration: none; border: none; font-weight: 500; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-secondary { background: #e5e7eb; color: #1f2937; }
    </style>
</head>
<body>
    <div class="card">
        <h2>✎ Editar Widget</h2>
        <form id="widgetForm">
            <input type="hidden" name="id" value="<?php echo $widget['id']; ?>">
            <div class="form-group">
                <label>Nombre del Widget</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($widget['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label>Tipo de Widget</label>
                <select name="tipo">
                    <option value="hero" <?php echo $widget['tipo'] === 'hero' ? 'selected' : ''; ?>>Hero</option>
                    <option value="cta" <?php echo $widget['tipo'] === 'cta' ? 'selected' : ''; ?>>CTA (Llamada a la acción)</option>
                    <option value="faq" <?php echo $widget['tipo'] === 'faq' ? 'selected' : ''; ?>>FAQ (Preguntas Frecuentes)</option>
                    <option value="text" <?php echo $widget['tipo'] === 'text' ? 'selected' : ''; ?>>Texto Enriquecido</option>
                </select>
            </div>
            <div class="form-group">
                <label>Configuración (JSON)</label>
                <textarea name="config" rows="5" required><?php echo htmlspecialchars($widget['config']); ?></textarea>
            </div>
            <div class="form-group">
                <label>HTML de Previsualización (Opcional)</label>
                <textarea name="preview_html" rows="3"><?php echo htmlspecialchars($widget['preview_html']); ?></textarea>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Actualizar Widget</button>
                <a href="list.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('widgetForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            try {
                JSON.parse(data.config);
                const response = await fetch('../../api/widgets.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('✅ Widget actualizado con éxito');
                    window.location.href = 'list.php';
                } else {
                    alert('❌ Error: ' + result.error);
                }
            } catch (err) {
                alert('❌ Error: El campo Configuración debe ser un JSON válido.');
            }
        };
    </script>
</body>
</html>
