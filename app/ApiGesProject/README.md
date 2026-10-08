<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

PHP >= 8.2
Composer
MySQL
Git

Clonar el repositorio
git clone https://github.com/tu-usuario/taskflow_api.git

Muévete al proyecto
cd taskflow_api

Instalar dependencias de PHP
composer install

Configurar el archivo de entorno
Copia el archivo de ejemplo:
cp .env.example .env

Genera la clave de la aplicación:
php artisan key:generate

Configurar la base de datos
Abra el archivo .env y edite estas líneas con sus datos de MySQL:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow_db
DB_USERNAME=root
DB_PASSWORD=tu_contraseña

Crea la base de datos en MySQL:
CREATE DATABASE taskflow_db;

Ejecutar migraciones y poblar la base de datos
php artisan migrate:fresh --seed

Esto crea todas las tablas necesarias y las llena con datos de prueba: usuarios con user_code, contactos aceptados, proyectos y tareas.

Levantar el servidor
php artisan serve

La API quedará disponible en:
http://127.0.0.1:8000

Correr los tests
El proyecto usa Pest como framework de testing. Para correr todos los tests:
php artisan test

Qué cubren los tests:
tests/Feature/AuthTest.php
Registro de usuario exitoso, generación automática de user_code y login con Sanctum.

tests/Feature/ContactoTest.php
Envío de solicitudes por user_code, aceptación de participantes y rechazo de duplicados.

tests/Feature/ProyectoTest.php
Creación de proyectos y adición de miembros (solo contactos aceptados).

tests/Feature/TareaTest.php
Asignación de tareas, cambio a estado review_pending, aprobación (completed) y rechazo devolviendo a in_progress.

Probar los endpoints con curl

Registrar un usuario:
curl -X POST http://127.0.0.1:8000/api/registro \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Ian Karlhos","email":"ianks@gmail.com","password":"12345678"}'

Iniciar sesión:
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"ianks@gmail.com","password":"12345678"}'

Copia el token y el user_code que devuelve la respuesta, lo vas a necesitar en las siguientes peticiones.

Solicitar participante enviando su user_code:
curl -X POST http://127.0.0.1:8000/api/contactos/solicitar \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN" \
  -d '{"user_code":"USR-8F32A"}'

Ver solicitudes pendientes recibidas:
curl -X GET http://127.0.0.1:8000/api/contactos/pendientes \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN"

Responder (Aceptar) solicitud de contacto:
curl -X PATCH http://127.0.0.1:8000/api/contactos/1/responder \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN" \
  -d '{"status":"accepted"}'

Listar tus contactos/participantes confirmados:
curl -X GET http://127.0.0.1:8000/api/contactos \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN"

Crear un proyecto:
curl -X POST http://127.0.0.1:8000/api/proyectos \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN" \
  -d '{"name":"Sistema E-Commerce","description":"Desarrollo de API backend","start_date":"2026-10-01","end_date":"2026-12-31"}'

Agregar un miembro al proyecto (debe ser un contacto aceptado):
curl -X POST http://127.0.0.1:8000/api/proyectos/1/agregar-miembro \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN" \
  -d '{"user_id":2}'

Crear y asignar una tarea:
curl -X POST http://127.0.0.1:8000/api/tareas \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN" \
  -d '{"title":"Migraciones BD","description":"Crear migraciones y modelos","project_id":1,"assigned_to":2,"due_date":"2026-10-15 18:00:00"}'

Cambiar estado de tarea a "Revisión Pendiente" (review_pending):
curl -X PATCH http://127.0.0.1:8000/api/tareas/1/cambiar-estado \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN_DESARROLLADOR" \
  -d '{"status":"review_pending"}'

Revisar tarea (Rechazar y devolver a in_progress):
curl -X PATCH http://127.0.0.1:8000/api/tareas/1/revisar \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN_CREADOR" \
  -d '{"aprobado":false,"rejection_reason":"Faltan claves foráneas en la tabla tareas."}'

Revisar tarea (Aprobar y pasar a completed):
curl -X PATCH http://127.0.0.1:8000/api/tareas/1/revisar \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN_CREADOR" \
  -d '{"aprobado":true}'

Cerrar sesión:
curl -X POST http://127.0.0.1:8000/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TU_TOKEN"