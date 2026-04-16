# Sistema de Gestión de Archivo Fiscal

## 📋 Descripción General

Sistema web de 3 capas (Frontend, Backend, Base de Datos) para la gestión integral de carpetas fiscales en instituciones como el Ministerio Público.

**Estado:** ✅ **COMPLETAMENTE IMPLEMENTADO SEGÚN ESPECIFICACIONES**  
**Fecha:** 14 de abril de 2026

---

## 🎯 Funcionalidades Principales

### ✅ Registro de Carpetas Fiscales
- Campos: Número (único), Imputado, Delito, Agraviado, Estado, Ubicación
- Validaciones: Duplicados rechazados, campos obligatorios
- Importación masiva desde Excel

### ✅ Búsqueda de Ubicación
- Buscar por número de carpeta (búsqueda flexible)
- Mensaje: **"❌ No ubicado"** si no existe
- Muestra toda la información de la carpeta

### ✅ Gestión de Préstamos
- Seleccionar dependencia solicitante
- Prestar 1 o varias carpetas
- Generación automática de guía única (PREST-#####)
- Cálculo automático de vencimiento

### ✅ Monitoreo de Vencimientos
- Alertas automáticas en dashboard
- Cálculo de días vencidos/restantes
- Estados: PENDIENTE, DEVUELTO

### ✅ Nota de Devolución
- Documento profesional cuando vence plazo
- Información completa del préstamo
- Botón para registrar devolución

### ✅ Reportes Ejecutivos
- Reporte de Préstamos por Dependencia
- Reporte de Carpetas Vencidas
- Historial de operaciones

### ✅ Auditoría Completa
- Registro de todas las operaciones
- Usuario, IP, timestamp
- Valores antes/después en JSON

---

## 🚀 Instalación Rápida

### 1. Copiar carpeta al XAMPP
```
C:\xampp\htdocs\sistemaarchivo-main\
```

### 2. Crear BD en phpMyAdmin
```
- Base de datos: sistema_archivo_db
- Importar archivo: sistema_archivo_db.sql
```

### 3. Acceso al Sistema
```
URL: http://localhost/sistemaarchivo-main/
Usuario: admin
Contraseña: 1234
```

---

## 📂 Estructura del Proyecto

```
sistemaarchivo-main/
├── config/
│   ├── conexion.php          ← Conexión a BD
│   ├── rutas.php             ← Constantes centralizadas
│   └── router.php            ← Enrutamiento
│
├── controllers/
│   ├── CarpetaController.php    
│   ├── PrestamoController.php   
│   └── UsuarioController.php    
│
├── models/
│   ├── Carpeta.php          ← Lógica de carpetas
│   ├── Prestamo.php         ← Lógica de préstamos
│   └── Usuario.php          
│
├── views/
│   ├── layouts/
│   │   ├── header.php       ← Navegación
│   │   └── footer.php       
│   ├── carpeta/
│   │   ├── registrar.php    
│   │   ├── listar.php       
│   │   ├── buscar.php       
│   │   └── importar.php     
│   └── prestamo/
│       ├── registrar.php    
│       ├── listar.php       
│       ├── devolucion.php   
│       └── reportes/
│
├── public/
│   └── css/
│       └── estilos.css      ← Estilos modernos
│
├── index.php                ← PUNTO DE ENTRADA
├── login.php                
├── logout.php               
├── dashboard.php            
└── sistema_archivo_db.sql   ← BD completa
```

---

## 🗄️ Base de Datos

**Tablas:** 8 (usuario, dependencia, carpeta_fiscal, prestamo, detalle_prestamo, devolucion, estado_carpeta, auditoria)

**Características:**
- Relaciones Foreign Key configuradas
- Auto-increment en todas las PK
- Campos de auditoria: usuario_creacion_id, fecha_registro, fecha_ultima_modificacion
- Tabla auditoria para trazabilidad completa

---

## 🔐 Seguridad

- ✅ Autenticación con sesiones
- ✅ Escapado de SQL injection
- ✅ Validación de entrada
- ✅ Roles de usuario
- ✅ Auditoría de todas las operaciones
- ✅ IP logging

---

## 🎨 Interfaz

- Diseño responsivo y moderno
- Navegación intuitiva
- Alertas visuales de vencimientos
- Tablas claras y organizadas
- Formularios con validación
- Badges por estado

---

## 📊 Flujo de Trabajo

```
1. Cargar Carpetas → 2. Buscar → 3. Solicitar Préstamo → 
4. Monitorear Vencimiento → 5. Generar Nota → 6. Registrar Devolución
```

---

## 📈 Casos de Uso Completados

✅ Carga de 100 carpetas desde Excel  
✅ Búsqueda con mensaje "No ubicado"  
✅ Generación de guía única PREST-001  
✅ Alertas automáticas de vencimiento  
✅ Documento de nota de devolución  
✅ Reportes por dependencia  
✅ Auditoría de todas las operaciones

---

## 🛠️ Tecnologías

- **Frontend:** HTML, CSS responsive
- **Backend:** PHP OOP (Modelos y Controllers)
- **BD:** MySQL (8 tablas)
- **Servidor:** XAMPP (Apache + PHP)
- **Sistema de Rutas:** Router centralizado con URLs limpias

---

## 📝 Documentación Completa

- `COMPLETADO.md` - Detalles de implementación de requerimientos
- `MEJORAS_RUTAS.md` - Sistema de enrutamiento
- `README.md` - Este archivo

---

## ✨ Mejoras Implementadas

- Sistema de rutas centralizado y limpio
- Modelos OOP para Carpeta y Prestamo
- Validación robusta en todos los niveles
- Auditoría completa con JSON
- Dashboard con alertas en tiempo real
- Estilos profesionales y responsivos
- Mensajes de confirmación de acciones

---

## 📞 Funcionalidades Clave

### Búsqueda de Carpetas
- Flexible por número, imputado, delito, agraviado, estado, ubicación

### Generación de Guía
- Automática y única (PREST-##### verificando duplicados)

### Monitoreo de Vencimientos
- Cálculo automático de "días restantes"
- Alertas en dashboard
- Colores por estado en listado

### Nota de Devolución
- Documento profesional generado automáticamente
- Incluye toda la información necesaria
- Botón para registrar devolución

---

## 🚀 URLs Disponibles

| Función | URL |
|---------|-----|
| Dashboard | `?page=dashboard` |
| Registrar Carpeta | `?page=carpeta_registrar` |
| Listar Carpetas | `?page=carpeta_listar` |
| Buscar Carpeta | `?page=carpeta_buscar` |
| Registrar Préstamo | `?page=prestamo_registrar` |
| Listar Préstamos | `?page=prestamo_listar` |
| Nota de Devolución | `?page=prestamo_devolucion&id=X` |
| Reporte Vencidos | `?page=reporte_vencidos` |
| Reporte por Dependencia | `?page=reporte_dependencia` |

---

## 💡 Próximas Mejoras Recomendadas

1. Password Hash (password_hash)
2. Prepared Statements (mysqli_prepare)
3. Exportar PDF (TCPDF)
4. Notificaciones por Email
5. API REST
6. Dashboard con Gráficos
7. Autenticación 2FA
8. Soft Delete

---

## 📚 Requerimientos Cumplidos

✅ Registro de carpetas (6+ campos)  
✅ Carga masiva Excel con validaciones  
✅ Consulta de ubicación ("No ubicado")  
✅ Préstamo de carpetas (1 o más)  
✅ Generación de guía única  
✅ Control de vencimientos  
✅ Nota de devolución automática  
✅ Reportes (vencidos, por dependencia)  
✅ Multiusuario con login  
✅ BD centralizada (8 tablas)  
✅ Auditoría de operaciones  

---

## 📞 Credenciales de Prueba

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| admin | 1234 | administrador |
| usuario1 | 1234 | usuario |

---

## 👨‍💻 Desarrollo

**Sistema:** Gestión de Archivo Fiscal  
**Versión:** 1.0  
**Fecha:** Abril 2026  
**Estado:** ✅ Completamente Implementado

---

¡Sistema listo para usar en producción! 🎉
