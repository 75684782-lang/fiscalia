    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Archivo Fiscal</title>
    <link rel="stylesheet" href="<?php echo PUBLIC_CSS; ?>estilos.css">
    <style>
        /* Estilos exclusivos para el Login Split-Screen */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            background-color: white;
        }
        .split-login-container {
            display: flex;
            height: 100vh;
        }
        .login-image-side {
            flex: 1.3;
            /* Imagen de alta calidad de Unsplash (Temática Justicia/Leyes) con filtro azul */
            background: linear-gradient(rgba(30, 64, 175, 0.85), rgba(37, 99, 235, 0.75)), url('https://images.unsplash.com/photo-1589829085413-56de8ae18c73?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 5rem;
            color: white;
            position: relative;
        }
        .login-image-side h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            animation: slideInLeft 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        .login-image-side p {
            font-size: 1.25rem;
            opacity: 0.9;
            max-width: 500px;
            line-height: 1.6;
            animation: slideInLeft 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s forwards;
            opacity: 0;
        }
        .login-form-side {
            flex: 1;
            max-width: 550px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            box-shadow: -20px 0 40px rgba(0,0,0,0.1);
            z-index: 10;
        }
        .login-form-wrapper {
            width: 100%;
            animation: fadeIn 1s ease-out;
        }
        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
            animation: slideInUp 0.6s ease-out forwards;
        }
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stagger-1 { animation: fadeInUp 0.6s ease-out 0.1s forwards; opacity: 0; }
        .stagger-2 { animation: fadeInUp 0.6s ease-out 0.2s forwards; opacity: 0; }
        .stagger-3 { animation: fadeInUp 0.6s ease-out 0.3s forwards; opacity: 0; }
        .stagger-4 { animation: fadeInUp 0.6s ease-out 0.4s forwards; opacity: 0; }

        /* Responsivo: Ocultar imagen en celulares */
        @media (max-width: 960px) {
            .login-image-side { display: none; }
            .login-form-side { max-width: 100%; flex: 1; padding: 2rem; }
        }
    </style>
</head>
<body>

<div class="split-login-container">
    <!-- LADO IZQUIERDO: Branding e Imagen -->
    <div class="login-image-side">
        <h1>Gestión Fiscal<br>Inteligente</h1>
        <p>Plataforma integral para el control, seguridad y trazabilidad absoluta de todos los expedientes y carpetas del Ministerio Público.</p>
    </div>

    <!-- LADO DERECHO: Formulario de Ingreso -->
    <div class="login-form-side">
        <div class="login-form-wrapper">
            <div class="brand-icon">⚖️</div>
            <h2 class="stagger-1" style="font-size: 2rem; font-weight: 800; color: var(--text); margin-bottom: 0.5rem;">Bienvenido</h2>
            <p class="text-muted mb-4 stagger-1" style="font-size: 1.1rem;">Ingrese sus credenciales para continuar</p>
        </div>

        <?php if (isset($_SESSION['mensaje'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje'] ?? 'info'; ?> stagger-2">
                <?php 
                echo $_SESSION['mensaje'];
                unset($_SESSION['mensaje']);
                ?>
            </div>
        <?php } ?>

        <form action="<?php echo BASE_URL; ?>controllers/UsuarioController.php" method="POST" class="form-registro">
            <div class="form-group stagger-2">
                <label for="username" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">Usuario Institucional</label>
                <input type="text" id="username" name="username" placeholder="Ej: admin" required autofocus style="padding: 1rem; font-size: 1rem;">
            </div>
            
            <div class="form-group stagger-3">
                <label for="password" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">Contraseña de Acceso</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required style="padding: 1rem; font-size: 1rem;">
            </div>
            
            <button type="submit" name="login" class="btn-primary w-full mt-2 stagger-4" style="padding: 1rem; font-size: 1.1rem; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);">
                Ingresar al Sistema
            </button>
        </form>

        <div class="mt-4 p-4 stagger-4" style="background-color: #f8fafc; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 0.95rem;">
            <strong style="display: block; margin-bottom: 0.75rem; color: var(--text); display: flex; align-items: center; gap: 0.5rem;">
                <span style="color: var(--warning);">⚡</span> Credenciales de Acceso:
            </strong>
            <div style="color: var(--text-muted); line-height: 1.8;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border); padding-bottom: 0.25rem;">
                    <span>Administrador:</span> <strong style="color: var(--text); font-family: monospace; font-size: 1.1rem;">admin</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: 0.25rem;">
                    <span>Contraseña:</span> <strong style="color: var(--text); font-family: monospace; font-size: 1.1rem;">1234</strong>
                </div>
            </div>
        </div>
    </div>
</div>
