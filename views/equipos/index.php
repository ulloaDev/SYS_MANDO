<?php
require_once __DIR__ . '/../../includes/header.php';

$equipos = $data['equipos'] ?? [];
$tiposEquipo = $data['tipos_equipo'] ?? [];
?> <div class="min-h-screen bg-gray-50">
 <!-- Sidebar --> <?php include_once __DIR__ . '/../../includes/sidebar.php'; ?>
 <!-- Main Content -->
 <div class="md:ml-64">
  <!-- Header -->
  <div class="bg-white shadow">
   <div class="px-4 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-gray-800">Gestión de Equipos</h1>
    <button id="nuevoEquipoBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
     <i class="fas fa-plus mr-2"></i>Nuevo Equipo </button>
   </div>
  </div>
  <!-- Content -->
  <main class="p-4">
   <!-- Filtros -->
   <div class="bg-white p-4 rounded-lg shadow mb-6">
    <form id="filtrosEquipos" class="grid grid-cols-1 md:grid-cols-4 gap-4">
     <div>
      <label for="filtroTipo" class="block text-sm font-medium text-gray-700">Tipo de Equipo</label>
      <select id="filtroTipo" name="tipo" class="mt-1 p-2 border rounded-md w-full">
       <option value="">Todos</option> <?php foreach ($tiposEquipo as $tipo): ?> <option
        value="<?php echo $tipo['id']; ?>"><?php echo htmlspecialchars($tipo['nombre']); ?></option>
       <?php endforeach; ?>
      </select>
     </div>
     <div>
      <label for="filtroEstado" class="block text-sm font-medium text-gray-700">Estado</label>
      <select id="filtroEstado" name="estado" class="mt-1 p-2 border rounded-md w-full">
       <option value="">Todos</option>
       <option value="1">Activo</option>
       <option value="0">Inactivo</option>
      </select>
     </div>
     <div class="flex items-end">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
       <i class="fas fa-filter mr-2"></i>Filtrar </button>
     </div>
    </form>
   </div>
   <!-- Tabla de equipos -->
   <div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full text-sm">
     <thead class="bg-gray-100">
      <tr>
       <th class="p-3 text-left">ID</th>
       <th class="p-3 text-left">Nombre</th>
       <th class="p-3 text-left">Tipo</th>
       <th class="p-3 text-left">Serie</th>
       <th class="p-3 text-left">Ubicación</th>
       <th class="p-3 text-left">Estado</th>
       <th class="p-3 text-left">Acciones</th>
      </tr>
     </thead>
     <tbody> <?php foreach ($equipos as $equipo): ?> <tr class="border-t hover:bg-gray-50">
       <td class="p-3"><?php echo htmlspecialchars($equipo['id']); ?></td>
       <td class="p-3"><?php echo htmlspecialchars($equipo['nombre']); ?></td>
       <td class="p-3"><?php echo htmlspecialchars($equipo['tipo_nombre']); ?></td>
       <td class="p-3"><?php echo htmlspecialchars($equipo['serie']); ?></td>
       <td class="p-3"><?php echo htmlspecialchars($equipo['ubicacion']); ?></td>
       <td class="p-3">
        <span
         class="px-2 py-1 rounded-full text-xs 
                                    <?php echo $equipo['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
         <?php echo $equipo['activo'] ? 'Activo' : 'Inactivo'; ?> </span>
       </td>
       <td class="p-3">
        <button class="editarEquipoBtn bg-yellow-100 text-yellow-800 p-1 rounded"
         data-id="<?php echo $equipo['id']; ?>">
         <i class="fas fa-edit"></i>
        </button>
        <button
         class="toggleEquipoBtn <?php echo $equipo['activo'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?> p-1 rounded ml-1"
         data-id="<?php echo $equipo['id']; ?>" data-status="<?php echo $equipo['activo'] ? '1' : '0'; ?>">
         <i class="fas <?php echo $equipo['activo'] ? 'fa-times' : 'fa-check'; ?>"></i>
        </button>
       </td>
      </tr> <?php endforeach; ?> </tbody>
    </table>
   </div>
  </main>
 </div>
</div>
<!-- Modal para equipos -->
<div id="equipoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
 <div class="bg-white p-6 rounded-lg w-full max-w-md">
  <div class="flex justify-between items-center mb-4">
   <h3 id="equipoModalTitle" class="text-lg font-bold">Nuevo Equipo</h3>
   <button id="closeEquipoModal" class="text-gray-500 hover:text-gray-700">
    <i class="fas fa-times"></i>
   </button>
  </div>
  <form id="equipoForm">
   <input type="hidden" id="equipoId" name="id" value="">
   <div class="space-y-4">
    <div>
     <label for="equipoNombre" class="block text-sm font-medium text-gray-700">Nombre</label>
     <input type="text" id="equipoNombre" name="nombre" class="mt-1 p-2 border rounded-md w-full" required>
    </div>
    <div>
     <label for="equipoTipo" class="block text-sm font-medium text-gray-700">Tipo</label>
     <select id="equipoTipo" name="tipo_id" class="mt-1 p-2 border rounded-md w-full" required>
      <option value="">Seleccione...</option> <?php foreach ($tiposEquipo as $tipo): ?> <option
       value="<?php echo $tipo['id']; ?>"><?php echo htmlspecialchars($tipo['nombre']); ?></option> <?php endforeach; ?>
     </select>
    </div>
    <div>
     <label for="equipoSerie" class="block text-sm font-medium text-gray-700">Número de Serie</label>
     <input type="text" id="equipoSerie" name="serie" class="mt-1 p-2 border rounded-md w-full">
    </div>
    <div>
     <label for="equipoUbicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
     <input type="text" id="equipoUbicacion" name="ubicacion" class="mt-1 p-2 border rounded-md w-full">
    </div>
    <div>
     <label for="equipoEstado" class="block text-sm font-medium text-gray-700">Estado</label>
     <select id="equipoEstado" name="activo" class="mt-1 p-2 border rounded-md w-full">
      <option value="1">Activo</option>
      <option value="0">Inactivo</option>
     </select>
    </div>
   </div>
   <div class="mt-6 flex justify-end space-x-2">
    <button type="button" id="cancelEquipoBtn" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400"> Cancelar
    </button>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"> Guardar </button>
   </div>
  </form>
 </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
 // Abrir modal para nuevo equipo
 document.getElementById('nuevoEquipoBtn').addEventListener('click', function() {
  document.getElementById('equipoModalTitle').textContent = 'Nuevo Equipo';
  document.getElementById('equipoForm').reset();
  document.getElementById('equipoId').value = '';
  document.getElementById('equipoModal').classList.remove('hidden');
 });
 // Cerrar modal
 document.getElementById('closeEquipoModal').addEventListener('click', function() {
  document.getElementById('equipoModal').classList.add('hidden');
 });
 document.getElementById('cancelEquipoBtn').addEventListener('click', function() {
  document.getElementById('equipoModal').classList.add('hidden');
 });
 // Manejar envío del formulario
 document.getElementById('equipoForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const isNew = !formData.get('id');
  fetch(`/api.php?endpoint=${isNew ? 'crear-equipo' : 'actualizar-equipo'}`, {
   method: 'POST',
   body: formData
  }).then(response => response.json()).then(data => {
   if (data.success) {
    window.location.reload();
   }
  });
 });
 // Editar equipo
 document.querySelectorAll('.editarEquipoBtn').forEach(btn => {
  btn.addEventListener('click', function() {
   const id = this.getAttribute('data-id');
   fetch(`/api.php?endpoint=detalle-equipo&id=${id}`).then(response => response.json()).then(data => {
    if (data.success) {
     document.getElementById('equipoModalTitle').textContent = 'Editar Equipo';
     document.getElementById('equipoId').value = data.data.id;
     document.getElementById('equipoNombre').value = data.data.nombre;
     document.getElementById('equipoTipo').value = data.data.tipo_id;
     document.getElementById('equipoSerie').value = data.data.serie;
     document.getElementById('equipoUbicacion').value = data.data.ubicacion;
     document.getElementById('equipoEstado').value = data.data.activo ? '1' : '0';
     document.getElementById('equipoModal').classList.remove('hidden');
    }
   });
  });
 });
 // Activar/Desactivar equipo
 document.querySelectorAll('.toggleEquipoBtn').forEach(btn => {
  btn.addEventListener('click', function() {
   const id = this.getAttribute('data-id');
   const currentStatus = this.getAttribute('data-status');
   const newStatus = currentStatus === '1' ? '0' : '1';
   if (confirm(`¿Estás seguro de ${newStatus === '1' ? 'activar' : 'desactivar'} este equipo?`)) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('activo', newStatus);
    fetch('/api.php?endpoint=toggle-equipo', {
     method: 'POST',
     body: formData
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