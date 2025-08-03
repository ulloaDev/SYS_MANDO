<?php
require_once __DIR__ . '/../../includes/header.php';

$mantenimientos = $data['mantenimientos'] ?? [];
$filtros = $data['filtros'] ?? [];
?> <div class="min-h-screen bg-gray-50 p-4">
 <!-- Header -->
 <div class="bg-blue-900 text-white p-4 rounded-t-lg">
  <div class="flex justify-between items-center">
   <div class="flex items-center space-x-4">
    <i class="fas fa-list-alt text-2xl"></i>
    <h1 class="text-xl font-bold">LISTA DE MANTENIMIENTOS</h1>
   </div>
   <div class="flex items-center space-x-4 text-sm">
    <span>Total registros: <?php echo count($mantenimientos); ?></span>
   </div>
  </div>
 </div>
 <!-- Filtros -->
 <div class="bg-white p-4 border-x border-gray-200">
  <form id="filtrosForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
   <div>
    <label for="fecha_desde" class="block text-sm font-medium text-gray-700">Desde</label>
    <input type="date" id="fecha_desde" name="fecha_desde"
     value="<?php echo htmlspecialchars($filtros['fecha_desde'] ?? ''); ?>" class="mt-1 p-2 border rounded-md w-full">
   </div>
   <div>
    <label for="fecha_hasta" class="block text-sm font-medium text-gray-700">Hasta</label>
    <input type="date" id="fecha_hasta" name="fecha_hasta"
     value="<?php echo htmlspecialchars($filtros['fecha_hasta'] ?? ''); ?>" class="mt-1 p-2 border rounded-md w-full">
   </div>
   <div>
    <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
    <select id="estado" name="estado" class="mt-1 p-2 border rounded-md w-full">
     <option value="">Todos</option>
     <option value="Programado" <?php echo ($filtros['estado'] ?? '') === 'Programado' ? 'selected' : ''; ?>>Programado
     </option>
     <option value="En Proceso" <?php echo ($filtros['estado'] ?? '') === 'En Proceso' ? 'selected' : ''; ?>>En Proceso
     </option>
     <option value="Completado" <?php echo ($filtros['estado'] ?? '') === 'Completado' ? 'selected' : ''; ?>>Completado
     </option>
    </select>
   </div>
   <div class="flex items-end">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
     <i class="fas fa-filter mr-2"></i>Filtrar </button>
   </div>
  </form>
 </div>
 <!-- Tabla de resultados -->
 <div class="bg-white border border-gray-200 overflow-x-auto">
  <table class="w-full text-sm">
   <thead>
    <tr class="bg-blue-100">
     <th class="border border-gray-300 p-2">ID</th>
     <th class="border border-gray-300 p-2">Técnico</th>
     <th class="border border-gray-300 p-2">Equipo</th>
     <th class="border border-gray-300 p-2">Fecha Programada</th>
     <th class="border border-gray-300 p-2">Estado</th>
     <th class="border border-gray-300 p-2">Prioridad</th>
     <th class="border border-gray-300 p-2">Acciones</th>
    </tr>
   </thead>
   <tbody> <?php if (empty($mantenimientos)): ?> <tr>
     <td colspan="7" class="text-center py-4 text-gray-500">No se encontraron mantenimientos</td>
    </tr> <?php else: ?> <?php foreach ($mantenimientos as $mant): ?> <tr
     class="<?php echo $mant['prioridad'] === 'Alta' ? 'bg-red-50' : ''; ?>">
     <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($mant['id']); ?></td>
     <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($mant['tecnico_nombre']); ?></td>
     <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($mant['equipo_nombre']); ?></td>
     <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($mant['fecha_programada']); ?></td>
     <td class="border border-gray-300 p-2 text-center">
      <span
       class="px-2 py-1 rounded-full text-xs 
                                    <?php echo $mant['estado'] === 'Completado' ? 'bg-green-100 text-green-800' : 
                                           ($mant['estado'] === 'En Proceso' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'); ?>">
       <?php echo htmlspecialchars($mant['estado']); ?> </span>
     </td>
     <td class="border border-gray-300 p-2 text-center"><?php echo htmlspecialchars($mant['prioridad']); ?></td>
     <td class="border border-gray-300 p-2 text-center">
      <button class="view-btn bg-blue-100 text-blue-800 p-1 rounded" data-id="<?php echo $mant['id']; ?>">
       <i class="fas fa-eye"></i>
      </button>
      <button class="edit-btn bg-yellow-100 text-yellow-800 p-1 rounded ml-1" data-id="<?php echo $mant['id']; ?>">
       <i class="fas fa-edit"></i>
      </button>
     </td>
    </tr> <?php endforeach; ?> <?php endif; ?> </tbody>
  </table>
 </div>
 <!-- Paginación --> <?php if (isset($data['paginacion'])): ?> <div class="mt-4 flex justify-center">
  <nav class="inline-flex rounded-md shadow"> <?php foreach ($data['paginacion']['links'] as $link): ?> <a
    href="<?php echo htmlspecialchars($link['url']); ?>" class="<?php echo $link['active'] ? 'bg-blue-600 text-white' : 'bg-white text-blue-600'; ?> 
                              px-4 py-2 border border-gray-300 hover:bg-blue-50">
    <?php echo htmlspecialchars($link['label']); ?> </a> <?php endforeach; ?> </nav>
 </div> <?php endif; ?>
</div>
<!-- Modal para ver detalles -->
<div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
 <div class="bg-white p-6 rounded-lg w-full max-w-2xl">
  <div class="flex justify-between items-center mb-4">
   <h3 class="text-lg font-bold">Detalles del Mantenimiento</h3>
   <button id="closeDetailModal" class="text-gray-500 hover:text-gray-700">
    <i class="fas fa-times"></i>
   </button>
  </div>
  <div id="modalContent" class="space-y-4">
   <!-- Contenido cargado dinámicamente -->
  </div>
 </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
 // Manejar clic en botones de ver
 document.querySelectorAll('.view-btn').forEach(btn => {
  btn.addEventListener('click', function() {
   const id = this.getAttribute('data-id');
   fetch(`/api.php?endpoint=detalle-mantenimiento&id=${id}`).then(response => response.json()).then(data => {
    if (data.success) {
     document.getElementById('modalContent').innerHTML = `
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="font-semibold">Técnico:</p>
                                    <p>${data.data.tecnico_nombre}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Equipo:</p>
                                    <p>${data.data.equipo_nombre}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Fecha Programada:</p>
                                    <p>${data.data.fecha_programada}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Estado:</p>
                                    <p>${data.data.estado}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="font-semibold">Notas:</p>
                                    <p class="bg-gray-100 p-2 rounded">${data.data.notas || 'Sin notas'}</p>
                                </div>
                            </div>
                        `;
     document.getElementById('detailModal').classList.remove('hidden');
    }
   });
  });
 });
 // Cerrar modal
 document.getElementById('closeDetailModal').addEventListener('click', function() {
  document.getElementById('detailModal').classList.add('hidden');
 });
});
</script> <?php require_once __DIR__ . '/../../includes/footer.php'; ?>