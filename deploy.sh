#!/usr/bin/env bash
#
# Despliegue de DelfinBeta en el servidor.
#
# Lo ejecuta GitHub Actions por SSH en cada push a `main`, pero también
# se puede correr a mano desde el servidor:
#
#   ./deploy.sh            # despliega origin/main
#   ./deploy.sh develop    # despliega otra rama
#
# Los assets (public/build) los compila GitHub Actions y los sube con rsync;
# si se ejecuta a mano, pasar BUILD_ASSETS=1 para compilarlos aquí.
#
# Todo va dentro de main() para que bash lea el archivo completo antes de
# ejecutarlo: `git reset` puede reescribir este mismo script a mitad del deploy.

set -euo pipefail

main() {
    local branch="${1:-main}"
    local php="${PHP_BIN:-$(command -v php8.5 || command -v php8.4 || command -v php)}"
    local composer=("$php" "${COMPOSER_BIN:-$(command -v composer)}")

    cd "$(dirname "$(readlink -f "$0")")"

    echo "▶ Desplegando rama ${branch} en $(pwd) con $("$php" -r 'echo PHP_VERSION;')"

    "$php" artisan down --retry=30 || true
    # shellcheck disable=SC2064 # expandir $php ahora: al salir la variable local ya no existe
    trap "'$php' artisan up || true" EXIT

    echo "▶ Actualizando código"
    git fetch --prune origin "$branch"
    git reset --hard "origin/${branch}"

    echo "▶ Dependencias PHP"
    "${composer[@]}" install --no-dev --no-interaction --prefer-dist --optimize-autoloader

    if [[ "${BUILD_ASSETS:-0}" == "1" ]]; then
        echo "▶ Compilando assets"
        npm ci
        npm run build
    fi

    echo "▶ Migraciones"
    "$php" artisan migrate --force

    echo "▶ Cachés"
    "$php" artisan optimize:clear
    "$php" artisan optimize
    "$php" artisan storage:link 2>/dev/null || true

    echo "▶ Reiniciando workers de cola"
    "$php" artisan queue:restart

    echo "✔ Deploy completado: $(git rev-parse --short HEAD)"
}

main "$@"
exit
