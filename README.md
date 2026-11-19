# DelfinBeta 🐬

Sitio web de mi marca personal **DelfinBeta**.

🌐 **[delfinbeta.tech](https://delfinbeta.tech/)**

Aquí comparto quién soy, en qué trabajo y cómo contactarme como desarrolladora web.

## Tecnologías 🛠️

- [PHP](https://www.php.net/) 8.3+
- [Laravel](https://laravel.com/) 13
- [Livewire](https://livewire.laravel.com/) 4 + [Flux UI](https://fluxui.dev/) 2
- [Laravel Fortify](https://laravel.com/docs/fortify) para la autenticación
- [Tailwind CSS](https://tailwindcss.com/) 4 y [Vite](https://vite.dev/)
- [Pest](https://pestphp.com/) 5 para las pruebas

## Requisitos 📋

- PHP 8.3 o superior
- Composer
- Node.js y npm
- SQLite (por defecto) o MySQL

## Instalación 🚀

```bash
git clone git@github.com:DelfinBetaProyectos/delfinbeta.git
cd delfinbeta
composer setup
```

El script `composer setup` instala las dependencias de PHP y Node, crea el archivo `.env`, genera la clave de la aplicación, ejecuta las migraciones y compila los assets.

## Desarrollo 💻

Levanta el servidor, la cola, los logs y Vite al mismo tiempo:

```bash
composer dev
```

La aplicación queda disponible en `http://localhost:8000`.

## Calidad de código ✅

```bash
composer lint        # Formatea el código con Pint
composer types:check # Análisis estático con Larastan
composer test        # Pint + Larastan + pruebas con Pest
```

## Autora ✒️

**Dayan Betancourt**, *Desarrolladora Web*: [@delfinbeta](https://github.com/delfinbeta)

## Licencia 📄

Este proyecto es de uso personal. El código base parte del [Laravel Livewire Starter Kit](https://github.com/laravel/livewire-starter-kit), que tiene licencia MIT.
