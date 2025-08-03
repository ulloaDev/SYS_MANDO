<?php
require_once __DIR__ . '/../../includes/header.php';

$reportes = $data['reportes'] ?? [];
$filtros = $data['filtros'] ?? [];
?> <div class="min-h-screen bg-gray-50 p-4">
 <!-- Header -->
 <div class="bg-blue-900 text-white p-4 rounded-t-lg">
  <div class="flex justify-between items-center">
   <div class="flex items-center space-x-4">
    <i class="fas fa-file-alt text-2xl"></i>
    <h1 class="text-xl font-bold">REPORTES DE MANTENIMIENTO</h1>
   </div>
   <div class="flex items-center space-x-4">
    <button id="generateReportBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
     <i class="fas fa-plus mr-2"></i>Generar Reporte </button>
   </div>
  </div>
 </div>
 <!-- Filtros -->
 <div class="bg-white p-4 border-x border-gray-200">
  <form id="reportFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4">
   <div>
    <label for="reportType" class="block text-sm font-medium text-gray-700">Tipo de Reporte</label>
    <select id="reportType" name="tipo" class="mt-1 p-2 border rounded-md w-full">
     <option value="">Todos</option>
     <option value="mensual" <?php echo ($filtros['tipo'] ?? '') === 'mensual' ? 'selected' : ''; ?>>Mensual</option>
     <option value="trimestral" <?php echo ($filtros['tipo'] ?? '') === 'trimestral' ? 'selected' : ''; ?>>Trimestral
     </option>
     <option value="anual" <?php echo ($filtros['tipo'] ?? '') === 'anual' ? 'selected' : ''; ?>>Anual</option>
    </select>
   </div>
   <div>
    <label for="reportYear" class="block text-sm font-medium text-gray-700">Año</label>
    <select id="reportYear" name="anio" class="mt-1 p-2 border rounded-md w-full">
     <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?> <option value="<?php echo $y; ?>"
      <?php echo ($filtros['anio'] ?? '') == $y ? 'selected' : ''; ?>><?php echo $y; ?></option> <?php endfor; ?>
    </select>
   </div>
   <div class="flex items-end">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
     <i class="fas fa-filter mr-2"></i>Filtrar </button>
   </div>
  </form>
 </div>
 <!-- Lista de reportes -->
 <div class="bg-white border border-gray-200 overflow-x-auto">
  <table class="w-full text-sm">
   <thead>
    <tr class="bg-blue-100">
     <th class="border border-gray-300 p-2">ID</th>
     <th class="border border-gray-300 p-2">Tipo</th>
     <th class="border border-gray-300 p-2">Periodo</th>
     <th class="border border-gray-300 p-2">Generado por</th>
     <th class="border border-gray-300 p-2">Fecha</th>
     <th class="border border-gray-300 p-2">Acciones</th>
    </tr>
   </thead>
   <tbody> <?php if (empty($reportes)): ?> <tr>
     <td colspan="6" class="text-center py-4 text-gray-500">No hay reportes generados</td>
    </tr> <?php else: ?> <?php foreach ($reportes as $reporte): ?> <tr>
     <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($reporte['id']); ?></td>
     <td class="border border-gray-300 p-2 text-center"> <?php 
                                $tipo = [
                                    'mensual' => 'Mensual',
                                    'trimestral' => 'Trimestral',
                                    'anual' => 'Anual'
                                ];
                                echo htmlspecialchars($tipo[$reporte['tipo']] ?? $reporte['tipo']); 
                                ?> </td>
     <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($reporte['periodo']); ?></td>
     <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($reporte['generado_por']); ?></td>
     <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($reporte['fecha_generacion']); ?>
     </td>
     <td class="border border-gray-300 p-2 text-center">
      <a href="/api.php?endpoint=descargar-reporte&id=<?php echo $reporte['id']; ?>"
       class="bg-blue-100 text-blue-800 p-1 rounded inline-block">
       <i class="fas fa-download"></i>
      </a>
      <button class="view-report-btn bg-green-100 text-green-800 p-1 rounded ml-1"
       data-id="<?php echo $reporte['id']; ?>">
       <i class="fas fa-eye"></i>
      </button>
      <button class="delete-report-btn bg-red-100 text-red-800 p-1 rounded ml-1"
       data-id="<?php echo $reporte['id']; ?>">
       <i class="fas fa-trash"></i>
      </button>
     </td>
    </tr> <?php endforeach; ?> <?php endif; ?> </tbody>
  </table>
 </div>
</div>
<!-- Modal para generar reporte -->
<div id="generateReportModal"
 class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
 <div class="bg-white p-6 rounded-lg w-full max-w-md">
  <div class="flex justify-between items-center mb-4">
   <h3 class="text-lg font-bold">Generar Nuevo Reporte</h3>
   <button id="closeReportModal" class="text-gray-500 hover:text-gray-700">
    <i class="fas fa-times"></i>
   </button>
  </div>
  <form id="reportForm">
   <div class="space-y-4">
    <div>
     <label for="newReportType" class="block text-sm font-medium text-gray-700">Tipo de Reporte</label>
     <select id="newReportType" name="tipo" class="mt-1 p-2 border rounded-md w-full" required>
      <option value="">Seleccione...</option>
      <option value="mensual">Mensual</option>
      <option value="trimestral">Trimestral</option>
      <option value="anual">Anual</option>
     </select>
    </div>
    <div>
     <label for="newReportMonth" class="block text-sm font-medium text-gray-700">Mes</label>
     <select id="newReportMonth" name="mes" class="mt-1 p-2 border rounded-md w-full">
      <option value="">Todos (para reportes anuales)</option> <?php for ($m = 1; $m <= 12; $m++): ?> <option
       value="<?php echo $m; ?>"><?php echo date('F', mktime(0, 0, 0, $m, 1)); ?></option> <?php endfor; ?>
     </select>
    </div>
    <div>
     <label for="newReportQuarter" class="block text-sm font-medium text-gray-700">Trimestre</label>
     <select id="newReportQuarter" name="trimestre" class="mt-1 p-2 border rounded-md w-full">
      <option value="">Todos (para reportes anuales)</option>
      <option value="1">Primer Trimestre (Ene-Mar)</option>
      <option value="2">Segundo Trimestre (Abr-Jun)</option>
      <option value="3">Tercer Trimestre (Jul-Sep)</option>
      <option value="4">Cuarto Trimestre (Oct-Dic)</option>
     </select>
    </div>
    <div>
     <label for="newReportYear" class="block text-sm font-medium text-gray-700">Año</label>
     <select id="newReportYear" name="anio" class="mt-1 p-2 border rounded-md w-full" required>
      <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?> <option value="<?php echo $y; ?>"
       <?php echo $y == date('Y') ? 'selected' : ''; ?>><?php echo $y; ?></option> <?php endfor; ?> </select>
    </div>
   </div>
   <div class="mt-6 flex justify-end space-x-2">
    <button type="button" id="cancelReportBtn" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400"> Cancelar
    </button>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"> Generar </button>
   </div>
  </form>
 </div>
</div>
<!-- Modal para vista previa -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
 <div class="bg-white p-6 rounded-lg w-full max-w-4xl max-h-[90vh] overflow-auto">
  <div class="flex justify-between items-center mb-4">
   <h3 class="text-lg font-bold">Vista Previa del Reporte</h3>
   <button id="closePreviewModal" class="text-gray-500 hover:text-gray-700">
    <i class="fas fa-times"></i>
   </button>
  </div>
  <div id="previewContent">
   <!-- Contenido cargado dinámicamente -->
  </div>
 </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
 // Abrir modal para generar reporte
 document.getElementById('generateReportBtn').addEventListener('click', function() {
  document.getElementById('generateReportModal').classList.remove('hidden');
 });
 // Cerrar modales
 document.getElementById('closeReportModal').addEventListener('click', function() {
  document.getElementById('generateReportModal').classList.add('hidden');
 });
 document.getElementById('cancelReportBtn').addEventListener('click', function() {
  document.getElementById('generateReportModal').classList.add('hidden');
 });
 document.getElementById('closePreviewModal').addEventListener('click', function() {
  document.getElementById('previewModal').classList.add('hidden');
 });
 // Manejar cambio de tipo de reporte
 document.getElementById('newReportType').addEventListener('change', function() {
  const type = this.value;
  document.getElementById('newReportMonth').disabled = type !== 'mensual';
  document.getElementById('newReportQuarter').disabled = type !== 'trimestral';
 });
 // Manejar envío del formulario de reporte
 document.getElementById('reportForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('/api.php?endpoint=generar-reporte', {
   method: 'POST',
   body: formData
  }).then(response => response.json()).then(data => {
   if (data.success) {
    window.location.reload();
   }
  });
 });
 // Vista previa de reporte
 document.querySelectorAll('.view-report-btn').forEach(btn => {
  btn.addEventListener('click', function() {
   const id = this.getAttribute('data-id');
   fetch(`/api.php?endpoint=vista-previa-reporte&id=${id}`).then(response => response.json()).then(data => {
    if (data.success) {
     document.getElementById('previewContent').innerHTML = data.html;
     document.getElementById('previewModal').classList.remove('hidden');
    }
   });
  });
 });
 // Eliminar reporte
 document.querySelectorAll('.delete-report-btn').forEach(btn => {
  btn.addEventListener('click', function() {
   const id = this.getAttribute('data-id');
   if (confirm('¿Estás seguro de eliminar este reporte?')) {
    fetch(`/api.php?endpoint=eliminar-reporte&id=${id}`, {
     method: 'DELETE'
    }).then(response => response.json()).then(data => {
     if (data.success) {
      window.location.reload();
     }
    });
   }
  });
 });
});
</script> <?php require_once __DIR__ . '/../../includes/footer.php'; ?>