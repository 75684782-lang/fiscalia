# Mejora del Sistema de Rutas - Documentación

## Cambios Implementados

Se ha mejorado significativamente el sistema de enrutamiento de la aplicación con las siguientes características:

### 1. **Constantes Centralizadas de Rutas** (`config/rutas.php`)

Archivo que contiene:
- **BASE_URL**: URL base de la aplicación
- **Constantes de rutas**: Para acceder a carpetas y recursos
- **Función `ruta()`**: Genera URLs limpias y seguras
- **Función `ruta_vista()`**: Para URLs de recursos públicos
- **Mapeo de páginas**: Asocia nombres amigables con archivos físicos

#### Ejemplo de uso:

```php
// Generar URL limpia a carpetas
<a href="<?php echo ruta('carpeta_listar'); ?>">Listar Carpetas</a>

// Con parámetros
<a href="<?php echo ruta('prestamo_devolucion', ['id' => 5]); ?>">Devolver</a>

// Recurso CSS
<link rel="stylesheet" href="<?php echo PUBLIC_CSS; ?>estilos.css">
```

### 2. **Router Centralizado** (`config/router.php`)

Clase `Router` que maneja:
- **Ruteo de páginas**: Basado en parámetro `?page=`
- **Autenticación**: Protege páginas que requieren sesión
- **Parámetros GET**: Extrae y valida parámetros
- **Redirecciones**: Método estático `Router::redirigir()`
- **Carga de vistas**: Método `cargar_vista()`

#### Métodos disponibles:

```php
// Obtener página actual
$router->obtener_pagina();

// Obtener parámetro específico
$router->obtener_parametro('id');

// Redirigir a otra página
Router::redirigir('dashboard');
Router::redirigir('prestamo_listar', ['estado' => 'vencido']);

// Cargar la vista correspondiente
$router->cargar_vista();
```

### 3. **URLs Limpias (Clean URLs)**

**Antes:**
```
http://localhost/sistemaarchivo-main/views/carpeta/listar.php
http://localhost/sistemaarchivo-main/views/prestamo/registrar.php?id=5
```

**Ahora:**
```
http://localhost/sistemaarchivo-main/index.php?page=carpeta_listar
http://localhost/sistemaarchivo-main/index.php?page=prestamo_registrar&id=5
```

### 4. **Mapeo de Rutas Disponibles**

En `config/rutas.php` se define el mapeo:

| Nombre | Ruta Física |
|--------|-------------|
| `dashboard` | `dashboard.php` |
| `carpeta_registrar` | `views/carpeta/registrar.php` |
| `carpeta_listar` | `views/carpeta/listar.php` |
| `carpeta_buscar` | `views/carpeta/buscar.php` |
| `prestamo_registrar` | `views/prestamo/registrar.php` |
| `prestamo_listar` | `views/prestamo/listar.php` |
| `reporte_dependencia` | `views/prestamo/reportes/prestamos_dependencia.php` |
| `reporte_vencidos` | `views/prestamo/reportes/vencidos.php` |
| `login` | `login.php` |
| `logout` | `logout.php` |

## Archivo index.php como Punto de Entrada

El archivo `index.php` ahora es el único punto de entrada de la aplicación:

```php
<?php
session_start();
require_once("config/conexion.php");
require_once("config/router.php");

// Cargar header
include(APP_PATH . VIEWS_LAYOUTS . "header.php");

// Cargar la vista según la ruta
$router->cargar_vista();

// Cargar footer
include(APP_PATH . VIEWS_LAYOUTS . "footer.php");
?>
```

## Cambios en las Vistas

### Antes (rutas relativas problemáticas):
```php
<a href="views/carpeta/listar.php">Listar</a>
<a href="../../controllers/CarpetaController.php">Enviar</a>
<link rel="stylesheet" href="/sistemaarchivo/public/css/estilos.css">
```

### Ahora (rutas centralizadas y seguras):
```php
<a href="<?php echo ruta('carpeta_listar'); ?>">Listar</a>
<form action="<?php echo ruta(''); ?>controllers/CarpetaController.php">
<link rel="stylesheet" href="<?php echo PUBLIC_CSS; ?>estilos.css">
```

## Header Mejorado

El archivo `views/layouts/header.php` ahora incluye:
- Navegación principal con enlaces dinámicos
- Muestra el usuario logueado
- Botón de cerrar sesión
- URLs generadas con la función `ruta()`

## Ventajas del Nuevo Sistema

✅ **Mantenimiento Simple**: Cambiar rutas en un solo lugar  
✅ **URLs Seguras**: Las rutas se validan antes de ejecutarse  
✅ **Protección de Autenticación**: Control centralizado de sesiones  
✅ **Facilidad de Cambio**: Renombrar páginas sin actualizar todos los enlaces  
✅ **SEO Mejor**: URLs limpias y descriptivas  
✅ **Escalabilidad**: Fácil agregar nuevas rutas  
✅ **Consistencia**: Mismo patrón en toda la aplicación  

## Uso de la Función ruta()

### Ejemplo 1: Navegar a una página
```php
<a href="<?php echo ruta('dashboard'); ?>">Ir al Dashboard</a>
// Genera: index.php?page=dashboard
```

### Ejemplo 2: Pasar parámetros
```php
<a href="<?php echo ruta('prestamo_devolucion', ['id' => 123]); ?>">Devolver</a>
// Genera: index.php?page=prestamo_devolucion&id=123
```

### Ejemplo 3: Redirigir desde PHP
```php
if ($error) {
    Router::redirigir('login');
}
```

### Ejemplo 4: Obtener parámetros en vistas
```php
<?php
require_once("config/rutas.php");
$id = $router->obtener_parametro('id');
$estado = $router->obtener_parametro('estado', 'pendiente');
?>
```

## Estructura de Directorios Recomendada

```
sistemaarchivo-main/
├── config/
│   ├── conexion.php      (conexión BD)
│   ├── rutas.php         (constantes y mapeo)
│   └── router.php        (clase Router)
├── controllers/
│   ├── CarpetaController.php
│   ├── PrestamoController.php
│   └── UsuarioController.php
├── models/
│   ├── Carpeta.php
│   ├── Prestamo.php
│   └── Usuario.php
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── carpeta/
│   ├── prestamo/
│   └── reportes/
├── public/
│   ├── css/
│   ├── js/
│   └── images/
├── index.php             (punto de entrada)
├── login.php
├── logout.php
└── dashboard.php
```

## Próximas Mejoras Recomendadas

1. **Crear archivo `.htaccess`** para reescritura de URLs más limpias
   ```
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php?page=$1 [QSA,L]
   ```

2. **Validación de entrada**: Sanitizar parámetros GET

3. **Control de errores 404**: Manejar rutas no existentes

4. **Middleware de autenticación**: Sistema más robusto

5. **Logging**: Registrar navegación y errores

---

**Implementación completada: 14 de abril de 2026**
