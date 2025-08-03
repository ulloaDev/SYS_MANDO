<?php
require_once __DIR__ . '/../../includes/header.php';

$tecnicos = $data['tecnicos'] ?? [];
?> <div class="min-h-screen bg-gray-50 p-4">
 <!-- Header -->
 <div class="bg-blue-900 text-white p-4 rounded-t-lg">
  <div class="flex justify-between items-center">
   <div class="flex items-center space-x-4">
    <i class="fas fa-users text-2xl"></i>
    <h1 class="text-xl font-bold">GESTIÓN DE TÉCNICOS</h1>
   </div>
   <div class="flex items-center space-x-4">
    <button id="newTechBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
     <i class="fas fa-plus mr-2"></i>Nuevo Técnico </button>
   </div>
  </div>
 </div>
 <!-- Tabla de técnicos -->
 <div class="bg-white border border-gray-200 overflow-x-auto">
  <table class="w-full text-sm">
   <thead>
    <tr class="bg-blue-100">
     <th class="border border-gray-300 p-2">ID</th>
     <th class="border border-gray-300 p-2">Nombre</th>
     <th class="border border-gray-300 p-2">Especialidad</th>
     <th class="border border-gray-300 p-2">Contacto</th>
     <th class="border border-gray-300 p-2">Estado</th>
     <th class="border border-gray-300 p-2">Acciones</th>
    </tr>
   </thead>
   <tbody> <?php if (empty($tecnicos)): ?> <tr>
     <td colspan="6" class="text-center py-4 text-gray-500">No hay técnicos registrados</td>
    </tr> <?php else: ?> <?php foreach ($tecnicos as $tecnico): ?> <tr>
     <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($tecnico['id']); ?></td>
     <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($tecnico['nombre']); ?></td>
     <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($tecnico['especialidad']); ?></td>
     <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($tecnico['telefono']); ?></td>
     <td class="border border-gray-300 p-2 text-center">
      <span
       class="px-2 py-1 rounded-full text-xs 
                                    <?php echo $tecnico['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
       <?php echo $tecnico['activo'] ? 'Activo' : 'Inactivo'; ?> </span>
     </td>
     <td class="border border-gray-300 p-2 text-center">
      <button class="edit-tech-btn bg-yellow-100 text-yellow-800 p-1 rounded" data-id="<?php echo $tecnico['id']; ?>">
       <i class="fas fa-edit"></i>
      </button>
      <button
       class="toggle-tech-btn <?php echo $tecnico['activo'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?> p-1 rounded ml-1"
       data-id="<?php echo $tecnico['id']; ?>" data-status="<?php echo $tecnico['activo'] ? '1' : '0'; ?>">
       <i class="fas <?php echo $tecnico['activo'] ? 'fa-times' : 'fa-check'; ?>"></i>
      </button>
     </td>
    </tr> <?php endforeach; ?> <?php endif; ?> </tbody>
  </table>
 </div>
</div>
<!-- Modal para técnicos -->
<div id="techModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
 <div class="bg-white p-6 rounded-lg w-full max-w-md">
  <div class="flex justify-between items-center mb-4">
   <h3 id="techModalTitle" class="text-lg font-bold">Nuevo Técnico</h3>
   <button id="closeTechModal" class="text-gray-500 hover:text-gray-700">
    <i class="fas fa-times"></i>
   </button>
  </div>
  <form id="techForm">
   <input type="hidden" id="techId" name="id" value="">
   <div class="space-y-4">
    <div>
     <label for="techName" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
     <input type="text" id="techName" name="nombre" class="mt-1 p-2 border rounded-md w-full" required>
    </div>
    <div>
     <label for="techSpecialty" class="block text-sm font-medium text-gray-700">Especialidad</label>
     <input type="text" id="techSpecialty" name="especialidad" class="mt-1 p-2 border rounded-md w-full" required>
    </div>
    <div>
     <label for="techPhone" class="block text-sm font-medium text-gray-700">Teléfono</label>
     <input type="tel" id="techPhone" name="telefono" class="mt-1 p-2 border rounded-md w-full">
    </div>
    <div>
     <label for="techEmail" class="block text-sm font-medium text-gray-700">Email</label>
     <input type="email" id="techEmail" name="email" class="mt-1 p-2 border rounded-md w-full">
    </div>
    <div>
     <label for="techStatus" class="block text-sm font-medium text-gray-700">Estado</label>
     <select id="techStatus" name="activo" class="mt-1 p-2 border rounded-md w-full">
      <option value="1">Activo</option>
      <option value="0">Inactivo</option>
     </select>
    </div>
   </div>
   <div class="mt-6 flex justify-end space-x-2">
    <button type="button" id="cancelTechBtn" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400"> Cancelar
    </button>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"> Guardar </button>
   </div>
  </form>
 </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
 // Abrir modal para nuevo técnico
 document.getElementById('newTechBtn').addEventListener('click', function() {
  document.getElementById('techModalTitle').textContent = 'Nuevo Técnico';
  document.getElementById('techForm').reset();
  document.getElementById('techId').value = '';
  document.getElementById('techModal').classList.remove('hidden');
 });
 // Cerrar modal
 document.getElementById('closeTechModal').addEventListener('click', function() {
  document.getElementById('techModal').classList.add('hidden');
 });
 document.getElementById('cancelTechBtn').addEventListener('click', function() {
  document.getElementById('techModal').classList.add('hidden');
 });
 // Manejar envío del formulario
 document.getElementById('techForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const isNew = !formData.get('id');
  fetch(`/api.php?endpoint=${isNew ? 'crear-tecnico' : 'actualizar-tecnico'}`, {
   method: 'POST',
   body: formData
  }).then(response => response.json()).then(data => {
   if (data.success) {
    window.location.reload();
   }
  });
 });
 // Editar técnico
 document.querySelectorAll('.edit-tech-btn').forEach(btn => {
  btn.addEventListener('click', function() {
   const id = this.getAttribute('data-id');
   fetch(`/api.php?endpoint=detalle-tecnico&id=${id}`).then(response => response.json()).then(data => {
    if (data.success) {
     document.getElementById('techModalTitle').textContent = 'Editar Técnico';
     document.getElementById('techId').value = data.data.id;
     document.getElementById('techName').value = data.data.nombre;
     document.getElementById('techSpecialty').value = data.data.especialidad;
     document.getElementById('techPhone').value = data.data.telefono;
     document.getElementById('techEmail').value = data.data.email;
     document.getElementById('techStatus').value = data.data.activo ? '1' : '0';
     document.getElementById('techModal').classList.remove('hidden');
    }
   });
  });
 });
 // Activar/Desactivar técnico
 document.querySelectorAll('.toggle-tech-btn').forEach(btn => {
  btn.addEventListener('click', function() {
   const id = this.getAttribute('data-id');
   const currentStatus = this.getAttribute('data-status');
   const newStatus = currentStatus === '1' ? '0' : '1';
   if (confirm(`¿Estás seguro de ${newStatus === '1' ? 'activar' : 'desactivar'} este técnico?`)) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('activo', newStatus);
    fetch('/api.php?endpoint=toggle-tecnico', {
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