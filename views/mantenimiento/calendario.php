<?php
require_once __DIR__ . '/../../includes/header.php';
/**
 * Vista del Calendario de Mantenimientos - Versión Final
 * Muestra una interfaz completa con calendario interactivo, filtros y gestión de eventos
 */

// Verificar si hay errores globales
if (!empty($error_global)) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($error_global) . '</div>';
}
?> <style>
.calendar-day {
 height: 120px;
 vertical-align: top;
 position: relative;
 cursor: pointer;
}

.calendar-day:hover {
 background-color: #f8f9fa;
}

.calendar-today {
 background-color: #e9f7fe;
}

.calendar-day-header {
 text-align: right;
 padding: 2px 5px;
 font-weight: bold;
}

.calendar-events {
 max-height: 90px;
 overflow-y: auto;
 font-size: 0.85rem;
}

.calendar-event {
 margin-bottom: 2px;
 padding: 2px;
 border-radius: 3px;
 background-color: #f8f9fa;
 white-space: nowrap;
 overflow: hidden;
 text-overflow: ellipsis;
}

.calendar-event:hover {
 background-color: #e2e6ea;
}

.event-details {
 font-size: 0.8rem;
 margin-top: 5px;
 padding: 5px;
 background-color: white;
 border-radius: 3px;
 border: 1px solid #dee2e6;
}
</style>
<div class="container-fluid mt-4">
 <div class="card">
  <div class="card-header bg-primary text-white">
   <div class="d-flex justify-content-between align-items-center">
    <h3 class="mb-0">
     <i class="fas fa-calendar-alt mr-2"></i>Calendario de Mantenimientos
    </h3>
    <div>
     <a href="?action=mantenimiento/formulario" class="btn btn-success btn-sm">
      <i class="fas fa-plus"></i> Nuevo Mantenimiento </a>
    </div>
   </div>
  </div>
  <div class="card-body">
   <!-- Filtros -->
   <div class="row mb-4">
    <div class="col-md-3">
     <label for="filtro-tecnico">Técnico:</label>
     <select id="filtro-tecnico" class="form-control form-control-sm">
      <option value="">Todos los técnicos</option> <?php foreach ($tecnicos as $tecnico): ?> <option
       value="<?= $tecnico['IdTecnico'] ?>" <?= ($tecnicoId == $tecnico['IdTecnico']) ? 'selected' : '' ?>>
       <?= htmlspecialchars($tecnico['Nombre']) ?> </option> <?php endforeach; ?>
     </select>
    </div>
    <div class="col-md-3">
     <label for="filtro-estado">Estado:</label>
     <select id="filtro-estado" class="form-control form-control-sm">
      <option value="">Todos los estados</option>
      <option value="Programado">Programado</option>
      <option value="En Proceso">En Proceso</option>
      <option value="Completado">Completado</option>
      <option value="Cancelado">Cancelado</option>
     </select>
    </div>
    <div class="col-md-3">
     <label for="filtro-mes">Mes:</label>
     <select id="filtro-mes" class="form-control form-control-sm"> <?php for ($i = 1; $i <= 12; $i++): ?> <option
       value="<?= $i ?>" <?= ($currentMonth == $i) ? 'selected' : '' ?>> <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
      </option> <?php endfor; ?> </select>
    </div>
    <div class="col-md-3">
     <label for="filtro-anio">Año:</label>
     <select id="filtro-anio" class="form-control form-control-sm">
      <?php for ($i = date('Y') - 2; $i <= date('Y') + 2; $i++): ?> <option value="<?= $i ?>"
       <?= ($currentYear == $i) ? 'selected' : '' ?>> <?= $i ?> </option> <?php endfor; ?> </select>
    </div>
   </div>
   <!-- Navegación del calendario -->
   <div class="row mb-3">
    <div class="col-md-6">
     <div class="btn-group">
      <button id="btn-mes-anterior" class="btn btn-outline-primary">
       <i class="fas fa-chevron-left"></i> Mes Anterior </button>
      <button id="btn-hoy" class="btn btn-outline-secondary">Hoy</button>
      <button id="btn-mes-siguiente" class="btn btn-outline-primary"> Mes Siguiente <i class="fas fa-chevron-right"></i>
      </button>
     </div>
    </div>
    <div class="col-md-6 text-right">
     <h4 class="text-primary font-weight-bold"> <?= date('F Y', strtotime("$currentYear-$currentMonth-01")) ?> </h4>
    </div>
   </div>
   <!-- Calendario -->
   <div id="calendario-mantenimientos">
    <div class="table-responsive">
     <table class="table table-bordered">
      <thead class="thead-light">
       <tr>
        <th class="text-center">Lun</th>
        <th class="text-center">Mar</th>
        <th class="text-center">Mié</th>
        <th class="text-center">Jue</th>
        <th class="text-center">Vie</th>
        <th class="text-center">Sáb</th>
        <th class="text-center">Dom</th>
       </tr>
      </thead>
      <tbody> <?php
                            $firstDay = new DateTime("$currentYear-$currentMonth-01");
                            $lastDay = new DateTime("$currentYear-$currentMonth-" . $firstDay->format('t'));
                            
                            $startDay = (int)$firstDay->format('N') - 1; // Día de la semana (0=Lun, 6=Dom)
                            $totalDays = (int)$lastDay->format('d');
                            $totalCells = ceil(($totalDays + $startDay) / 7) * 7;
                            
                            $currentDay = 1;
                            $currentCell = 0;
                            
                            while ($currentCell < $totalCells):
                                if ($currentCell % 7 == 0) echo '<tr>';
                                
                                if ($currentCell < $startDay || $currentDay > $totalDays):
                                    echo '<td class="calendar-empty-day" style="height: 120px;"></td>';
                                else:
                                    $currentDate = "$currentYear-$currentMonth-" . str_pad($currentDay, 2, '0', STR_PAD_LEFT);
                                    $hasEvents = isset($mantenimientosEnMes[$currentDate]);
                                    $isToday = ($currentDate == date('Y-m-d')) ? 'calendar-today' : '';
                                    
                                    echo '<td class="calendar-day ' . $isToday . '" data-date="' . $currentDate . '">';
                                    echo '<div class="calendar-day-header">' . $currentDay . '</div>';
                                    if ($hasEvents):
                                        echo '<div class="calendar-events">';
                                        foreach ($mantenimientosEnMes[$currentDate] as $evento):
                                            $badgeClass = [
                                                'Programado' => 'bg-primary',
                                                'En Proceso' => 'bg-warning text-dark',
                                                'Completado' => 'bg-success',
                                                'Cancelado' => 'bg-danger'
                                            ][$evento['Estado']] ?? 'bg-secondary';
                                            
                                            echo '<div class="calendar-event" data-id="' . $evento['IdPlan'] . '">';
                                            echo '<span class="badge ' . $badgeClass . '">' . substr($evento['Estado'], 0, 1) . '</span> ';
                                            echo '<span class="event-title">' . htmlspecialchars($evento['equipo_nombre']) . '</span>';
                                            echo '<div class="event-details d-none">';
                                            echo '<p><strong>Hora:</strong> ' . $evento['HoraInicio'] . ' - ' . $evento['HoraFin'] . '</p>';
                                            echo '<p><strong>Técnico:</strong> ' . htmlspecialchars($evento['tecnico_nombre']) . '</p>';
                                            echo '<p><strong>Prioridad:</strong> ' . $evento['Prioridad'] . '</p>';
                                            echo '<div class="btn-group btn-group-sm">';
                                            echo '<a href="?action=mantenimiento/formulario&id=' . $evento['IdPlan'] . '" class="btn btn-sm btn-outline-primary">Editar</a>';
                                            
                                            if ($evento['Estado'] == 'Programado'):
                                                echo '<button class="btn btn-sm btn-outline-success btn-cambiar-estado" data-estado="En Proceso">Iniciar</button>';
                                            elseif ($evento['Estado'] == 'En Proceso'):
                                                echo '<button class="btn btn-sm btn-outline-success btn-cambiar-estado" data-estado="Completado">Completar</button>';
                                            endif;
                                            
                                            echo '</div>';
                                            echo '</div>';
                                            echo '</div>';
                                        endforeach;
                                        echo '</div>';
                                    endif;
                                    
                                    echo '</td>';
                                    $currentDay++;
                                endif;
                                
                                $currentCell++;
                                if ($currentCell % 7 == 0) echo '</tr>';
                            endwhile;
                            ?> </tbody>
     </table>
    </div>
   </div>
  </div>
 </div>
</div>
<div class="modal fade" id="eventoModal" tabindex="-1" role="dialog" aria-labelledby="eventoModalLabel"
 aria-hidden="true">
 <div class="modal-dialog" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title" id="eventoModalLabel">Detalles del Mantenimiento</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body" id="modal-body-content">
    <!-- Contenido dinámico -->
   </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
    <a href="#" class="btn btn-primary" id="btn-editar-evento">Editar</a>
   </div>
  </div>
 </div>
</div>
<!-- Modal para cambiar estado -->
<div class="modal fade" id="estadoModal" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title">Cambiar Estado</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body">
    <form id="form-cambiar-estado">
     <input type="hidden" id="idPlanEstado" name="IdPlan">
     <input type="hidden" id="nuevoEstado" name="Estado">
     <div id="div-motivo-cancelacion" class="form-group d-none">
      <label for="motivoCancelacion">Motivo de cancelación:</label>
      <textarea class="form-control" id="motivoCancelacion" name="Motivo" rows="3" required></textarea>
     </div>
     <div id="div-observaciones-completado" class="form-group d-none">
      <label for="observacionesCompletado">Observaciones:</label>
      <textarea class="form-control" id="observacionesCompletado" name="Observaciones" rows="3"></textarea>
     </div>
    </form>
   </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
    <button type="button" class="btn btn-primary" id="btn-confirmar-estado">Confirmar</button>
   </div>
  </div>
 </div>
</div>
<sc> $(document).ready(function() { // Manejar clic en evento $('.calendar-event').click(function(e) {
 e.stopPropagation(); $(this).find('.event-details').toggleClass('d-none'); }); // Manejar clic en día
 $('.calendar-day').click(function() { const date = $(this).data('date'); window.location.href =
 `?action=mantenimiento/lista&fecha_desde=${date}&fecha_hasta=${date}`; }); // Filtros $('#filtro-tecnico,
 #filtro-estado, #filtro-mes, #filtro-anio').change(function() { const tecnico = $('#filtro-tecnico').val(); const
 estado = $('#filtro-estado').val(); const mes = $('#filtro-mes').val(); const anio = $('#filtro-anio').val(); let url =
 `?action=mantenimiento/calendario&month=${mes}&year=${anio}`; if (tecnico) url += `&tecnico=${tecnico}`; if (estado)
 url += `&estado=${estado}`; window.location.href = url; }); // Navegación del calendario
 $('#btn-mes-anterior').click(function() { const mes = parseInt($('#filtro-mes').val()); const anio =
 parseInt($('#filtro-anio').val()); let nuevoMes = mes - 1; let nuevoAnio = anio; if (nuevoMes < 1) { nuevoMes=12;
  nuevoAnio--; } window.location.href=`?action=mantenimiento/calendario&month=${nuevoMes}&year=${nuevoAnio}`; });
  $('#btn-mes-siguiente').click(function() { const mes=parseInt($('#filtro-mes').val()); const
  anio=parseInt($('#filtro-anio').val()); let nuevoMes=mes + 1; let nuevoAnio=anio; if (nuevoMes> 12) { nuevoMes = 1;
  nuevoAnio++; } window.location.href = `?action=mantenimiento/calendario&month=${nuevoMes}&year=${nuevoAnio}`; });
  $('#btn-hoy').click(function() { const hoy = new Date(); window.location.href =
  `?action=mantenimiento/calendario&month=${hoy.getMonth() + 1}&year=${hoy.getFullYear()}`; }); // Cambiar estado de
  mantenimiento $('.btn-cambiar-estado').click(function(e) { e.stopPropagation(); const idPlan =
  $(this).closest('.calendar-event').data('id'); const nuevoEstado = $(this).data('estado');
  $('#idPlanEstado').val(idPlan); $('#nuevoEstado').val(nuevoEstado); // Configurar modal según el estado
  $('#div-motivo-cancelacion, #div-observaciones-completado').addClass('d-none'); if (nuevoEstado === 'Cancelado') {
  $('#div-motivo-cancelacion').removeClass('d-none'); } else if (nuevoEstado === 'Completado') {
  $('#div-observaciones-completado').removeClass('d-none'); } $('#estadoModal').modal('show'); }); // Confirmar cambio
  de estado $('#btn-confirmar-estado').click(function() { const formData = $('#form-cambiar-estado').serialize();
  $.post('?action=mantenimiento/cambiarEstado', formData, function(response) { if (response.success) {
  location.reload(); } else { alert('Error: ' + response.message); } }, 'json').fail(function() { alert('Error al
  procesar la solicitud'); }); }); // Mostrar modal para ver detalles completos
  $('.calendar-event').dblclick(function(e) { e.stopPropagation(); const idPlan = $(this).data('id');
  $.get(`?action=mantenimiento/obtenerMantenimiento&id=${idPlan}`, function(data) { if (data.success) { const m =
  data.data; let html = ` <h6>${m.equipo_nombre}</h6>
  <p><strong>Fecha:</strong> ${m.FechaProgramada} ${m.HoraInicio} - ${m.HoraFin}</p>
  <p><strong>Técnico:</strong> ${m.tecnico_nombre}</p>
  <p><strong>Estado:</strong> <span class="badge ${getBadgeClass(m.Estado)}">${m.Estado}</span></p>
  <p><strong>Prioridad:</strong> ${m.Prioridad}</p>
  <p><strong>Observaciones:</strong> ${m.Observaciones || 'Ninguna'}</p> `; if (m.Estado === 'Completado' &&
  m.ejecucion) { html += `
  <hr>
  <h6>Detalles de Ejecución</h6>
  <p><strong>Resultado:</strong> ${m.ejecucion.ResultadoGeneral}</p>
  <p><strong>Observaciones:</strong> ${m.ejecucion.ObservacionesEjecucion || 'Ninguna'}</p> `; }
  $('#modal-body-content').html(html); $('#btn-editar-evento').attr('href',
  `?action=mantenimiento/formulario&id=${idPlan}`); $('#eventoModal').modal('show'); } else { alert('Error al cargar los
  datos'); } }, 'json'); }); function getBadgeClass(estado) { const classes = { 'Programado': 'bg-primary', 'En
  Proceso': 'bg-warning text-dark', 'Completado': 'bg-success', 'Cancelado': 'bg-danger' }; return classes[estado] ||
  'bg-secondary'; } }); </scrip> <?php require_once __DIR__ . '/../../includes/footer.php'; ?>