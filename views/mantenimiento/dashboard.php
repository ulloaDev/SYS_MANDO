<?php
require_once __DIR__ . '/../../includes/header.php';

// Verificar autenticación
if (!isset($_SESSION['usuario'])) {
    header('Location: /login');
    exit;
}

$estadisticas = $data['estadisticas'] ?? [];
?> <div class="min-h-screen bg-gray-50">
 <!-- Sidebar --> <?php include_once __DIR__ . '/../../includes/sidebar.php'; ?>
 <!-- Main Content -->
 <div class="md:ml-64">
  <!-- Header -->
  <div class="bg-white shadow">
   <div class="px-4 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
    <div class="flex items-center space-x-4">
     <span class="text-sm"><?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
     <button id="logoutBtn" class="text-red-600 hover:text-red-800">
      <i class="fas fa-sign-out-alt"></i>
     </button>
    </div>
   </div>
  </div>
  <!-- Content -->
  <main class="p-4">
   <!-- Estadísticas -->
   <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg shadow">
     <div class="text-blue-600 text-2xl font-bold"><?php echo $estadisticas['total_mantenimientos'] ?? 0; ?></div>
     <div class="text-gray-600">Mantenimientos</div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
     <div class="text-green-600 text-2xl font-bold"><?php echo $estadisticas['completados'] ?? 0; ?></div>
     <div class="text-gray-600">Completados</div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
     <div class="text-yellow-600 text-2xl font-bold"><?php echo $estadisticas['pendientes'] ?? 0; ?></div>
     <div class="text-gray-600">Pendientes</div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
     <div class="text-red-600 text-2xl font-bold"><?php echo $estadisticas['atrasados'] ?? 0; ?></div>
     <div class="text-gray-600">Atrasados</div>
    </div>
   </div>
   <!-- Gráficos y resúmenes -->
   <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-4 rounded-lg shadow">
     <h2 class="text-lg font-semibold mb-4">Mantenimientos por Estado</h2>
     <div id="chartEstado" class="h-64"></div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
     <h2 class="text-lg font-semibold mb-4">Mantenimientos Recientes</h2>
     <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
       <thead>
        <tr class="bg-gray-100">
         <th class="p-2 text-left">Equipo</th>
         <th class="p-2 text-left">Técnico</th>
         <th class="p-2 text-left">Estado</th>
        </tr>
       </thead>
       <tbody> <?php foreach ($data['mantenimientos_recientes'] ?? [] as $mant): ?> <tr class="border-t">
         <td class="p-2"><?php echo htmlspecialchars($mant['equipo_nombre']); ?></td>
         <td class="p-2"><?php echo htmlspecialchars($mant['tecnico_nombre']); ?></td>
         <td class="p-2">
          <span
           class="px-2 py-1 rounded-full text-xs
           <?php echo $mant['estado'] === 'Completado' ? 'bg-green-100 text-green-800': ($mant['estado'] === 'En Proceso' ? 'bg-blue-100 text-blue-800':'bg-yellow-100 text-yellow-800');?> "><?php echo htmlspecialchars($mant['estado']); ?>
          </span>
         </td>
        </tr> <?php endforeach; ?> </tbody>
      </table>
     </div>
    </div>
   </div>
  </main>
 </div>
</div>
<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
 // Gráfico de estados
 const ctx = document.getElementById('chartEstado').getContext('2d');
 const chart = new Chart(ctx, {
  type: 'doughnut',
  data: {
   labels: ['Completados', 'En Proceso', 'Pendientes', 'Atrasados'],
   datasets: [{
    data: [
     <?php echo $estadisticas['completados'] ?? 0; ?>,
     <?php echo $estadisticas['en_proceso'] ?? 0; ?>,
     <?php echo $estadisticas['pendientes'] ?? 0; ?>,
     <?php echo $estadisticas['atrasados'] ?? 0; ?>
    ],
    backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444']
   }]
  }
 });
 // Logout
 document.getElementById('logoutBtn').addEventListener('click', function() {
  fetch('/api.php?endpoint=logout').then(response => response.json()).then(data => {
   if (data.success) {
    window.location.href = '/login';
   }
  });
 });
});
</script> <?php require_once __DIR__ . '/../../includes/footer.php'; ?>