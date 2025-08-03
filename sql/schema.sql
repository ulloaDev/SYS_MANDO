SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0; -- Desactivar temporalmente la verificación de claves foráneas

-- ELIMINAR VISTAS PRIMERO
DROP VIEW IF EXISTS `maintenance_calendar_view`;

-- ELIMINAR TABLAS EN ORDEN INVERSO DE DEPENDENCIA
DROP TABLE IF EXISTS `maintenance_history`;
DROP TABLE IF EXISTS `maintenance_schedule`;
DROP TABLE IF EXISTS `maintenance_activities`;
DROP TABLE IF EXISTS `maintenance_frequencies`;
DROP TABLE IF EXISTS `usuarios_especialidades`; -- Nueva tabla en tu esquema
DROP TABLE IF EXISTS `especialidades_tecnicos`; -- Nueva tabla en tu esquema
DROP TABLE IF EXISTS `tipos_mantenimiento`; -- Tabla del esquema anterior, si existe
DROP TABLE IF EXISTS `technicians`; -- Tabla de tu nuevo esquema
-- NOTA: No eliminamos 'users', 'equipos', 'clubs', 'equipment_types'
-- asumiendo que ya existen y no queremos tocarlas.
-- Si estas tablas no existen, debes crearlas ANTES de ejecutar este script.
-- Aquí se incluyen definiciones básicas para que el script sea autocontenido si no las tienes.

-- =====================================================
-- TABLAS BASE (si no existen) - EJEMPLOS
-- =====================================================
-- Si ya tienes estas tablas, puedes omitir estas CREATE TABLE.
-- Asegúrate de que los IDs y nombres de columna coincidan con las FKs.

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `firstname` VARCHAR(50) NOT NULL,
  `lastname` VARCHAR(50) NOT NULL,
  `user_name` VARCHAR(50) NOT NULL UNIQUE,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `equipos` (
  `IdEq` INT NOT NULL AUTO_INCREMENT,
  `Serie` VARCHAR(50) NOT NULL,
  `NombrePC` VARCHAR(100) DEFAULT NULL,
  `Tipo` VARCHAR(50) DEFAULT NULL,
  `Usuario` VARCHAR(100) DEFAULT NULL,
  `FechaManto` DATE DEFAULT NULL,
  `Manto` CHAR(1) DEFAULT 'N',
  PRIMARY KEY (`IdEq`, `Serie`),
  UNIQUE KEY `idx_serie` (`Serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `clubs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `location` VARCHAR(255) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `equipment_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- RECREAR TABLAS Y DATOS (según tu nuevo esquema)
-- =====================================================

-- TABLA: technicians (Tu nueva tabla de técnicos)
CREATE TABLE `technicians` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `contact_info` VARCHAR(255) NULL,
  `club_id` INT NULL, -- FK a la tabla 'clubs'
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name_unique`(`name`) USING BTREE,
  INDEX `fk_technicians_club`(`club_id`) USING BTREE,
  CONSTRAINT `fk_technicians_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- TABLA: maintenance_frequencies
CREATE TABLE `maintenance_frequencies` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `interval_days` INT NULL, -- Días entre mantenimientos (ej: 7 para semanal, 30 para mensual)
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- TABLA: especialidades_tecnicos (Tu nueva tabla de especialidades)
CREATE TABLE `especialidades_tecnicos` (
  `IdEspecialidad` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Descripcion` varchar(200) DEFAULT NULL,
  `Estado` char(1) DEFAULT 'A',
  PRIMARY KEY (`IdEspecialidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Especialidades de los técnicos';

-- TABLA: usuarios_especialidades (Tu nueva tabla de relación)
CREATE TABLE `usuarios_especialidades` (
  `user_id` int(11) NOT NULL,
  `IdEspecialidad` int(11) NOT NULL,
  `FechaAsignacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `Estado` char(1) DEFAULT 'A',
  PRIMARY KEY (`user_id`, `IdEspecialidad`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`IdEspecialidad`) REFERENCES `especialidades_tecnicos` (`IdEspecialidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Especialidades asignadas a usuarios/técnicos';


-- TABLA: maintenance_activities
CREATE TABLE `maintenance_activities` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `equipment_type_id` INT NULL, -- Opcional: FK a 'equipment_types'
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name_unique_per_type` (`name`, `equipment_type_id`) USING BTREE,
  CONSTRAINT `fk_activity_equipment_type` FOREIGN KEY (`equipment_type_id`) REFERENCES `equipment_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- TABLA: maintenance_schedule
CREATE TABLE `maintenance_schedule` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `technician_id` INT NULL, -- FK a 'technicians'
  `equipo_id` INT NULL, -- FK a 'equipos.IdEq'
  `equipment_type_id` INT NOT NULL, -- FK a 'equipment_types' (Tipo de equipo para el mantenimiento)
  `club_id` INT NULL, -- FK a 'clubs' (Puede ser null si es un mantenimiento general o si el equipo no tiene club)
  `scheduled_date` DATE NOT NULL,
  `scheduled_week` INT NOT NULL,
  `scheduled_month` INT NOT NULL,
  `scheduled_year` INT NOT NULL,
  `priority` ENUM('Baja', 'Media', 'Alta', 'Crítica') NOT NULL DEFAULT 'Media',
  `status` ENUM('Programado', 'En Proceso', 'Completado', 'Reprogramado', 'Cancelado') NOT NULL DEFAULT 'Programado',
  `notes` TEXT NULL,
  `estimated_duration` INT NULL DEFAULT 120, -- Duración estimada en minutos
  `frequency_id` INT NULL, -- FK a 'maintenance_frequencies' (para mantenimientos recurrentes)
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_schedule_technician`(`technician_id`) USING BTREE,
  INDEX `fk_schedule_equipo`(`equipo_id`) USING BTREE,
  INDEX `fk_schedule_equipment_type`(`equipment_type_id`) USING BTREE,
  INDEX `fk_schedule_club`(`club_id`) USING BTREE,
  INDEX `fk_schedule_frequency`(`frequency_id`) USING BTREE,
  INDEX `idx_scheduled_date_status`(`scheduled_date`, `status`) USING BTREE,
  CONSTRAINT `fk_schedule_technician` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`IdEq`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_equipment_type` FOREIGN KEY (`equipment_type_id`) REFERENCES `equipment_types` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_frequency` FOREIGN KEY (`frequency_id`) REFERENCES `maintenance_frequencies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- TABLA: maintenance_history
CREATE TABLE `maintenance_history` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `maintenance_schedule_id` INT NOT NULL, -- FK a 'maintenance_schedule'
  `technician_id` INT NULL, -- FK a 'technicians' (el que ejecutó)
  `equipo_id` INT NULL, -- FK a 'equipos.IdEq'
  `club_id` INT NULL, -- FK a 'clubs'
  `start_time` DATETIME NULL,
  `end_time` DATETIME NULL,
  `work_performed` TEXT NULL, -- Descripción del trabajo realizado
  `cost` DECIMAL(10, 2) NULL DEFAULT 0.00, -- Costo total del mantenimiento
  `photos` JSON NULL, -- Rutas de fotos en formato JSON
  `status` ENUM('Completado', 'Fallido', 'Incompleto') NOT NULL DEFAULT 'Completado',
  `next_maintenance_date` DATE NULL, -- Sugerencia para la próxima fecha si es recurrente
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_history_schedule`(`maintenance_schedule_id`) USING BTREE,
  INDEX `fk_history_technician`(`technician_id`) USING BTREE,
  INDEX `fk_history_equipo`(`equipo_id`) USING BTREE,
  INDEX `fk_history_club`(`club_id`) USING BTREE,
  CONSTRAINT `fk_history_schedule` FOREIGN KEY (`maintenance_schedule_id`) REFERENCES `maintenance_schedule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_history_technician` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_history_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`IdEq`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_history_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- =====================================================
-- DATOS INICIALES RECOMENDADOS (ajustados a tu nuevo esquema)
-- =====================================================

-- Datos de ejemplo para clubs (si no existen)
INSERT IGNORE INTO `clubs` (`id`, `name`, `location`) VALUES
(1, 'Club 6701', 'Ubicación Club 6701'),
(2, 'Club 6702', 'Ubicación Club 6702'),
(3, 'Club 6703', 'Ubicación Club 6703');

-- Datos de ejemplo para equipment_types (si no existen)
INSERT IGNORE INTO `equipment_types` (`id`, `name`, `description`) VALUES
(1, 'Computadoras', 'Equipos de computo de escritorio y portátiles'),
(2, 'Impresoras', 'Dispositivos de impresión'),
(3, 'Servidores', 'Servidores de red y datos'),
(4, 'Aires Acondicionados', 'Sistemas de climatización'),
(5, 'Bombas', 'Bombas de agua y líquidos'),
(6, 'Compresores', 'Compresores de aire'),
(7, 'Generadores', 'Generadores eléctricos'),
(8, 'UPS', 'Sistemas de alimentación ininterrumpida');

-- Datos de ejemplo para users (si no existen)
INSERT IGNORE INTO `users` (`user_id`, `firstname`, `lastname`, `user_name`) VALUES
(1, 'Admin', 'User', 'admin'),
(2, 'Jorge', 'Contreras', 'jcont'),
(3, 'Juan', 'Luis', 'jlui'),
(4, 'José', 'Reyes', 'jreyes'),
(5, 'Francisco', 'Luna', 'fluna'),
(6, 'Efrain D', 'Hernandez', 'edhern'),
(7, 'Efrain', 'Chavez', 'echavez'),
(8, 'Jennifer E.', 'Mendoza', 'jmendo'),
(9, 'David', 'Ventulo', 'dventu'),
(10, 'Osman Jesus', 'Sanchez Henriquez', 'osanchez'),
(11, 'Mayra', 'Portillo', 'mportillo');

-- Datos de ejemplo para equipos (si no existen)
INSERT IGNORE INTO `equipos` (`IdEq`, `Serie`, `NombrePC`, `Tipo`, `Usuario`, `FechaManto`, `Manto`) VALUES
(1, 'EQP-001', 'PC Gerencia', 'Computadora', 'Gerente A', '2024-01-15', 'S'),
(2, 'EQP-002', 'Impresora RH', 'Impresora', 'RRHH', '2024-02-20', 'N'),
(3, 'EQP-003', 'Servidor Principal', 'Servidor', 'IT', '2024-03-10', 'S'),
(4, 'EQP-004', 'Aire Acondicionado Sala', 'Aires Acondicionados', 'Mantenimiento', '2024-04-05', 'N'),
(5, 'EQP-005', 'Bomba de Agua 1', 'Bombas', 'Mantenimiento', '2024-05-01', 'S'),
(6, 'EQP-006', 'UPS Principal', 'UPS', 'IT', '2024-06-10', 'N');


-- Datos de ejemplo para technicians (tu nueva tabla)
INSERT INTO `technicians` (`id`, `name`, `contact_info`, `club_id`) VALUES
(1, 'Jorge Contreras', 'jorge.c@example.com', 1),
(2, 'Juan Luis', 'juan.l@example.com', 2),
(3, 'José Reyes', 'jose.r@example.com', 1),
(4, 'Francisco Luna', 'francisco.l@example.com', 3),
(5, 'Efrain D Hernandez', 'efrain.h@example.com', 1),
(6, 'Efrain Chavez', 'efrain.c@example.com', 2),
(7, 'Jennifer E. Mendoza', 'jennifer.m@example.com', 3),
(8, 'David Ventulo', 'david.v@example.com', 1),
(9, 'Osman Jesus Sanchez Henriquez', 'osman.s@example.com', 2),
(10, 'Mayra Portillo', 'mayra.p@example.com', 3)
ON DUPLICATE KEY UPDATE name=VALUES(name); -- Para evitar errores si ya existen IDs

-- Datos de ejemplo para maintenance_frequencies
INSERT INTO `maintenance_frequencies` (`id`, `name`, `description`, `interval_days`) VALUES
(1, 'Semanal', 'Mantenimiento realizado cada semana', 7),
(2, 'Mensual', 'Mantenimiento realizado cada mes', 30),
(3, 'Trimestral', 'Mantenimiento realizado cada tres meses', 90),
(4, 'Semestral', 'Mantenimiento realizado cada seis meses', 180),
(5, 'Anual', 'Mantenimiento realizado cada año', 365),
(6, 'Única Vez', 'Mantenimiento no recurrente', NULL)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Datos de ejemplo para especialidades_tecnicos
INSERT INTO `especialidades_tecnicos` (`IdEspecialidad`, `Nombre`, `Descripcion`) VALUES
(1, 'Mecánica General', 'Mantenimiento mecánico general'),
(2, 'Electricidad', 'Sistemas eléctricos y electrónicos'),
(3, 'Electrónica', 'Equipos electrónicos especializados'),
(4, 'Aire Acondicionado', 'Sistemas HVAC'),
(5, 'Plomería', 'Sistemas hidráulicos y sanitarios'),
(6, 'Informática', 'Equipos de cómputo y redes'),
(7, 'Instrumentación', 'Instrumentos de medición y control'),
(8, 'Soldadura', 'Trabajos de soldadura especializada')
ON DUPLICATE KEY UPDATE Nombre=VALUES(Nombre);

-- Datos de ejemplo para usuarios_especialidades (asumiendo user_id 2-11 son técnicos y tienen especialidad 6 'Informática')
INSERT INTO `usuarios_especialidades` (`user_id`, `IdEspecialidad`) VALUES
(2, 6), (3, 6), (4, 6), (5, 6), (6, 6), (7, 6), (8, 6), (9, 6), (10, 6), (11, 6)
ON DUPLICATE KEY UPDATE Estado=VALUES(Estado); -- Para evitar errores si ya existen

-- Datos de ejemplo para maintenance_activities
INSERT INTO `maintenance_activities` (`id`, `name`, `description`, `equipment_type_id`) VALUES
(1, 'Limpieza de componentes internos', 'Limpiar polvo y suciedad de la tarjeta madre y ventiladores', 1),
(2, 'Verificación de conexiones', 'Asegurar que todos los cables estén correctamente conectados', 1),
(3, 'Actualización de software', 'Instalar últimas actualizaciones de sistema operativo y drivers', 1),
(4, 'Revisión de filtros de aire', 'Limpiar o reemplazar los filtros de aire del sistema de climatización', 4),
(5, 'Verificación de niveles de refrigerante', 'Chequear y rellenar niveles de refrigerante en AC', 4),
(6, 'Inspección de fugas', 'Buscar posibles fugas en el sistema de bombas', 5),
(7, 'Calibración de presión', 'Ajustar la presión de operación del compresor', 6),
(8, 'Cambio de aceite', 'Reemplazar el aceite del motor del generador', 7),
(9, 'Prueba de batería', 'Verificar el estado y la carga de las baterías del UPS', 8),
(10, 'Diagnóstico de hardware', 'Realizar un diagnóstico general de los componentes de hardware', NULL)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Datos de ejemplo para maintenance_schedule (para poblar la tabla y ver algo en el calendario)
-- Asegúrate de que los IDs de técnico, equipo y tipo de equipo existan.
INSERT INTO `maintenance_schedule`
    (`technician_id`, `equipo_id`, `equipment_type_id`, `club_id`, `scheduled_date`, `scheduled_week`, `scheduled_month`, `scheduled_year`, `priority`, `status`, `notes`, `estimated_duration`, `frequency_id`) VALUES
(1, 1, 1, 1, '2025-03-05', 1, 3, 2025, 'Alta', 'Programado', 'Mantenimiento preventivo PC Gerencia', 120, 3),
(2, 2, 2, 2, '2025-03-12', 2, 3, 2025, 'Media', 'En Proceso', 'Revisión de impresora de RRHH', 90, 3),
(3, 3, 3, 1, '2025-06-18', 3, 6, 2025, 'Baja', 'Completado', 'Mantenimiento de servidor principal', 180, 3),
(4, 4, 4, 3, '2025-09-25', 4, 9, 2025, 'Alta', 'Programado', 'Mantenimiento AC Sala', 150, 3),
(5, 5, 5, 1, '2025-12-03', 1, 12, 2025, 'Media', 'Programado', 'Inspección de bomba de agua', 60, 3),
(6, 6, 8, 2, '2025-03-28', 4, 3, 2025, 'Baja', 'Programado', 'Prueba de batería UPS', 45, 3),
(7, 1, 1, 1, '2025-06-01', 1, 6, 2025, 'Media', 'Programado', 'PC Gerencia - 2do Trimestre', 120, 3),
(8, 2, 2, 2, '2025-09-10', 2, 9, 2025, 'Baja', 'Programado', 'Impresora RRHH - 3er Trimestre', 90, 3),
(9, 3, 3, 1, '2025-12-15', 3, 12, 2025, 'Alta', 'Programado', 'Servidor Principal - 4to Trimestre', 180, 3)
ON DUPLICATE KEY UPDATE technician_id=VALUES(technician_id);


-- =====================================================
-- VISTAS ÚTILES PARA REPORTES
-- =====================================================

-- Vista para calendario de mantenimiento
-- Se ajusta para usar las nuevas tablas y columnas
CREATE OR REPLACE VIEW `maintenance_calendar_view` AS
SELECT
    ms.id AS IdPlan,
    ms.scheduled_date AS FechaProgramada,
    ms.scheduled_week,
    ms.scheduled_month,
    ms.scheduled_year,
    ms.priority AS Prioridad,
    ms.status AS Estado,
    ms.notes AS Observaciones,
    ms.estimated_duration AS EstimatedDuration,
    ms.created_at AS CreadoEn,
    ms.updated_at AS ActualizadoEn,
    e.IdEq,
    e.Serie,
    e.NombrePC AS EquipoNombre,
    e.Tipo AS TipoEquipoDelEquipo,
    et.id AS IdTipoManto, -- ID del tipo de equipo asociado al mantenimiento
    et.name AS TipoMantenimiento, -- Nombre del tipo de equipo
    t.id AS TecnicoId,
    t.name AS TecnicoAsignado,
    c.id AS ClubId,
    c.name AS Club,
    -- Simulación de HoraInicio y HoraFin (puedes añadir campos a maintenance_schedule si necesitas horas específicas)
    '09:30:00' AS HoraInicio,
    '16:30:00' AS HoraFin,
    -- Datos de la última ejecución (si existe)
    mh.start_time AS FechaEjecucion,
    TIME(mh.start_time) AS HoraEjecucion,
    mh.technician_id AS TecnicoEjecutorId,
    tec_exec.name AS TecnicoEjecutor,
    mh.work_performed AS WorkPerformed,
    mh.cost AS Cost,
    mh.next_maintenance_date AS NextMaintenanceDate,
    -- Datos de cancelación (si aplica)
    CASE WHEN ms.status = 'Cancelado' THEN ms.notes ELSE NULL END AS MotivoCancelacion,
    CASE WHEN ms.status = 'Cancelado' THEN 'Admin/Sistema' ELSE NULL END AS CanceladoPor, -- Placeholder
    CASE WHEN ms.status = 'Cancelado' THEN ms.updated_at ELSE NULL END AS FechaCancelacion
FROM
    maintenance_schedule ms
LEFT JOIN
    equipos e ON ms.equipo_id = e.IdEq
LEFT JOIN
    technicians t ON ms.technician_id = t.id
LEFT JOIN
    equipment_types et ON ms.equipment_type_id = et.id
LEFT JOIN
    clubs c ON ms.club_id = c.id
LEFT JOIN
    maintenance_history mh ON ms.id = mh.maintenance_schedule_id AND mh.status = 'Completado' -- Tomar la última ejecución completada
LEFT JOIN
    technicians tec_exec ON mh.technician_id = tec_exec.id;


-- Vista para seguimiento de mantenimientos (ajustada a tu nuevo esquema)
CREATE OR REPLACE VIEW `vista_seguimiento_mantenimiento` AS
SELECT
    ms.id AS IdPlan,
    ms.scheduled_date AS FechaProgramada,
    ms.status AS EstadoPlan,
    e.NombrePC AS NombreEquipo,
    e.Tipo AS TipoEquipo,
    et.name AS TipoMantenimiento,
    t.name AS TecnicoAsignado,
    mh.start_time AS FechaInicioEjecucion,
    mh.end_time AS FechaFinEjecucion,
    mh.status AS ResultadoGeneralEjecucion,
    TIMESTAMPDIFF(MINUTE, mh.start_time, mh.end_time) AS TiempoEjecutadoMinutos,
    mh.cost AS CostoTotalEjecucion,
    tec_exec.name AS TecnicoEjecutor,
    mh.next_maintenance_date AS ProximoMantenimientoSugerido
FROM maintenance_schedule ms
LEFT JOIN equipos e ON ms.equipo_id = e.IdEq
LEFT JOIN equipment_types et ON ms.equipment_type_id = et.id
LEFT JOIN technicians t ON ms.technician_id = t.id
LEFT JOIN maintenance_history mh ON ms.id = mh.maintenance_schedule_id
LEFT JOIN technicians tec_exec ON mh.technician_id = tec_exec.id;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS ÚTILES
-- =====================================================

DELIMITER //

-- Procedimiento para programar mantenimiento automático (ajustado a tu nuevo esquema)
CREATE PROCEDURE `ProgramarMantenimientoAutomatico`(
    IN p_technician_id INT,
    IN p_equipo_id INT,
    IN p_equipment_type_id INT,
    IN p_club_id INT,
    IN p_scheduled_date DATE,
    IN p_priority ENUM('Baja', 'Media', 'Alta', 'Crítica'),
    IN p_notes TEXT,
    IN p_estimated_duration INT,
    IN p_frequency_id INT,
    IN p_num_periods INT -- Número de veces a programar
)
BEGIN
    DECLARE v_counter INT DEFAULT 0;
    DECLARE v_current_date DATE;
    DECLARE v_interval_days INT;
    
    -- Obtener días de intervalo de la frecuencia
    SELECT interval_days INTO v_interval_days
    FROM maintenance_frequencies
    WHERE id = p_frequency_id;
    
    SET v_current_date = p_scheduled_date;
    
    WHILE v_counter < p_num_periods DO
        INSERT INTO maintenance_schedule
        (technician_id, equipo_id, equipment_type_id, club_id, scheduled_date,
         scheduled_week, scheduled_month, scheduled_year, priority, status, notes,
         estimated_duration, frequency_id)
        VALUES
        (p_technician_id, p_equipo_id, p_equipment_type_id, p_club_id, v_current_date,
         WEEK(v_current_date, 3), MONTH(v_current_date), YEAR(v_current_date), p_priority, 'Programado', p_notes,
         p_estimated_duration, p_frequency_id);
        
        -- Avanzar a la próxima fecha si hay un intervalo definido
        IF v_interval_days IS NOT NULL THEN
            SET v_current_date = DATE_ADD(v_current_date, INTERVAL v_interval_days DAY);
        ELSE
            -- Si es de 'Única Vez', salir del bucle después de la primera inserción
            SET v_counter = p_num_periods;
        END IF;
        
        SET v_counter = v_counter + 1;
    END WHILE;
END //

-- Procedimiento para obtener mantenimientos pendientes por técnico (ajustado a tu nuevo esquema)
CREATE PROCEDURE `ObtenerMantenimientosPendientesPorTecnico`(
    IN p_technician_id INT,
    IN p_date_from DATE,
    IN p_date_to DATE
)
BEGIN
    SELECT
        ms.id AS IdPlan,
        ms.scheduled_date AS FechaProgramada,
        ms.priority AS Prioridad,
        e.NombrePC AS NombreEquipo,
        et.name AS TipoMantenimiento,
        ms.notes AS Observaciones
    FROM maintenance_schedule ms
    INNER JOIN equipos e ON ms.equipo_id = e.IdEq
    INNER JOIN equipment_types et ON ms.equipment_type_id = et.id
    WHERE ms.technician_id = p_technician_id
      AND ms.status = 'Programado'
      AND ms.scheduled_date BETWEEN p_date_from AND p_date_to
    ORDER BY ms.scheduled_date;
END //

DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA RENDIMIENTO
-- =====================================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX `idx_schedule_date_status` ON `maintenance_schedule` (`scheduled_date`, `status`);
CREATE INDEX `idx_schedule_technician_date` ON `maintenance_schedule` (`technician_id`, `scheduled_date`);
CREATE INDEX `idx_history_start_status` ON `maintenance_history` (`start_time`, `status`);
-- Este índice de `equipos` ya debería existir o estar en la tabla `equipos` si la tienes
-- CREATE INDEX `idx_equipos_manto_fecha` ON `equipos` (`FechaManto`, `Manto`);

SET FOREIGN_KEY_CHECKS = 1; -- Reactivar la verificación de claves foráneas
