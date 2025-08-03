/*
 Navicat Premium Data Transfer

 Source Server         : cnAPPlocal
 Source Server Type    : MySQL
 Source Server Version : 100432
 Source Host           : localhost:3306
 Source Schema         : sysmanto

 Target Server Type    : MySQL
 Target Server Version : 100432
 File Encoding         : 65001

 Date: 02/08/2025 17:42:34
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for accesorios
-- ----------------------------
DROP TABLE IF EXISTS `accesorios`;
CREATE TABLE `accesorios`  (
  `IdAcc` int NOT NULL AUTO_INCREMENT,
  `NombreAcc` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`IdAcc`) USING BTREE,
  INDEX `idx_nombreacc`(`NombreAcc`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for celulares
-- ----------------------------
DROP TABLE IF EXISTS `celulares`;
CREATE TABLE `celulares`  (
  `IdCel` int NOT NULL AUTO_INCREMENT,
  `NumCel` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Extension` int NULL DEFAULT NULL,
  `NombreDisp` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Club` int NULL DEFAULT NULL,
  `NombreUsuario` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `IdDepto` int NULL DEFAULT NULL,
  `Modelo` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `IdMarca` int NULL DEFAULT NULL,
  `Imei` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Condicion` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `SaldoAsig` float(8, 2) NULL DEFAULT NULL,
  `CostoDatos` float(8, 2) NULL DEFAULT NULL,
  `Capacidad` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Cobertura` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `CostoLinea` float(8, 2) NULL DEFAULT NULL,
  `Seguro` float(8, 2) NULL DEFAULT NULL,
  `ManosLibres` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Cargador` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Manuales` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Protector` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Otros` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Comentario` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Estado` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `FechaContrato` date NULL DEFAULT NULL,
  `MesesContrato` int NOT NULL,
  `FechaReg` date NULL DEFAULT NULL,
  `UsuarioReg` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `FechaAct` date NULL DEFAULT NULL,
  `UsuarioAct` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`IdCel`, `Imei`, `NombreDisp`) USING BTREE,
  INDEX `FK_Eq_Marca`(`IdMarca`) USING BTREE,
  INDEX `FK_Eq_Depto`(`IdDepto`) USING BTREE,
  INDEX `IdEq`(`IdCel`) USING BTREE,
  INDEX `idx_numcel`(`NumCel`) USING BTREE,
  INDEX `idx_extension`(`Extension`) USING BTREE,
  INDEX `idx_nombreusuario`(`NombreUsuario`) USING BTREE,
  INDEX `idx_modelo`(`Modelo`) USING BTREE,
  INDEX `idx_condicion`(`Condicion`) USING BTREE,
  INDEX `idx_capacidad`(`Capacidad`) USING BTREE,
  INDEX `idx_cobertura`(`Cobertura`) USING BTREE,
  INDEX `idx_estado`(`Estado`) USING BTREE,
  CONSTRAINT `celulares_ibfk_1` FOREIGN KEY (`IdMarca`) REFERENCES `marcas` (`IdMarca`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `celulares_ibfk_2` FOREIGN KEY (`IdDepto`) REFERENCES `departamentos` (`IdDepto`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 195 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for clubs
-- ----------------------------
DROP TABLE IF EXISTS `clubs`;
CREATE TABLE `clubs`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6705 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for departamentos
-- ----------------------------
DROP TABLE IF EXISTS `departamentos`;
CREATE TABLE `departamentos`  (
  `IdDepto` int NOT NULL AUTO_INCREMENT,
  `NombreDepto` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`IdDepto`) USING BTREE,
  INDEX `idx_nombredepto`(`NombreDepto`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for detalleacc
-- ----------------------------
DROP TABLE IF EXISTS `detalleacc`;
CREATE TABLE `detalleacc`  (
  `IdDetAcc` int NOT NULL AUTO_INCREMENT,
  `IdEq` int NOT NULL,
  `IdAcc` int NOT NULL,
  `Marca` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Serie` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `FechaAcc` date NULL DEFAULT NULL,
  `FechaRegistro` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdDetAcc`) USING BTREE,
  INDEX `FK_DetAcc_Eq`(`IdEq`) USING BTREE,
  INDEX `FK_DetAcc_Acc`(`IdAcc`) USING BTREE,
  INDEX `idx_marca`(`Marca`) USING BTREE,
  INDEX `idx_serie`(`Serie`) USING BTREE,
  INDEX `idx_fechaacc`(`FechaAcc`) USING BTREE,
  CONSTRAINT `FK_DetAcc_Acc` FOREIGN KEY (`IdAcc`) REFERENCES `accesorios` (`IdAcc`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_DetAcc_Eq` FOREIGN KEY (`IdEq`) REFERENCES `equipos` (`IdEq`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 555 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for detallelic
-- ----------------------------
DROP TABLE IF EXISTS `detallelic`;
CREATE TABLE `detallelic`  (
  `IdDetLic` int NOT NULL AUTO_INCREMENT,
  `IdEq` int NOT NULL,
  `IdLic` int NOT NULL,
  `TipoLic` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `FechaLic` date NULL DEFAULT NULL,
  `Vigencia` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Serial` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Usuario` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Clave` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `FechaReg` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdDetLic`) USING BTREE,
  INDEX `FK_DetLic_Eq`(`IdEq`) USING BTREE,
  INDEX `FK_DetLic_Lic`(`IdLic`) USING BTREE,
  INDEX `idx_tipolic`(`TipoLic`) USING BTREE,
  INDEX `idx_vigencia`(`Vigencia`) USING BTREE,
  INDEX `idx_serial`(`Serial`) USING BTREE,
  INDEX `idx_usuario`(`Usuario`) USING BTREE,
  INDEX `idx_clave`(`Clave`) USING BTREE,
  CONSTRAINT `FK_DetLic_Eq` FOREIGN KEY (`IdEq`) REFERENCES `equipos` (`IdEq`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_DetLic_Lic` FOREIGN KEY (`IdLic`) REFERENCES `licencias` (`IdLic`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 158280 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for equipment_types
-- ----------------------------
DROP TABLE IF EXISTS `equipment_types`;
CREATE TABLE `equipment_types`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id`, `name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for equipos
-- ----------------------------
DROP TABLE IF EXISTS `equipos`;
CREATE TABLE `equipos`  (
  `IdEq` int NOT NULL AUTO_INCREMENT,
  `Activo` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Serie` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `NombrePC` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `UsuarioAD` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `IdMarca` int NULL DEFAULT NULL,
  `Modelo` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Precio` float(8, 2) NULL DEFAULT NULL,
  `Club` int NULL DEFAULT NULL,
  `IdDepto` int NULL DEFAULT NULL,
  `Tipo` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `IdProv` int NULL DEFAULT NULL,
  `Usuario` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `TipoG` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `DuracionG` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `FechaCompra` date NULL DEFAULT NULL,
  `Estado` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Descripcion` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Comentario` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Asset` int NULL DEFAULT NULL,
  `UsuarioRegistra` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `UsuarioActualiza` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `FechaRegistro` date NULL DEFAULT NULL,
  `FechaActualiza` date NULL DEFAULT NULL,
  `FormEntrega` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Disposal` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Manto` int NULL DEFAULT 1,
  `FechaManto` date NULL DEFAULT NULL,
  `UsuarioManto` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`IdEq`, `Serie`) USING BTREE,
  INDEX `FK_Eq_Marca`(`IdMarca`) USING BTREE,
  INDEX `FK_Eq_Prov`(`IdProv`) USING BTREE,
  INDEX `FK_Eq_Depto`(`IdDepto`) USING BTREE,
  INDEX `IdEq`(`IdEq`) USING BTREE,
  INDEX `idx_activo`(`Activo`) USING BTREE,
  INDEX `idx_nombrepc`(`NombrePC`) USING BTREE,
  INDEX `idx_usuarioad`(`UsuarioAD`) USING BTREE,
  INDEX `idx_modelo_eq`(`Modelo`) USING BTREE,
  INDEX `idx_tipo`(`Tipo`) USING BTREE,
  INDEX `idx_tipog`(`TipoG`) USING BTREE,
  INDEX `idx_duraciong`(`DuracionG`) USING BTREE,
  INDEX `idx_estado_eq`(`Estado`) USING BTREE,
  INDEX `fk_equipos_club_id`(`Club`) USING BTREE,
  CONSTRAINT `FK_Eq_Depto` FOREIGN KEY (`IdDepto`) REFERENCES `departamentos` (`IdDepto`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_Eq_Marca` FOREIGN KEY (`IdMarca`) REFERENCES `marcas` (`IdMarca`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_Eq_Prov` FOREIGN KEY (`IdProv`) REFERENCES `proveedores` (`IdProv`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_equipos_club_id` FOREIGN KEY (`Club`) REFERENCES `clubs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 565 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for especialidades_tecnicos
-- ----------------------------
DROP TABLE IF EXISTS `especialidades_tecnicos`;
CREATE TABLE `especialidades_tecnicos`  (
  `IdEspecialidad` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Descripcion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'A',
  PRIMARY KEY (`IdEspecialidad`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'Especialidades de los técnicos' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for licencias
-- ----------------------------
DROP TABLE IF EXISTS `licencias`;
CREATE TABLE `licencias`  (
  `IdLic` int NOT NULL AUTO_INCREMENT,
  `NombreLic` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`IdLic`) USING BTREE,
  INDEX `idx_nombrelic`(`NombreLic`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for maintenance_activities
-- ----------------------------
DROP TABLE IF EXISTS `maintenance_activities`;
CREATE TABLE `maintenance_activities`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `equipment_type_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name_unique_per_type`(`name`, `equipment_type_id`) USING BTREE,
  INDEX `fk_activity_equipment_type`(`equipment_type_id`) USING BTREE,
  CONSTRAINT `fk_activities_tipoeq` FOREIGN KEY (`equipment_type_id`) REFERENCES `tipoeq` (`NombreEq`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for maintenance_frequencies
-- ----------------------------
DROP TABLE IF EXISTS `maintenance_frequencies`;
CREATE TABLE `maintenance_frequencies`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `interval_days` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for maintenance_history
-- ----------------------------
DROP TABLE IF EXISTS `maintenance_history`;
CREATE TABLE `maintenance_history`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `maintenance_schedule_id` int NOT NULL,
  `technician_id` int NULL DEFAULT NULL,
  `equipo_id` int NULL DEFAULT NULL,
  `club_id` int NULL DEFAULT NULL,
  `start_time` datetime NULL DEFAULT NULL,
  `end_time` datetime NULL DEFAULT NULL,
  `work_performed` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `cost` decimal(10, 2) NULL DEFAULT 0.00,
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `status` enum('Completado','Fallido','Incompleto') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Completado',
  `next_maintenance_date` date NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_history_schedule`(`maintenance_schedule_id`) USING BTREE,
  INDEX `fk_history_technician`(`technician_id`) USING BTREE,
  INDEX `fk_history_equipo`(`equipo_id`) USING BTREE,
  INDEX `fk_history_club`(`club_id`) USING BTREE,
  INDEX `idx_history_start_status`(`start_time`, `status`) USING BTREE,
  CONSTRAINT `fk_history_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_history_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`IdEq`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_history_schedule` FOREIGN KEY (`maintenance_schedule_id`) REFERENCES `maintenance_schedule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_history_technician` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for maintenance_schedule
-- ----------------------------
DROP TABLE IF EXISTS `maintenance_schedule`;
CREATE TABLE `maintenance_schedule`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `technician_id` int NULL DEFAULT NULL,
  `equipo_id` int NULL DEFAULT NULL,
  `equipment_type_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `club_id` int NULL DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_week` int NOT NULL,
  `scheduled_month` int NOT NULL,
  `scheduled_year` int NOT NULL,
  `priority` enum('Baja','Media','Alta','Crítica') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Media',
  `status` enum('Programado','En Proceso','Completado','Reprogramado','Cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Programado',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `estimated_duration` int NULL DEFAULT 120,
  `frequency_id` int NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_schedule_technician`(`technician_id`) USING BTREE,
  INDEX `fk_schedule_equipo`(`equipo_id`) USING BTREE,
  INDEX `fk_schedule_frequency`(`frequency_id`) USING BTREE,
  INDEX `idx_schedule_date_status_priority`(`scheduled_date`, `status`, `priority`) USING BTREE,
  INDEX `fk_schedule_equipo_tipo`(`equipment_type_id`) USING BTREE,
  INDEX `idx_schedule_date_status`(`scheduled_date`, `status`) USING BTREE,
  INDEX `idx_schedule_technician_date`(`technician_id`, `scheduled_date`) USING BTREE,
  CONSTRAINT `fk_schedule_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`IdEq`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_frequency` FOREIGN KEY (`frequency_id`) REFERENCES `maintenance_frequencies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_technician` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_tipoeq` FOREIGN KEY (`equipment_type_id`) REFERENCES `tipoeq` (`NombreEq`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 27848 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for marcas
-- ----------------------------
DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas`  (
  `IdMarca` int NOT NULL AUTO_INCREMENT,
  `NombreMarca` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Device` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`IdMarca`) USING BTREE,
  INDEX `idx_nombremarca`(`NombreMarca`) USING BTREE,
  INDEX `idx_device`(`Device`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for network
-- ----------------------------
DROP TABLE IF EXISTS `network`;
CREATE TABLE `network`  (
  `IdEq` int NOT NULL AUTO_INCREMENT,
  `Activo` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Serie` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `NombreEq` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Mac` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `IdMarca` int NULL DEFAULT NULL,
  `Modelo` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Ubicacion` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Tipo` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Precio` float(8, 2) NULL DEFAULT NULL,
  `IdProv` int NULL DEFAULT NULL,
  `Club` int NULL DEFAULT NULL,
  `FechaCompra` date NULL DEFAULT NULL,
  `FechaInstal` date NULL DEFAULT NULL,
  `Estado` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Descripcion` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Comentarios` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Asset` int NULL DEFAULT NULL,
  `UsuarioRegistra` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `UsuarioActualiza` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `FechaRegistro` date NULL DEFAULT NULL,
  `FechaActualiza` date NULL DEFAULT NULL,
  PRIMARY KEY (`IdEq`, `Serie`) USING BTREE,
  INDEX `FK_Eq_Marca`(`IdMarca`) USING BTREE,
  INDEX `FK_Eq_Prov`(`IdProv`) USING BTREE,
  INDEX `IdEq`(`IdEq`) USING BTREE,
  INDEX `idx_activo_net`(`Activo`) USING BTREE,
  INDEX `idx_nombreeq`(`NombreEq`) USING BTREE,
  INDEX `idx_ip`(`Ip`) USING BTREE,
  INDEX `idx_mac`(`Mac`) USING BTREE,
  INDEX `idx_modelo_net`(`Modelo`) USING BTREE,
  INDEX `idx_ubicacion`(`Ubicacion`) USING BTREE,
  INDEX `idx_tipo_net`(`Tipo`) USING BTREE,
  INDEX `idx_estado_net`(`Estado`) USING BTREE,
  CONSTRAINT `network_ibfk_2` FOREIGN KEY (`IdMarca`) REFERENCES `marcas` (`IdMarca`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `network_ibfk_3` FOREIGN KEY (`IdProv`) REFERENCES `proveedores` (`IdProv`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 104 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for port
-- ----------------------------
DROP TABLE IF EXISTS `port`;
CREATE TABLE `port`  (
  `PortId` int NOT NULL AUTO_INCREMENT,
  `NumberPort` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PortId`) USING BTREE,
  INDEX `idx_numberport`(`NumberPort`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for portconfig
-- ----------------------------
DROP TABLE IF EXISTS `portconfig`;
CREATE TABLE `portconfig`  (
  `IdPort` int NOT NULL AUTO_INCREMENT,
  `IdEq` int NOT NULL,
  `PortNumber` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Vlan` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ServiceProv` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `FechaRegistro` date NULL DEFAULT NULL,
  PRIMARY KEY (`IdPort`) USING BTREE,
  INDEX `idx_portnumber`(`PortNumber`) USING BTREE,
  INDEX `idx_vlan`(`Vlan`) USING BTREE,
  INDEX `idx_serviceprov`(`ServiceProv`) USING BTREE,
  INDEX `portconfig_ibfk_1`(`IdEq`) USING BTREE,
  CONSTRAINT `portconfig_ibfk_1` FOREIGN KEY (`IdEq`) REFERENCES `network` (`IdEq`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 714 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for printers
-- ----------------------------
DROP TABLE IF EXISTS `printers`;
CREATE TABLE `printers`  (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Tag` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Serie` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Marca` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Modelo` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Tipo` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Conexion` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `IP` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `NombreAS` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Toner` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Ubicacion` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Club` int NULL DEFAULT NULL,
  `Propietario` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Descripcion` varchar(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Comentario` varchar(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Asset` int NULL DEFAULT NULL,
  `Precio` float(8, 2) NULL DEFAULT NULL,
  `UsuarioRegistra` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `UsuarioActualiza` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `FechaRegistro` date NULL DEFAULT NULL,
  `FechaActualiza` date NULL DEFAULT NULL,
  `FormEntrega` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `Disposal` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `FechaCompra` date NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE,
  INDEX `idx_tag`(`Tag`) USING BTREE,
  INDEX `idx_serie_pr`(`Serie`) USING BTREE,
  INDEX `idx_marca_pr`(`Marca`) USING BTREE,
  INDEX `idx_modelo_pr`(`Modelo`) USING BTREE,
  INDEX `idx_tipo_pr`(`Tipo`) USING BTREE,
  INDEX `idx_conexion`(`Conexion`) USING BTREE,
  INDEX `idx_ip_pr`(`IP`) USING BTREE,
  INDEX `idx_nombreas`(`NombreAS`) USING BTREE,
  INDEX `idx_toner`(`Toner`) USING BTREE,
  INDEX `idx_ubicacion_pr`(`Ubicacion`) USING BTREE,
  INDEX `idx_propietario`(`Propietario`) USING BTREE,
  INDEX `idx_estado_pr`(`Estado`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 106 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for proveedores
-- ----------------------------
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores`  (
  `IdProv` int NOT NULL AUTO_INCREMENT,
  `NombreProv` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`IdProv`) USING BTREE,
  INDEX `idx_nombreprov`(`NombreProv`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for technicians
-- ----------------------------
DROP TABLE IF EXISTS `technicians`;
CREATE TABLE `technicians`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contact_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `club_id` int NULL DEFAULT NULL,
  `user_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `fk_technicians_club_id`(`club_id`) USING BTREE,
  CONSTRAINT `fk_technician_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_technicians_club_id` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_technicians_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 49 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for tipoeq
-- ----------------------------
DROP TABLE IF EXISTS `tipoeq`;
CREATE TABLE `tipoeq`  (
  `IdTpEq` int NOT NULL AUTO_INCREMENT,
  `NombreEq` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`IdTpEq`) USING BTREE,
  INDEX `idx_nombreeq`(`NombreEq`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `user_id` int NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `firstname` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'user\'s name, unique',
  `user_password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'user\'s password in salted and hashed format',
  `user_email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'user\'s email, unique',
  `date_added` datetime NOT NULL,
  `level` int NOT NULL,
  `club_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`) USING BTREE,
  UNIQUE INDEX `user_name`(`user_name`) USING BTREE,
  UNIQUE INDEX `user_email`(`user_email`) USING BTREE,
  INDEX `idx_firstname`(`firstname`) USING BTREE,
  INDEX `idx_lastname`(`lastname`) USING BTREE,
  INDEX `idx_date_added`(`date_added`) USING BTREE,
  INDEX `idx_level`(`level`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'user data' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for usuarios_especialidades
-- ----------------------------
DROP TABLE IF EXISTS `usuarios_especialidades`;
CREATE TABLE `usuarios_especialidades`  (
  `user_id` int NOT NULL,
  `IdEspecialidad` int NOT NULL,
  `FechaAsignacion` datetime NULL DEFAULT current_timestamp,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'A',
  PRIMARY KEY (`user_id`, `IdEspecialidad`) USING BTREE,
  INDEX `usuarios_especialidades_ibfk_2`(`IdEspecialidad`) USING BTREE,
  CONSTRAINT `usuarios_especialidades_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `usuarios_especialidades_ibfk_2` FOREIGN KEY (`IdEspecialidad`) REFERENCES `especialidades_tecnicos` (`IdEspecialidad`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'Especialidades asignadas a usuarios/técnicos' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- View structure for maintenance_calendar_view
-- ----------------------------
DROP VIEW IF EXISTS `maintenance_calendar_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `maintenance_calendar_view` AS SELECT
    ms.id AS maintenance_id,
    ms.scheduled_date AS event_date,
    CONCAT('Mantenimiento: ', te.NombreEq, ' - ', e.NombrePC) AS event_title,
    t.name AS technician_name,
    ms.status AS event_status
FROM maintenance_schedule ms
JOIN equipos e ON ms.equipo_id = e.IdEq
JOIN tipoeq te ON ms.equipment_type_id = te.NombreEq
LEFT JOIN technicians t ON ms.technician_id = t.id
WHERE ms.status = 'Programado' ;

-- ----------------------------
-- View structure for vista_seguimiento_mantenimiento
-- ----------------------------
DROP VIEW IF EXISTS `vista_seguimiento_mantenimiento`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `vista_seguimiento_mantenimiento` AS SELECT
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
LEFT JOIN technicians tec_exec ON mh.technician_id = tec_exec.id ;

-- ----------------------------
-- Function structure for CalcularProximaFechaMantenimiento
-- ----------------------------
DROP FUNCTION IF EXISTS `CalcularProximaFechaMantenimiento`;
delimiter ;;
CREATE FUNCTION `CalcularProximaFechaMantenimiento`(p_frequency_id INT,
    p_last_date DATE)
 RETURNS date
  DETERMINISTIC
BEGIN
    DECLARE v_interval_days INT;
    DECLARE v_next_date DATE;
    
    -- Obtener el intervalo en días para la frecuencia
    SELECT interval_days INTO v_interval_days
    FROM maintenance_frequencies
    WHERE id = p_frequency_id;
    
    -- Calcular la próxima fecha
    IF v_interval_days IS NOT NULL THEN
        SET v_next_date = DATE_ADD(p_last_date, INTERVAL v_interval_days DAY);
    ELSE
        -- Si no hay intervalo definido (mantenimiento de única vez), devolver NULL
        SET v_next_date = NULL;
    END IF;
    
    RETURN v_next_date;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for CancelarMantenimiento
-- ----------------------------
DROP PROCEDURE IF EXISTS `CancelarMantenimiento`;
delimiter ;;
CREATE PROCEDURE `CancelarMantenimiento`(IN p_schedule_id INT,
    IN p_reason TEXT,
    IN p_user_id INT)
BEGIN
    DECLARE v_current_status VARCHAR(20);
    
    -- Obtener estado actual
    SELECT status INTO v_current_status
    FROM maintenance_schedule
    WHERE id = p_schedule_id;
    
    -- Verificar si existe el schedule
    IF v_current_status IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El mantenimiento programado no existe';
    END IF;
    
    -- Solo se puede cancelar si está programado o en proceso
    IF v_current_status NOT IN ('Programado', 'En Proceso') THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Solo se pueden cancelar mantenimientos en estado "Programado" o "En Proceso"';
    END IF;
    
    -- Actualizar el schedule
    UPDATE maintenance_schedule
    SET 
        status = 'Cancelado',
        notes = CONCAT(IFNULL(notes, ''), '\nCancelado el ', NOW(), ' por usuario ', p_user_id, '. Razón: ', p_reason),
        updated_at = NOW()
    WHERE id = p_schedule_id;
    
    SELECT ROW_COUNT() AS FilasAfectadas;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for GenerarReporteMantenimientos
-- ----------------------------
DROP PROCEDURE IF EXISTS `GenerarReporteMantenimientos`;
delimiter ;;
CREATE PROCEDURE `GenerarReporteMantenimientos`(IN p_date_from DATE,
    IN p_date_to DATE,
    IN p_club_id INT,
    IN p_technician_id INT,
    IN p_status VARCHAR(20))
BEGIN
    SELECT
        ms.id AS IdPlan,
        ms.scheduled_date AS FechaProgramada,
        ms.status AS Estado,
        t.name AS Tecnico,
        COALESCE(e.NombrePC, et.name) AS Equipo,
        c.name AS Club,
        et.name AS TipoMantenimiento,
        ms.priority AS Prioridad,
        ms.notes AS Observaciones,
        mh.start_time AS FechaInicio,
        mh.end_time AS FechaFin,
        TIMESTAMPDIFF(MINUTE, mh.start_time, mh.end_time) AS DuracionMinutos,
        mh.cost AS Costo,
        mh.work_performed AS TrabajoRealizado,
        mh.next_maintenance_date AS ProximaFecha
    FROM maintenance_schedule ms
    LEFT JOIN maintenance_history mh ON ms.id = mh.maintenance_schedule_id
    LEFT JOIN technicians t ON ms.technician_id = t.id
    LEFT JOIN equipos e ON ms.equipo_id = e.IdEq
    LEFT JOIN equipment_types et ON ms.equipment_type_id = et.id
    LEFT JOIN clubs c ON ms.club_id = c.id
    WHERE 
        ms.scheduled_date BETWEEN p_date_from AND p_date_to
        AND (p_club_id IS NULL OR ms.club_id = p_club_id)
        AND (p_technician_id IS NULL OR ms.technician_id = p_technician_id)
        AND (p_status IS NULL OR ms.status = p_status)
    ORDER BY ms.scheduled_date, ms.priority DESC;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for GestionarAccionesConExcepciones
-- ----------------------------
DROP PROCEDURE IF EXISTS `GestionarAccionesConExcepciones`;
delimiter ;;
CREATE PROCEDURE `GestionarAccionesConExcepciones`(IN p_id INT)
BEGIN
    -- Declaración de variables para el manejo de errores
    DECLARE `_rollback` BOOL DEFAULT 0;
    DECLARE `msg` VARCHAR(255);

    -- =========================================================================
    -- DECLARACIÓN DE MANEJADORES DE EXCEPCIONES
    -- =========================================================================
    -- Este manejador se activa si hay una condición de error (ej. ERROR SQLSTATE).
    -- La cláusula `CONTINUE` significa que, después de ejecutar el código del manejador,
    -- el flujo del programa continúa. Esto es útil para advertencias.
    -- Aquí usamos `EXIT` para que el procedimiento termine inmediatamente si hay un error.
    -- `SQLSTATE '45000'` es un estado genérico para errores definidos por el usuario.
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Captura la información del error
        GET DIAGNOSTICS CONDITION 1
            @errno = MYSQL_ERRNO, @text = MESSAGE_TEXT;

        -- Guarda el estado del rollback y el mensaje de error
        SET `_rollback` = 1;
        SET `msg` = CONCAT('ERROR: SQLSTATE ', @errno, ', Mensaje: ', @text);

        -- Imprime un mensaje de error en la consola
        SELECT `msg` AS 'Estado';

        -- Si hay un error, se hará un ROLLBACK
        ROLLBACK;
    END;

    -- =========================================================================
    -- CUERPO PRINCIPAL DEL PROCEDIMIENTO
    -- =========================================================================
    START TRANSACTION;

    -- Llama a la función de validación (simulada)
    -- Aquí es donde llamarías a tu procedimiento `validarAcciones`
    -- Si `validarAcciones` lanza una excepción, el manejador anterior la capturará.
    -- Por ejemplo, simularemos un error si p_id es menor que 1.
    IF p_id < 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El ID proporcionado no es válido. Debe ser mayor que 0.';
    END IF;

    -- Llama a la función de acción múltiple (simulada)
    -- Aquí es donde llamarías a tu procedimiento `ejecutarAccionMultiple`
    -- Imaginemos que este procedimiento falla si p_id es 999.
    IF p_id = 999 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error simulado en ejecutarAccionMultiple.';
    END IF;

    -- Simulación de otras acciones
    -- INSERT INTO `tabla_ejemplo` VALUES (p_id, 'dato');
    -- UPDATE `otra_tabla` SET `columna` = 'nuevo_valor' WHERE `id` = p_id;

    -- Si no hubo errores, se hace el COMMIT
    IF `_rollback` THEN
        -- Si la variable `_rollback` es verdadera, significa que el manejador se activó.
        -- Ya se hizo ROLLBACK, por lo que no es necesario hacer nada más aquí.
        SELECT 'Procedimiento finalizado con ROLLBACK' AS 'Estado';
    ELSE
        -- Si no hubo errores, se aplica la transacción
        COMMIT;
        SELECT 'Procedimiento finalizado con COMMIT exitoso' AS 'Estado';
    END IF;

END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for ObtenerMantenimientosPendientesPorTecnico
-- ----------------------------
DROP PROCEDURE IF EXISTS `ObtenerMantenimientosPendientesPorTecnico`;
delimiter ;;
CREATE PROCEDURE `ObtenerMantenimientosPendientesPorTecnico`(IN p_technician_id INT,
    IN p_date_from DATE,
    IN p_date_to DATE)
BEGIN
    SELECT
        ms.id AS IdPlan,
        ms.scheduled_date AS FechaProgramada,
        ms.priority AS Prioridad,
        e.NombrePC AS NombreEquipo,
        te.NombreEq AS TipoMantenimiento,
        ms.notes AS Observaciones
    FROM maintenance_schedule ms
    INNER JOIN equipos e ON ms.equipo_id = e.IdEq
    INNER JOIN tipoeq te ON ms.equipment_type_id = te.NombreEq
    WHERE ms.technician_id = p_technician_id
      AND ms.status = 'Programado'
      AND ms.scheduled_date BETWEEN p_date_from AND p_date_to
    ORDER BY ms.scheduled_date;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for PrecargarDatosMantenimiento
-- ----------------------------
DROP PROCEDURE IF EXISTS `PrecargarDatosMantenimiento`;
delimiter ;;
CREATE PROCEDURE `PrecargarDatosMantenimiento`(IN p_year INT,
    IN p_clean_previous TINYINT(1))
BEGIN
    DECLARE v_equipo_id INT;
    DECLARE v_club_id INT;
    DECLARE v_type_id VARCHAR(255); -- Ahora esta variable es VARCHAR
    DECLARE v_tech_id INT;
    DECLARE v_quarter INT DEFAULT 0;
    DECLARE v_current_year INT;
    DECLARE v_tech_count INT DEFAULT 0;
    DECLARE v_tech_index INT DEFAULT 0;
    DECLARE done INT DEFAULT FALSE;

    -- Cursor que obtiene el tipo de equipo como VARCHAR, sin necesidad de buscar en equipment_types
    DECLARE equipos_cursor CURSOR FOR
        SELECT
            e.IdEq,
            COALESCE(e.Club, (SELECT id FROM clubs LIMIT 1)),
            -- Utiliza COALESCE para obtener el nombre del tipo de equipo (VARCHAR)
            COALESCE(e.Tipo, (SELECT NombreEq FROM tipoeq LIMIT 1))
        FROM equipos e
        WHERE e.Estado = 'A';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    SET v_current_year = IFNULL(p_year, YEAR(CURDATE()));

    IF p_clean_previous = 1 THEN
        DELETE FROM maintenance_schedule WHERE scheduled_year = v_current_year;
    END IF;

    DROP TEMPORARY TABLE IF EXISTS temp_tech;
    CREATE TEMPORARY TABLE temp_tech (
        id INT,
        row_num INT AUTO_INCREMENT PRIMARY KEY
    );

    INSERT INTO temp_tech (id)
    SELECT id FROM technicians;

    SELECT COUNT(*) INTO v_tech_count FROM temp_tech;

    IF v_tech_count = 0 THEN
        DROP TEMPORARY TABLE IF EXISTS temp_tech;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No hay técnicos activos disponibles';
    END IF;

    OPEN equipos_cursor;
    read_loop: LOOP
        FETCH equipos_cursor INTO v_equipo_id, v_club_id, v_type_id;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SET v_tech_index = (v_tech_index % v_tech_count) + 1;
        SELECT id INTO v_tech_id FROM temp_tech WHERE row_num = v_tech_index;

        INSERT INTO maintenance_schedule (
            technician_id, equipo_id, equipment_type_id, club_id,
            scheduled_date, scheduled_week, scheduled_month, scheduled_year,
            priority, status, notes, estimated_duration, frequency_id,
            created_at, updated_at
        )
        SELECT
            v_tech_id, v_equipo_id, v_type_id, v_club_id,
            CASE
                WHEN qtr = 1 THEN MAKEDATE(v_current_year, 74)
                WHEN qtr = 2 THEN MAKEDATE(v_current_year, 166)
                WHEN qtr = 3 THEN MAKEDATE(v_current_year, 258)
                WHEN qtr = 4 THEN MAKEDATE(v_current_year, 349)
            END,
            2,
            qtr * 3,
            v_current_year,
            'Media',
            'Programado',
            CONCAT('Mantenimiento preventivo - Equipo ', v_equipo_id),
            120,
            3,
            NOW(),
            NOW()
        FROM (SELECT 1 AS qtr UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) quarters;

    END LOOP;
    CLOSE equipos_cursor;

    SELECT
        CONCAT('Programación completada: ',
              (SELECT COUNT(*) FROM equipos WHERE Estado = 'A'),
              ' equipos procesados') AS resumen,
        v_current_year AS año_programado,
        (SELECT COUNT(*) FROM maintenance_schedule WHERE scheduled_year = v_current_year) AS total_mantenimientos,
        (SELECT COUNT(DISTINCT equipo_id) FROM maintenance_schedule WHERE scheduled_year = v_current_year) AS equipos_programados,
        (SELECT COUNT(DISTINCT technician_id) FROM maintenance_schedule WHERE scheduled_year = v_current_year) AS tecnicos_asignados;

    DROP TEMPORARY TABLE IF EXISTS temp_tech;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for ProgramarMantenimientoAutomatico
-- ----------------------------
DROP PROCEDURE IF EXISTS `ProgramarMantenimientoAutomatico`;
delimiter ;;
CREATE PROCEDURE `ProgramarMantenimientoAutomatico`(IN p_technician_id INT,
    IN p_equipo_id INT,
    IN p_equipment_type_id INT,
    IN p_club_id INT,
    IN p_scheduled_date DATE,
    IN p_priority ENUM('Baja', 'Media', 'Alta', 'Crítica'),
    IN p_notes TEXT,
    IN p_estimated_duration INT,
    IN p_frequency_id INT,
    IN p_num_periods INT)
BEGIN
    DECLARE v_counter INT DEFAULT 0;
    DECLARE v_current_date DATE;
    DECLARE v_interval_days INT;
    DECLARE v_week INT;
    DECLARE v_month INT;
    DECLARE v_year INT;
    
    -- Obtener días de intervalo de la frecuencia
    SELECT interval_days INTO v_interval_days
    FROM maintenance_frequencies
    WHERE id = p_frequency_id;
    
    SET v_current_date = p_scheduled_date;
    
    -- Verificar si el equipo existe
    IF p_equipo_id IS NOT NULL THEN
        IF NOT EXISTS (SELECT 1 FROM equipos WHERE IdEq = p_equipo_id) THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'El equipo especificado no existe';
        END IF;
    END IF;
    
    -- Verificar si el técnico existe
    IF p_technician_id IS NOT NULL THEN
        IF NOT EXISTS (SELECT 1 FROM technicians WHERE id = p_technician_id) THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'El técnico especificado no existe';
        END IF;
    END IF;
    
    WHILE v_counter < p_num_periods DO
        SET v_week = WEEK(v_current_date, 3);
        SET v_month = MONTH(v_current_date);
        SET v_year = YEAR(v_current_date);
        
        INSERT INTO maintenance_schedule
        (technician_id, equipo_id, equipment_type_id, club_id, scheduled_date,
         scheduled_week, scheduled_month, scheduled_year, priority, status, notes,
         estimated_duration, frequency_id)
        VALUES
        (p_technician_id, p_equipo_id, p_equipment_type_id, p_club_id, v_current_date,
         v_week, v_month, v_year, p_priority, 'Programado', p_notes,
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
    
    SELECT CONCAT('Se han programado ', v_counter, ' mantenimientos') AS Resultado;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for RegistrarEjecucionMantenimiento
-- ----------------------------
DROP PROCEDURE IF EXISTS `RegistrarEjecucionMantenimiento`;
delimiter ;;
CREATE PROCEDURE `RegistrarEjecucionMantenimiento`(IN p_schedule_id INT,
    IN p_technician_id INT,
    IN p_start_time DATETIME,
    IN p_end_time DATETIME,
    IN p_work_performed TEXT,
    IN p_cost DECIMAL(10,2),
    IN p_status ENUM('Completado', 'Fallido', 'Incompleto'),
    IN p_next_maintenance_date DATE,
    IN p_photos JSON)
BEGIN
    DECLARE v_equipo_id INT;
    DECLARE v_club_id INT;
    DECLARE v_equipment_type_id INT;
    
    -- Obtener información del schedule
    SELECT equipo_id, club_id, equipment_type_id 
    INTO v_equipo_id, v_club_id, v_equipment_type_id
    FROM maintenance_schedule
    WHERE id = p_schedule_id;
    
    -- Verificar si el schedule existe
    IF v_equipment_type_id IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El mantenimiento programado no existe';
    END IF;
    
    -- Verificar si el técnico existe
    IF p_technician_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM technicians WHERE id = p_technician_id) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El técnico especificado no existe';
    END IF;
    
    -- Insertar en el historial
    INSERT INTO maintenance_history (
        maintenance_schedule_id,
        technician_id,
        equipo_id,
        club_id,
        start_time,
        end_time,
        work_performed,
        cost,
        photos,
        status,
        next_maintenance_date
    ) VALUES (
        p_schedule_id,
        p_technician_id,
        v_equipo_id,
        v_club_id,
        p_start_time,
        p_end_time,
        p_work_performed,
        p_cost,
        p_photos,
        p_status,
        p_next_maintenance_date
    );
    
    -- Actualizar el estado del schedule si se completó
    IF p_status = 'Completado' THEN
        UPDATE maintenance_schedule 
        SET status = 'Completado',
            updated_at = NOW()
        WHERE id = p_schedule_id;
    END IF;
    
    SELECT LAST_INSERT_ID() AS IdHistorial;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for ReprogramarMantenimiento
-- ----------------------------
DROP PROCEDURE IF EXISTS `ReprogramarMantenimiento`;
delimiter ;;
CREATE PROCEDURE `ReprogramarMantenimiento`(IN p_schedule_id INT,
    IN p_new_date DATE,
    IN p_reason TEXT,
    IN p_user_id INT)
BEGIN
    DECLARE v_current_status VARCHAR(20);
    
    -- Obtener estado actual
    SELECT status INTO v_current_status
    FROM maintenance_schedule
    WHERE id = p_schedule_id;
    
    -- Verificar si existe el schedule
    IF v_current_status IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El mantenimiento programado no existe';
    END IF;
    
    -- Solo se puede reprogramar si está programado o en proceso
    IF v_current_status NOT IN ('Programado', 'En Proceso') THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Solo se pueden reprogramar mantenimientos en estado "Programado" o "En Proceso"';
    END IF;
    
    -- Actualizar el schedule
    UPDATE maintenance_schedule
    SET 
        scheduled_date = p_new_date,
        scheduled_week = WEEK(p_new_date, 3),
        scheduled_month = MONTH(p_new_date),
        scheduled_year = YEAR(p_new_date),
        status = 'Reprogramado',
        notes = CONCAT(IFNULL(notes, ''), '\nReprogramado el ', NOW(), ' por usuario ', p_user_id, '. Razón: ', p_reason),
        updated_at = NOW()
    WHERE id = p_schedule_id;
    
    SELECT ROW_COUNT() AS FilasAfectadas;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for sp_programar_mantenimientos_iniciales
-- ----------------------------
DROP PROCEDURE IF EXISTS `sp_programar_mantenimientos_iniciales`;
delimiter ;;
CREATE PROCEDURE `sp_programar_mantenimientos_iniciales`(IN p_year INT,
    IN p_limpiar_anteriores BOOLEAN)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE equipo_id INT;
    DECLARE tecnico_id INT;
    DECLARE total_tecnicos INT;
    DECLARE contador_tecnicos INT DEFAULT 0;
    
    -- Cursor para equipos activos
    DECLARE equipos_cursor CURSOR FOR 
        SELECT id FROM equipos WHERE estado = 'activo';
    
    -- Cursor para técnicos disponibles
    DECLARE tecnicos_cursor CURSOR FOR 
        SELECT id FROM tecnicos WHERE activo = TRUE ORDER BY id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Limpiar programaciones anteriores si se solicita
    IF p_limpiar_anteriores THEN
        DELETE FROM maintenance_schedule 
        WHERE YEAR(fecha_programada) = p_year;
    END IF;
    
    -- Obtener total de técnicos
    SELECT COUNT(*) INTO total_tecnicos 
    FROM tecnicos 
    WHERE activo = TRUE;
    
    -- Verificar que hay técnicos disponibles
    IF total_tecnicos = 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'No hay técnicos disponibles para asignar';
    END IF;
    
    -- Abrir cursor de técnicos
    OPEN tecnicos_cursor;
    
    -- Programar mantenimientos trimestrales
    OPEN equipos_cursor;
    equipos_loop: LOOP
        FETCH equipos_cursor INTO equipo_id;
        IF done THEN
            LEAVE equipos_loop;
        END IF;
        
        -- Rotar técnicos (asignación balanceada)
        FETCH tecnicos_cursor INTO tecnico_id;
        IF done THEN
            CLOSE tecnicos_cursor;
            OPEN tecnicos_cursor;
            FETCH tecnicos_cursor INTO tecnico_id;
            SET done = FALSE;
        END IF;
        
        -- Insertar los 4 mantenimientos trimestrales
        INSERT INTO maintenance_schedule (
            equipment_id,
            technician_id,
            fecha_programada,
            tipo_mantenimiento,
            status,
            prioridad,
            created_at
        ) VALUES 
        -- Primer trimestre (Marzo)
        (equipo_id, tecnico_id, 
         CONCAT(p_year, '-03-15'), 'preventivo', 'programado', 'media', NOW()),
        
        -- Segundo trimestre (Junio)
        (equipo_id, tecnico_id, 
         CONCAT(p_year, '-06-15'), 'preventivo', 'programado', 'media', NOW()),
        
        -- Tercer trimestre (Septiembre)
        (equipo_id, tecnico_id, 
         CONCAT(p_year, '-09-15'), 'preventivo', 'programado', 'media', NOW()),
        
        -- Cuarto trimestre (Diciembre)
        (equipo_id, tecnico_id, 
         CONCAT(p_year, '-12-15'), 'preventivo', 'programado', 'media', NOW());
        
    END LOOP;
    
    CLOSE equipos_cursor;
    CLOSE tecnicos_cursor;
    
    SELECT CONCAT('Programación creada para ', p_year) AS resultado;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table users
-- ----------------------------
DROP TRIGGER IF EXISTS `after_user_insert_add_technician`;
delimiter ;;
CREATE TRIGGER `after_user_insert_add_technician` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.club_id IN (6700, 6701, 6702, 6703, 6704) THEN -- ¡6700 AÑADIDO AQUÍ!
        INSERT INTO `technicians` (
            `user_id`,
            `name`,
            `contact_info`,
            `club_id`
        ) VALUES (
            NEW.user_id,
            CONCAT(NEW.firstname, ' ', NEW.lastname),
            NEW.user_email,
            NEW.club_id
        )
        ON DUPLICATE KEY UPDATE
            `name` = VALUES(`name`),
            `contact_info` = VALUES(`contact_info`),
            `club_id` = VALUES(`club_id`);
    END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table users
-- ----------------------------
DROP TRIGGER IF EXISTS `after_user_update_manage_technician`;
delimiter ;;
CREATE TRIGGER `after_user_update_manage_technician` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    DECLARE old_is_eligible BOOLEAN;
    DECLARE new_is_eligible BOOLEAN;

    SET old_is_eligible = OLD.club_id IN (6700, 6701, 6702, 6703, 6704); -- ¡6700 AÑADIDO AQUÍ!
    SET new_is_eligible = NEW.club_id IN (6700, 6701, 6702, 6703, 6704); -- ¡6700 AÑADIDO AQUÍ!

    -- Caso 1: El usuario transiciona A un club técnico elegible
    IF NOT old_is_eligible AND new_is_eligible THEN
        INSERT INTO `technicians` (
            `user_id`, `name`, `contact_info`, `club_id`
        ) VALUES (
            NEW.user_id, CONCAT(NEW.firstname, ' ', NEW.lastname), NEW.user_email, NEW.club_id
        )
        ON DUPLICATE KEY UPDATE
            `name` = VALUES(`name`), `contact_info` = VALUES(`contact_info`), `club_id` = VALUES(`club_id`);

    -- Caso 2: El usuario transiciona FUERA de un club técnico elegible
    ELSEIF old_is_eligible AND NOT new_is_eligible THEN
        DELETE FROM `technicians` WHERE `user_id` = OLD.user_id;

    -- Caso 3: El usuario sigue siendo parte de un club técnico elegible
    ELSEIF new_is_eligible THEN
        UPDATE `technicians`
        SET
            `name` = CONCAT(NEW.firstname, ' ', NEW.lastname),
            `contact_info` = NEW.user_email,
            `club_id` = NEW.club_id
        WHERE `user_id` = NEW.user_id;

        INSERT IGNORE INTO `technicians` (
            `user_id`, `name`, `contact_info`, `club_id`
        ) VALUES (
            NEW.user_id, CONCAT(NEW.firstname, ' ', NEW.lastname), NEW.user_email, NEW.club_id
        );
    END IF;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
