<?php
/**
 * Archivo: sidebar.php
 * Descripción: Barra lateral del sistema de mantenimiento
 * Contiene menú de navegación y elementos secundarios
 */

// Verificar si hay una sesión activa
$usuarioLogueado = isset($_SESSION['usuario_id']);
$currentPage = basename($_SERVER['PHP_SELF']);
?> <aside class="sidebar">
 <div class="sidebar-header">
  <h3 class="logo">SistemaManto</h3> <?php if($usuarioLogueado): ?> <div class="user-info">
   <span class="user-name"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?></span>
   <span class="user-role"><?= htmlspecialchars($_SESSION['usuario_rol'] ?? 'Técnico') ?></span>
  </div> <?php endif; ?>
 </div>
 <nav class="sidebar-nav">
  <ul class="nav-menu">
   <li class="nav-item <?= strpos($currentPage, 'calendario') !== false ? 'active' : '' ?>">
    <a href="?action=mantenimiento/calendario">
     <i class="fas fa-calendar-alt"></i>
     <span>Calendario</span>
    </a>
   </li>
   <li class="nav-item <?= strpos($currentPage, 'lista') !== false ? 'active' : '' ?>">
    <a href="?action=mantenimiento/lista">
     <i class="fas fa-list"></i>
     <span>Lista de Mantenimientos</span>
    </a>
   </li>
   <li class="nav-item <?= strpos($currentPage, 'formulario') !== false ? 'active' : '' ?>">
    <a href="?action=mantenimiento/formulario">
     <i class="fas fa-plus-circle"></i>
     <span>Nuevo Mantenimiento</span>
    </a>
   </li> <?php if($_SESSION['usuario_rol'] === 'admin'): ?> <li class="nav-divider">
    <span>Administración</span>
   </li>
   <li class="nav-item">
    <a href="?action=admin/tecnicos">
     <i class="fas fa-users-cog"></i>
     <span>Gestión de Técnicos</span>
    </a>
   </li>
   <li class="nav-item">
    <a href="?action=admin/equipos">
     <i class="fas fa-laptop-medical"></i>
     <span>Gestión de Equipos</span>
    </a>
   </li> <?php endif; ?>
  </ul>
 </nav>
 <div class="sidebar-footer"> <?php if($usuarioLogueado): ?> <a href="?action=auth/logout" class="logout-btn">
   <i class="fas fa-sign-out-alt"></i>
   <span>Cerrar Sesión</span>
  </a> <?php else: ?> <a href="?action=auth/login" class="login-btn">
   <i class="fas fa-sign-in-alt"></i>
   <span>Iniciar Sesión</span>
  </a> <?php endif; ?> </div>
</aside>
<style>
.sidebar {
 width: 250px;
 background: #2c3e50;
 color: #ecf0f1;
 height: 100vh;
 position: fixed;
 left: 0;
 top: 0;
 display: flex;
 flex-direction: column;
 transition: all 0.3s;
}

.sidebar-header {
 padding: 20px;
 border-bottom: 1px solid #34495e;
}

.logo {
 color: #fff;
 margin: 0 0 15px 0;
 font-size: 1.5rem;
}

.user-info {
 font-size: 0.9rem;
}

.user-name {
 display: block;
 font-weight: bold;
}

.user-role {
 font-size: 0.8rem;
 opacity: 0.8;
}

.sidebar-nav {
 flex: 1;
 overflow-y: auto;
 padding: 10px 0;
}

.nav-menu {
 list-style: none;
 padding: 0;
 margin: 0;
}

.nav-item {
 margin: 5px 0;
}

.nav-item a {
 color: #ecf0f1;
 padding: 10px 20px;
 display: flex;
 align-items: center;
 text-decoration: none;
 transition: all 0.2s;
}

.nav-item a:hover {
 background: #34495e;
}

.nav-item.active a {
 background: #3498db;
}

.nav-item i {
 margin-right: 10px;
 width: 20px;
 text-align: center;
}

.nav-divider {
 color: #7f8c8d;
 padding: 10px 20px;
 font-size: 0.8rem;
 text-transform: uppercase;
 margin-top: 15px;
}

.sidebar-footer {
 padding: 15px;
 border-top: 1px solid #34495e;
}

.logout-btn,
.login-btn {
 color: #ecf0f1;
 text-decoration: none;
 display: flex;
 align-items: center;
 padding: 8px 15px;
 border-radius: 4px;
 transition: all 0.2s;
}

.logout-btn {
 background: #e74c3c;
}

.logout-btn:hover {
 background: #c0392b;
}

.login-btn {
 background: #2ecc71;
}

.login-btn:hover {
 background: #27ae60;
}
</style>