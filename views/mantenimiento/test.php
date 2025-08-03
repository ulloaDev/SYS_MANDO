<?php 
require_once __DIR__ . '/../../includes/header.php';

// Los datos ya vienen del controlador en $data
$clubes = $data['clubes'] ?? [];
$programacion = $data['programacion'] ?? []; // Asegurar array aunque esté vacío
$estadisticas = $data['estadisticas'] ?? [];
$year = $data['year'] ?? date('Y');
$clubId = $data['clubId'] ?? null;
$quarters = $data['quarters'] ?? [];
$tecnicos = $data['tecnicos'] ?? []; // Ahora contiene technician_name, usuario, equipo_id, equipo_nombre, equipment_type_name

// --- INICIO DE DEPURACIÓN ---
// Imprime los datos de programación recibidos en el log de errores de PHP
error_log("Datos de programación recibidos en calendario.php: " . print_r($programacion, true));
// Imprime los datos de técnicos recibidos en el log de errores de PHP
error_log("Datos de técnicos recibidos en calendario.php: " . print_r($tecnicos, true));
// --- FIN DE DEPURACIÓN ---

// Mapear la programación para fácil acceso por técnico, equipo, mes y semana
$programacion_map = [];
foreach ($programacion as $item) {
    $techId = $item['technician_id'] ?? 'unknown';
    $equipoId = $item['equipo_id'] ?? 'null'; // Usar 'null' como string si es NULL
    $month = $item['scheduled_month'] ?? 'unknown';
    $week = $item['scheduled_week'] ?? 'unknown';
    $programacion_map[$techId . '-' . $equipoId][$month][$week] = $item;
}

// --- INICIO DE DEPURACIÓN ---
// Imprime el mapa de programación construido en el log de errores de PHP
error_log("Mapa de programación construido en calendario.php: " . print_r($programacion_map, true));
// --- FIN DE DEPURACIÓN ---

// Debug: Verificar datos recibidos (opcional, quitar en producción)
// echo '<pre>'.print_r($data, true).'</pre>';
?> <div class="min-h-screen bg-gray-50 p-4">
    <!-- Header -->
    <div class="bg-blue-900 text-white p-4 rounded-t-lg">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <i class="fas fa-tools text-2xl"></i>
                <h1 class="text-xl font-bold">PROGRAMACIÓN DE MANTENIMIENTO</h1>
            </div>
            <div class="flex items-center space-x-4 text-sm">
                <span>Ubicación: Club <?php 
                    if ($clubId) {
                        $clubName = 'Desconocido';
                        foreach ($clubes as $club) {
                            if ($club['id'] == $clubId) {
                                $clubName = $club['name'];
                                break;
                            }
                        }
                        echo htmlspecialchars($clubName);
                    } else {
                        echo 'Todos';
                    }
                    ?> </span>
                <span>Coordinado por: IT</span>
            </div>
        </div>
        <!-- Objective -->
        <div class="mt-2 text-sm">
            <strong>Objetivo:</strong> Mantener la eficiencia y durabilidad de los equipos del Club con un mantenimiento
            preventivo de manera trimestral.
        </div>
    </div>
    <!-- Controls -->
    <div class="bg-white p-4 border-x border-gray-200 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <select id="clubSelect" class="border border-gray-300 rounded px-3 py-1">
                <option value="">Todos los clubes</option> <?php foreach ($clubes as $club): ?> <option
                    value="<?php echo $club['id'] ?>" <?php echo $clubId == $club['id'] ? 'selected' : '' ?>>
                    <?php echo htmlspecialchars($club['name']) ?> </option> <?php endforeach; ?>
            </select>
            <select id="yearSelect" class="border border-gray-300 rounded px-3 py-1">
                <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?> <option value="<?php echo $y ?>"
                    <?php echo $year == $y ? 'selected' : '' ?>> <?php echo $y ?> </option> <?php endfor; ?> </select>
            <div class="flex items-center space-x-2">
                <i class="fas fa-filter"></i>
                <span class="text-sm">Distribución Semanal - <?php echo htmlspecialchars($year) ?></span>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded flex items-center space-x-2 hover:bg-blue-700">
                <i class="fas fa-plus"></i>
                <span>Nuevo</span>
            </button>
            <button id="saveBtn"
                class="bg-green-600 text-white px-4 py-2 rounded flex items-center space-x-2 hover:bg-green-700">
                <i class="fas fa-save"></i>
                <span>Guardar</span>
            </button>
        </div>
    </div>
    <!-- Schedule Table -->
    <div class="bg-white border border-gray-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-blue-100">
                    <th class="border border-gray-300 p-2 text-left min-w-32">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-user"></i>
                            <span>Usuario Equipo</span>
                        </div>
                    </th>
                    <th class="border border-gray-300 p-2 w-28">Equipo</th>
                    <th class="border border-gray-300 p-2 w-28">Serie</th>
                    <th class="border border-gray-300 p-2 w-20">Activo</th> <?php foreach ($quarters as $quarter): ?>
                    <th class="border border-gray-300 p-2">
                        <div class="text-center">
                            <div class="font-bold text-blue-900 mb-1"><?php echo htmlspecialchars($quarter['name']) ?>
                            </div>
                            <div class="grid grid-cols-5 gap-1 text-xs"> <?php foreach ($quarter['weeks'] as $week): ?>
                                <div class="bg-blue-50 p-1 rounded"><?php echo $week ?></div> <?php endforeach; ?>
                            </div>
                            <div class="text-xs mt-1 grid grid-cols-5 gap-1">
                                <span>(2-8)</span>
                                <span>(9-15)</span>
                                <span>(16-22)</span>
                                <span>(23-29)</span>
                                <span>(30-31)</span>
                            </div>
                        </div>
                    </th> <?php endforeach; ?>
                </tr>
            </thead>
            <tbody> <?php if (empty($tecnicos)): ?> <tr>
                    <td colspan="<?php echo 4 + count($quarters) ?>" class="text-center py-4 text-gray-500"> No hay
                        técnicos o equipos asociados para el filtro actual. </td>
                </tr> <?php else: ?> <?php foreach ($tecnicos as $index => $tech): 
                        $techId = $tech['id'] ?? null;
                        $techUser = $tech['usuario'] ?? $tech['technician_name'] ?? 'Sin asignar'; 
                        $equipoId = $tech['equipo_id'] ?? 'null'; 
                        $equipoNombre = $tech['equipo_nombre'] ?? 'N/A'; 
                        $equipmentTypeName = $tech['equipment_type_name'] ?? 'N/A'; 
                        $equipoUsuario = $tech['equipo_usuario'] ?? 'N/A'; 
                        $equipoSerie = $tech['equipo_serie'] ?? 'N/A'; 
                        $equipoActivo = $tech['equipo_activo'] ?? 'N/A'; 

                        $initials = substr(strtoupper($techUser), 0, 2); 
                    ?> <tr class="<?php echo $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?>"
                    data-technician-id="<?= htmlspecialchars($techId) ?>"
                    data-equipo-id="<?= htmlspecialchars($equipoId) ?>"
                    data-equipment-type-name="<?= htmlspecialchars($equipmentTypeName) ?>"
                    data-club-id="<?= htmlspecialchars($clubId) ?>" data-year="<?= htmlspecialchars($year) ?>">
                    <td class="border border-gray-300 p-2 bg-blue-900 text-white font-medium">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-xs">
                                <?php echo htmlspecialchars($initials) ?> </div>
                            <span><?php echo htmlspecialchars($equipoUsuario) ?></span>
                        </div>
                    </td>
                    <td class="border border-gray-300 p-2 text-center bg-blue-900 text-white text-xs">
                        <?php echo htmlspecialchars($equipoNombre) ?> </td>
                    <td class="border border-gray-300 p-2 text-center bg-blue-900 text-white text-xs">
                        <?php echo htmlspecialchars($equipoSerie) ?> </td>
                    <td class="border border-gray-300 p-2 text-center bg-blue-900 text-white text-xs">
                        <?php echo htmlspecialchars($equipoActivo) ?> </td> <?php foreach ($quarters as $quarter): 
                            $month = $quarter['month'] ?? null;
                        ?> <td class="border border-gray-300 p-1">
                        <div class="grid grid-cols-5 gap-1 h-12"> <?php foreach ($quarter['weeks'] as $weekIdx => $week): 
                                    $weekNum = $weekIdx + 1; 
                                    $slot = $programacion_map[$techId . '-' . $equipoId][$month][$weekNum] ?? null;
                                    
                                    $isScheduled = $slot !== null;
                                    $statusClass = '';
                                    $statusIcon = '';
                                    $priorityIcon = '';
                                    $tooltip = 'Click para programar';
                                    
                                    if ($isScheduled) {
                                        $status = $slot['status'] ?? 'Programado';
                                        $priority = $slot['priority'] ?? 'Media';
                                        $equipmentDisplay = $slot['equipo_nombre_programado'] ?? $slot['equipment_type'] ?? 'Equipo'; 
                                        
                                        switch ($status) {
                                            case 'Completado':
                                                $statusClass = 'bg-green-500 text-white';
                                                $statusIcon = 'fas fa-check-circle';
                                                break;
                                            case 'En Proceso':
                                                $statusClass = 'bg-blue-500 text-white';
                                                $statusIcon = 'fas fa-clock';
                                                break;
                                            case 'Programado':
                                            case 'Reprogramado': 
                                            default:
                                                $statusClass = 'bg-orange-500 text-white';
                                                break;
                                        }
                                        
                                        if ($priority === 'Alta' || $priority === 'Crítica') { 
                                            $priorityIcon = 'fas fa-exclamation-circle text-red-300';
                                        }
                                        
                                        $tooltip = htmlspecialchars("$equipmentDisplay - Estado: $status - Prioridad: $priority");
                                    }
                                ?> <div data-tech-id="<?= htmlspecialchars($techId) ?>"
                                data-equipo-id="<?= htmlspecialchars($equipoId) ?>"
                                data-equipment-type-name="<?= htmlspecialchars($equipmentTypeName) ?>"
                                data-month="<?= htmlspecialchars($month) ?>"
                                data-week="<?= htmlspecialchars($weekNum) ?>"
                                data-status="<?= htmlspecialchars($isScheduled ? $status : 'Unscheduled') ?>"
                                class="schedule-cell cursor-pointer rounded transition-all duration-200 flex items-center justify-center relative <?= $isScheduled ? $statusClass : 'bg-gray-100 hover:bg-gray-200' ?>"
                                title="<?= $tooltip ?>"> <?php if ($isScheduled): ?> <div class="text-center">
                                    <div class="text-xs font-bold">M</div> <?php if ($statusIcon): ?> <i
                                        class="<?= $statusIcon ?> text-xs absolute top-0 right-0"></i> <?php endif; ?>
                                    <?php if ($priorityIcon): ?> <i
                                        class="<?= $priorityIcon ?> text-xs absolute bottom-0 left-0"></i>
                                    <?php endif; ?>
                                </div> <?php endif; ?> </div> <?php endforeach; ?> </div>
                    </td> <?php endforeach; ?>
                </tr> <?php endforeach; ?> <?php endif; ?> </tbody>
        </table>
    </div>
    <!-- Footer Info -->
    <div class="bg-blue-900 text-white p-4 rounded-b-lg">
        <div class="grid grid-cols-4 gap-4 text-sm"> <?php foreach ($quarters as $quarter): ?> <div class="text-center">
                <div class="font-bold"><?php echo strtoupper($quarter['name']) ?></div>
                <div class="text-xs">HORA: 9:30 AM - 4:30 PM</div>
            </div> <?php endforeach; ?> </div>
        <div class="mt-4 pt-4 border-t border-blue-700">
            <h3 class="font-bold mb-2">CONCLUSIONES Y RECOMENDACIONES</h3>
            <p class="text-sm">El cronograma de rutinas está programado; los mantenimientos se debe tener un nivel de
                dedicación en los encontrados; la duración total estimó que se han implementado en el Club y que gracias
                al apoyo de los gerentes, supervisores y colaboradores se ha logrado mantener el correcto estado de las
                terminales y equipos de uso.</p>
        </div>
    </div>
    <!-- Legend -->
    <div class="mt-4 bg-white p-4 rounded-lg border border-gray-200">
        <h3 class="font-bold mb-2 flex items-center">
            <i class="fas fa-info-circle mr-2"></i> Leyenda
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-orange-500 rounded"></div>
                <span>Programado</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                <span>En Proceso</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-green-500 rounded"></div>
                <span>Completado</span>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span>Alta Prioridad</span>
            </div>
        </div>
    </div>
    <!-- Statistics -->
    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
            <div class="text-2xl font-bold text-blue-600"><?php echo $estadisticas['total_programados'] ?? 0 ?></div>
            <div class="text-sm text-gray-600">Total Programados</div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
            <div class="text-2xl font-bold text-green-600"><?php echo $estadisticas['completados'] ?? 0 ?></div>
            <div class="text-sm text-gray-600">Completados</div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
            <div class="text-2xl font-bold text-blue-600"><?php echo $estadisticas['en_proceso'] ?? 0 ?></div>
            <div class="text-sm text-gray-600">En Proceso</div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
            <div class="text-2xl font-bold text-orange-600"><?php echo $estadisticas['alta_prioridad'] ?? 0 ?></div>
            <div class="text-sm text-gray-600">Alta Prioridad</div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Almacena el estado de la programación en el cliente
    const currentScheduleState = {};
    // Inicializar currentScheduleState con la programación existente
    <?php foreach ($programacion as $item): ?>
    const key =
        `<?= $item['technician_id'] ?>-<?= $item['equipo_id'] ?? 'null' ?>-<?= $item['scheduled_month'] ?>-<?= $item['scheduled_week'] ?>`;
    // Usar json_encode para pasar el objeto PHP a JavaScript de forma segura
    currentScheduleState[key] = <?= json_encode($item) ?>;
    // Asegurarse de que equipo_id sea null si es 'null' string
    if (currentScheduleState[key].equipo_id === 'null') {
        currentScheduleState[key].equipo_id = null;
    }
    <?php endforeach; ?>
    // Manejar cambio de club
    document.getElementById('clubSelect').addEventListener('change', function() {
        const clubId = this.value;
        const year = document.getElementById('yearSelect').value;
        window.location.href = `?year=${year}${clubId ? '&club=' + clubId : ''}`;
    });
    // Manejar cambio de año
    document.getElementById('yearSelect').addEventListener('change', function() {
        const year = this.value;
        const clubId = document.getElementById('clubSelect').value;
        window.location.href = `?year=${year}${clubId ? '&club=' + clubId : ''}`;
    });
    // Manejar clic en celdas de programación
    document.querySelectorAll('.schedule-cell').forEach(cell => {
        cell.addEventListener('click', function() {
            const techId = this.dataset.techId;
            const equipoId = this.dataset.equipoId;
            const equipmentTypeName = this.dataset.equipmentTypeName;
            const month = this.dataset.month;
            const week = this.dataset.week;
            const currentYear = this.closest('tr').dataset.year;
            const clubId = this.closest('tr').dataset.clubId;
            const key = `${techId}-${equipoId}-${month}-${week}`;
            let slotData = currentScheduleState[key];
            if (slotData) {
                // Si ya está programado, cambiar estado (o abrir modal para editar)
                if (slotData.status === 'Programado') {
                    slotData.status = 'Completado';
                    this.classList.remove('bg-orange-500');
                    this.classList.add('bg-green-500');
                    this.querySelector('.fas').className =
                        'fas fa-check-circle text-xs absolute top-0 right-0';
                } else if (slotData.status === 'Completado') {
                    slotData.status = 'En Proceso';
                    this.classList.remove('bg-green-500');
                    this.classList.add('bg-blue-500');
                    this.querySelector('.fas').className =
                        'fas fa-clock text-xs absolute top-0 right-0';
                } else if (slotData.status === 'En Proceso') {
                    // Desprogramar (eliminar del estado)
                    delete currentScheduleState[key];
                    this.classList.remove('bg-blue-500', 'text-white');
                    this.classList.add('bg-gray-100', 'hover:bg-gray-200');
                    this.innerHTML = '';
                    this.title = 'Click para programar';
                    this.dataset.status = 'Unscheduled';
                }
                // Actualizar el tooltip y el data-status
                // Usar los datos directamente del slotData, que ya están escapados por json_encode
                this.title =
                    `${slotData.equipo_nombre_programado || slotData.equipment_type} - Estado: ${slotData.status} - Prioridad: ${slotData.priority}`;
                this.dataset.status = slotData.status;
            } else {
                // Si no está programado, programarlo (marcar como 'Programado')
                this.classList.remove('bg-gray-100', 'hover:bg-gray-200');
                this.classList.add('bg-orange-500', 'text-white');
                this.innerHTML =
                    '<div class="text-center"><div class="text-xs font-bold">M</div><i class="fas fa-tools text-xs absolute top-0 right-0"></i></div>';
                this.title = `Mantenimiento - Estado: Programado - Prioridad: Media`;
                this.dataset.status = 'Programado';
                // Añadir al estado de programación
                currentScheduleState[key] = {
                    id: null,
                    technician_id: parseInt(techId),
                    equipo_id: (equipoId === 'null' ? null : parseInt(equipoId)),
                    equipment_type_id: equipmentTypeName,
                    club_id: parseInt(clubId),
                    scheduled_date: `${currentYear}-${month.padStart(2, '0')}-15`,
                    scheduled_week: parseInt(week),
                    scheduled_month: parseInt(month),
                    scheduled_year: parseInt(currentYear),
                    priority: 'Media',
                    status: 'Programado',
                    notes: `Mantenimiento programado para ${equipmentTypeName} en ${currentYear}-${month}-${week}`,
                    estimated_duration: 120,
                    frequency_id: 3
                };
            }
        });
    });
    // Manejar botón de guardar
    document.getElementById('saveBtn').addEventListener('click', function() {
        const dataToSend = Object.values(currentScheduleState);
        if (dataToSend.length === 0) {
            alert('No hay cambios para guardar.');
            return;
        }
        fetch('index.php?action=guardarProgramacion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                scheduled_items: JSON.stringify(dataToSend)
            })
        }).then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Error al guardar la programación.');
                });
            }
            return response.json();
        }).then(data => {
            if (data.success) {
                alert('Programación guardada exitosamente.');
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Ocurrió un error desconocido.'));
            }
        }).catch(error => {
            console.error('Error al guardar:', error);
            alert('Error al guardar la programación: ' + error.message);
        });
    });
});
</script> <?php require_once __DIR__ . '/../../includes/footer.php'; ?>