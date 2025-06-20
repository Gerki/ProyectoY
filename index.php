<?php
// PROYECTOY MODERN LAYOUT TEST MARKER
session_start();
require_once 'config.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$is_admin = false;
if ($is_logged_in) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    $is_admin_exists = $result->num_rows > 0;
    if ($is_admin_exists) {
        $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if (isset($user['is_admin'])) {
            $is_admin = $user['is_admin'] == 1;
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualgv.com - Dashboard SaaS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="main-header">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-chart-line"></i>
                    <h1>Visualgv.com</h1>
                </div>
                <div class="header-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Buscar..." id="searchInput">
                    </div>
                    <button class="notification-btn" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="user-menu">
                        <img src="https://via.placeholder.com/32" alt="Usuario" class="user-avatar">
                        <span class="user-name"><?php echo htmlspecialchars($user_name ?: 'Usuario'); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Sidebar Navigation -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="sidebar-content">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" data-module="organizaciones">
                            <i class="fas fa-building"></i>
                            <span>Organizaciones</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-module="personas">
                            <i class="fas fa-user"></i>
                            <span>Personas</span>
                        </a>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link" data-module="archivos">
                            <i class="fas fa-file"></i>
                            <span>Archivos</span>
                            <i class="fas fa-chevron-down submenu-toggle"></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="#" class="nav-link" data-module="chats">
                                <i class="fas fa-comments"></i>
                                <span>Chats</span>
                            </a></li>
                            <li><a href="#" class="nav-link" data-module="autorizador">
                                <i class="fas fa-user-check"></i>
                                <span>Autorizador de Archivos</span>
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-module="industrias">
                            <i class="fas fa-industry"></i>
                            <span>Industrias</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-module="inventario">
                            <i class="fas fa-box"></i>
                            <span>Inventario de Activos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-module="match">
                            <i class="fas fa-handshake"></i>
                            <span>Zona Match</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-module="ar">
                            <i class="fas fa-vr-cardboard"></i>
                            <span>Realidad Aumentada</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-module="gps">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>GPS Tracking</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-module="evidencias">
                            <i class="fas fa-camera"></i>
                            <span>Evidencias</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-module="reportes">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reportes</span>
                        </a>
                    </li>
                    <?php if ($is_admin): ?>
                    <li class="nav-item">
                        <a href="admin.php" class="nav-link">
                            <i class="fas fa-users-cog"></i>
                            <span>Admin</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-footer">
                    <button class="add-module-btn" id="addModuleBtn">
                        <i class="fas fa-plus"></i>
                        <span>Agregar Módulo</span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <div class="breadcrumb">
                    <span id="currentModuleName">Organizaciones</span>
                </div>
                <div class="content-actions">
                    <button class="btn btn-secondary" id="exportBtn">
                        <i class="fas fa-download"></i>
                        Exportar
                    </button>
                    <button class="btn btn-primary" id="saveBtn">
                        <i class="fas fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>

            <div class="dashboard-modules">
                <!-- Organizaciones Module -->
                <section class="dashboard-module active" id="module-organizaciones">
                    <div class="module-header">
                        <h2><i class="fas fa-building"></i> Organizaciones</h2>
                        <div class="module-actions">
                            <button class="btn-icon" title="Configurar">
                                <i class="fas fa-cog"></i>
                            </button>
                            <button class="btn-icon" title="Ayuda">
                                <i class="fas fa-question-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="module-content">
                        <div class="form-grid">
                            <div class="form-card">
                                <form class="enhanced-form" data-form="organizacion">
                                    <div class="form-header">
                                        <h3>Organización, Empresa o tu mismo</h3>
                                    </div>
                                    <div class="form-body">
                                        <div class="input-group">
                                            <label for="organizacion">Nombre de la organización</label>
                                            <input type="text" 
                                                   id="organizacion"
                                                   name="organizacion" 
                                                   placeholder="Organización, empresa o tu mismo"
                                                   required>
                                            <div class="input-feedback"></div>
                                        </div>
                                        <div class="button-group">
                                            <button type="submit" class="btn btn-primary" data-action="crear">
                                                <i class="fas fa-plus"></i> Crear
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-action="editar">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-danger" data-action="borrar">
                                                <i class="fas fa-trash"></i> Borrar
                                            </button>
                                            <button type="button" class="btn btn-warning" data-action="archivar">
                                                <i class="fas fa-archive"></i> Archivar
                                            </button>
                                            <button type="button" class="btn btn-success" data-action="recuperar">
                                                <i class="fas fa-undo"></i> Recuperar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="form-card">
                                <form class="enhanced-form" data-form="invitar-organizacion">
                                    <div class="form-header">
                                        <h3>Invitar Usuarios</h3>
                                    </div>
                                    <div class="form-body">
                                        <div class="input-group">
                                            <label for="emailOrganizacion">Correo electrónico</label>
                                            <input type="email" 
                                                   id="emailOrganizacion"
                                                   name="emailOrganizacion" 
                                                   placeholder="Correo electrónico para invitar a organización"
                                                   required>
                                            <div class="input-feedback"></div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-envelope"></i> Invitar usuarios a organización
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="form-card">
                                <form class="enhanced-form" data-form="proyecto">
                                    <div class="form-header">
                                        <h3>Proyectos, Campañas o Eventos</h3>
                                    </div>
                                    <div class="form-body">
                                        <div class="input-group">
                                            <label for="proyecto">Nombre del proyecto</label>
                                            <input type="text" 
                                                   id="proyecto"
                                                   name="proyecto" 
                                                   placeholder="Crea el nombre de tu proyecto"
                                                   required>
                                            <div class="input-feedback"></div>
                                        </div>
                                        <div class="button-group">
                                            <button type="submit" class="btn btn-primary" data-action="crear">
                                                <i class="fas fa-plus"></i> Crear
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-action="editar">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-danger" data-action="borrar">
                                                <i class="fas fa-trash"></i> Borrar
                                            </button>
                                            <button type="button" class="btn btn-warning" data-action="archivar">
                                                <i class="fas fa-archive"></i> Archivar
                                            </button>
                                            <button type="button" class="btn btn-success" data-action="recuperar">
                                                <i class="fas fa-undo"></i> Recuperar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="form-card">
                                <form class="enhanced-form" data-form="invitar-proyecto">
                                    <div class="form-header">
                                        <h3>Invitar Usuarios al Proyecto</h3>
                                    </div>
                                    <div class="form-body">
                                        <div class="input-group">
                                            <label for="emailProyecto">Correo electrónico</label>
                                            <input type="email" 
                                                   id="emailProyecto"
                                                   name="emailProyecto" 
                                                   placeholder="Correo electrónico para invitar a proyecto"
                                                   required>
                                            <div class="input-feedback"></div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-envelope"></i> Invitar usuarios a proyecto
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Personas Module -->
                <section class="dashboard-module" id="module-personas">
                    <div class="module-header">
                        <h2><i class="fas fa-user"></i> Personas</h2>
                        <div class="module-actions">
                            <button class="btn-icon" title="Configurar">
                                <i class="fas fa-cog"></i>
                            </button>
                        </div>
                    </div>
                    <div class="module-content">
                        <div class="form-grid">
                            <div class="form-card">
                                <form class="enhanced-form" data-form="link">
                                    <div class="form-header">
                                        <h3>Link de Red Social</h3>
                                        <p>Agrega el link de la red social o de la geolocalización de alguna persona</p>
                                    </div>
                                    <div class="form-body">
                                        <div class="input-group">
                                            <label for="link">URL del link</label>
                                            <input type="url" 
                                                   id="link"
                                                   name="link" 
                                                   placeholder="https://ejemplo.com/perfil"
                                                   required>
                                            <div class="input-feedback"></div>
                                        </div>
                                        <div class="button-group">
                                            <button type="submit" class="btn btn-primary" data-action="crear">
                                                <i class="fas fa-plus"></i> Crear
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-action="editar">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-danger" data-action="borrar">
                                                <i class="fas fa-trash"></i> Borrar
                                            </button>
                                            <button type="button" class="btn btn-warning" data-action="archivar">
                                                <i class="fas fa-archive"></i> Archivar
                                            </button>
                                            <button type="button" class="btn btn-success" data-action="recuperar">
                                                <i class="fas fa-undo"></i> Recuperar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Archivos Module -->
                <section class="dashboard-module" id="module-archivos">
                    <div class="module-header">
                        <h2><i class="fas fa-file"></i> Archivos</h2>
                        <div class="module-actions">
                            <button class="btn-icon" title="Configurar">
                                <i class="fas fa-cog"></i>
                            </button>
                        </div>
                    </div>
                    <div class="module-content">
                        <div class="upload-area" id="uploadArea">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h3>Suba un archivo - Upload a File</h3>
                                <p>Arrastra archivos aquí o haz clic para seleccionar</p>
                                <input type="file" id="fileInput" multiple hidden>
                                <button type="button" class="btn btn-primary" id="browseBtn">
                                    <i class="fas fa-folder-open"></i> Examinar archivos
                                </button>
                            </div>
                            <div class="upload-progress" id="uploadProgress" style="display: none;">
                                <div class="progress-bar">
                                    <div class="progress-fill"></div>
                                </div>
                                <span class="progress-text">0%</span>
                            </div>
                        </div>
                        
                        <div class="files-list" id="filesList">
                            <!-- Files will be displayed here -->
                        </div>
                    </div>
                </section>

                <!-- Other modules with simplified content for brevity -->
                <section class="dashboard-module" id="module-industrias">
                    <div class="module-header">
                        <h2><i class="fas fa-industry"></i> Industrias</h2>
                    </div>
                    <div class="module-content">
                        <div class="industry-selector">
                            <h3>Procesos e Industrias</h3>
                            <div class="selector-grid">
                                <div class="selector-group">
                                    <label for="tipoIndustria">Tipo</label>
                                    <select id="tipoIndustria" class="form-select">
                                        <option value="">Seleccionar tipo</option>
                                        <option value="publicidad">Publicidad</option>
                                        <option value="administracion">Administración de organizaciones</option>
                                    </select>
                                </div>
                                <div class="selector-group">
                                    <label for="subcategoria">Subcategoría</label>
                                    <select id="subcategoria" class="form-select">
                                        <option value="">Seleccionar subcategoría</option>
                                        <option value="exterior-fija">Exterior Fija</option>
                                        <option value="exterior-movil">Exterior Móvil</option>
                                    </select>
                                </div>
                                <div class="selector-group">
                                    <label for="tipoProceso">Tipo de proceso</label>
                                    <select id="tipoProceso" class="form-select">
                                        <option value="">Seleccionar proceso</option>
                                        <option value="impresion-offset">Impresión offset</option>
                                        <option value="impresion-digital">Impresión digital gran formato</option>
                                        <option value="diseno-industrial">Diseño industrial</option>
                                        <option value="seguimiento-gps">Seguimiento GPS</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Simplified modules for other sections -->
                <section class="dashboard-module" id="module-inventario">
                    <div class="module-header">
                        <h2><i class="fas fa-box"></i> Inventario de Activos</h2>
                    </div>
                    <div class="module-content">
                        <div class="content-placeholder">
                            <i class="fas fa-box"></i>
                            <h3>Inventario de Activos</h3>
                            <p>Gestiona tu inventario de activos digitales y físicos</p>
                            <textarea placeholder="Agrega información sobre Inventario de Activos..." rows="4"></textarea>
                        </div>
                    </div>
                </section>

                <section class="dashboard-module" id="module-match">
                    <div class="module-header">
                        <h2><i class="fas fa-handshake"></i> Zona Match</h2>
                    </div>
                    <div class="module-content">
                        <div class="content-placeholder">
                            <i class="fas fa-handshake"></i>
                            <h3>Zona Match</h3>
                            <p>Conecta con otros usuarios y organizaciones</p>
                            <textarea placeholder="Agrega información sobre Zona Match..." rows="4"></textarea>
                        </div>
                    </div>
                </section>

                <section class="dashboard-module" id="module-ar">
                    <div class="module-header">
                        <h2><i class="fas fa-vr-cardboard"></i> Realidad Aumentada</h2>
                    </div>
                    <div class="module-content">
                        <div class="content-placeholder">
                            <i class="fas fa-vr-cardboard"></i>
                            <h3>Realidad Aumentada</h3>
                            <p>Experiencias inmersivas con AR</p>
                            <textarea placeholder="Agrega información sobre Realidad Aumentada..." rows="4"></textarea>
                        </div>
                    </div>
                </section>

                <section class="dashboard-module" id="module-gps">
                    <div class="module-header">
                        <h2><i class="fas fa-map-marker-alt"></i> GPS Tracking</h2>
                    </div>
                    <div class="module-content">
                        <div class="content-placeholder">
                            <i class="fas fa-map-marker-alt"></i>
                            <h3>GPS Tracking</h3>
                            <p>Seguimiento y localización en tiempo real</p>
                            <textarea placeholder="Agrega información sobre GPS Tracking..." rows="4"></textarea>
                        </div>
                    </div>
                </section>

                <section class="dashboard-module" id="module-evidencias">
                    <div class="module-header">
                        <h2><i class="fas fa-camera"></i> Evidencias</h2>
                    </div>
                    <div class="module-content">
                        <div class="content-placeholder">
                            <i class="fas fa-camera"></i>
                            <h3>Evidencias</h3>
                            <p>Captura y gestiona evidencias fotográficas</p>
                            <textarea placeholder="Agrega información sobre Evidencias..." rows="4"></textarea>
                        </div>
                    </div>
                </section>

                <section class="dashboard-module" id="module-reportes">
                    <div class="module-header">
                        <h2><i class="fas fa-chart-bar"></i> Reportes</h2>
                    </div>
                    <div class="module-content">
                        <div class="content-placeholder">
                            <i class="fas fa-chart-bar"></i>
                            <h3>Reportes</h3>
                            <p>Genera reportes y análisis detallados</p>
                            <textarea placeholder="Agrega información sobre Reportes..." rows="4"></textarea>
                        </div>
                    </div>
                </section>

                <section class="dashboard-module" id="module-chats">
                    <div class="module-header">
                        <h2><i class="fas fa-comments"></i> Chats</h2>
                    </div>
                    <div class="module-content">
                        <div class="chat-container">
                            <div class="chat-messages" id="chatMessages">
                                <div class="message message-other">
                                    <div class="message-content">Comentarios de otras personas</div>
                                    <div class="message-time">10:30</div>
                                </div>
                                <div class="message message-own">
                                    <div class="message-content">Mis comentarios van del lado derecho</div>
                                    <div class="message-time">10:32</div>
                                </div>
                            </div>
                            <div class="chat-input">
                                <input type="text" placeholder="Escribe un mensaje..." id="chatInput">
                                <button type="button" class="btn btn-primary" id="sendMessage">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="dashboard-module" id="module-autorizador">
                    <div class="module-header">
                        <h2><i class="fas fa-user-check"></i> Autorizador de Archivos</h2>
                    </div>
                    <div class="module-content">
                        <div class="content-placeholder">
                            <i class="fas fa-user-check"></i>
                            <h3>Autorizador de Archivos</h3>
                            <p>Chat para ponerse de acuerdo sobre archivos</p>
                            <textarea placeholder="Agrega información sobre Autorizador de Archivos..." rows="4"></textarea>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Module Creation Modal -->
    <div class="modal" id="moduleModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Crear Nuevo Módulo</h2>
                <button class="modal-close" id="modalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="moduleForm">
                <div class="modal-body">
                    <div class="input-group">
                        <label for="moduleName">Nombre del Módulo</label>
                        <input type="text" id="moduleName" required>
                    </div>
                    <div class="input-group">
                        <label for="moduleDescription">Descripción</label>
                        <textarea id="moduleDescription" required></textarea>
                    </div>
                    <div class="input-group">
                        <label for="moduleIcon">Icono</label>
                        <select id="moduleIcon" required>
                            <option value="">Seleccionar icono</option>
                            <option value="fas fa-cube">Cubo</option>
                            <option value="fas fa-star">Estrella</option>
                            <option value="fas fa-heart">Corazón</option>
                            <option value="fas fa-lightbulb">Bombilla</option>
                            <option value="fas fa-rocket">Cohete</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelModule">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Módulo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container" id="toastContainer"></div>

    <script src="main.js"></script>
</body>
</html> 