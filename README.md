# Polyglot Lab v_PHP

Proyecto final DAW desarrollado con:

- HTML5
- CSS3
- JavaScript
- PHP
- MySQL

Este proyecto es la versión dinámica en PHP + MySQL del sitio web Polyglot Lab. Incluye registro e inicio de sesión de usuarios, gestión de perfil, citas, noticias, zonas de administración y almacenamiento de solicitudes de presupuesto.

## Requisitos

- MAMP instalado en macOS
- Apache y MySQL iniciados desde MAMP
- PHP 8.x
- MySQL / phpMyAdmin

## Instalación local con MAMP

1. Copiar la carpeta del proyecto dentro de:

```text
/Applications/MAMP/htdocs/
```

2. Iniciar Apache y MySQL desde MAMP.

3. Abrir phpMyAdmin desde MAMP.

4. Importar el archivo SQL:

```text
polyglot_lab.sql
```

5. Confirmar que se crea la base de datos `polyglot_lab`.

6. Abrir el proyecto en el navegador:

```text
http://localhost:8888/Polyglot-Lab_v_PHP/
```

## Conexión a la base de datos

El proyecto usa el archivo:

```text
includes/conexion.php
```

Configuración local por defecto:

- host: `localhost`
- port: `8889`
- user: `root`
- password: `root`
- database: `polyglot_lab`

Si tu configuración de MAMP es diferente, hay que modificar ese archivo antes de ejecutar el proyecto.

## Páginas principales

- Inicio: `index.php`
- Noticias: `noticias.php`
- Registro: `register.php`
- Login: `login.php`
- Perfil: `profile.php`
- Citas: `citas.php`
- Administración de usuarios: `admin_users.php`
- Administración de citas: `admin_citas.php`
- Administración de noticias: `admin_noticias.php`

## Funcionalidades principales

- Registro de usuarios con `password_hash()`
- Login con `password_verify()`
- Gestión de sesiones
- Navegación según rol
- Edición de perfil
- Cambio de contraseña
- CRUD de citas para usuario
- CRUD de usuarios para admin
- CRUD de citas para admin
- CRUD de noticias para admin
- Solicitudes de presupuesto guardadas en base de datos

## Tablas de base de datos

Tablas obligatorias incluidas:

- `users_data`
- `users_login`
- `citas`
- `noticias`

Tabla adicional del proyecto:

- `quote_requests`

## Acceso administrador

Para probar la zona de administración, un usuario debe tener el rol `admin` en la tabla `users_login`.

Si hace falta, el rol se puede cambiar manualmente en phpMyAdmin.

## Notas

- Este proyecto está pensado para ejecutarse en local con MAMP.
- El archivo SQL de la base de datos está incluido en el proyecto.
- Para las imágenes de noticias, se recomienda usar rutas locales como:

```text
./assets/images/gallery/clase1.jpg
```
