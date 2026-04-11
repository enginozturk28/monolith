#!/usr/bin/env bash
#
# ssr-daemon.sh — Inertia SSR servisini background'da çalıştırır.
#
# Cloudpanel ortamında supervisor (systemd, supervisord) erişimi olmadığında
# kullanılan basit daemon helper'ı. PID dosyası ile tek instance garantisi
# sağlar; mevcut process çalışıyorsa yeniden başlatmaz.
#
# Komutlar:
#   ./scripts/ssr-daemon.sh start    — başlat (zaten çalışıyorsa atlanır)
#   ./scripts/ssr-daemon.sh stop     — durdur
#   ./scripts/ssr-daemon.sh restart  — yeniden başlat
#   ./scripts/ssr-daemon.sh status   — durum kontrolü
#
# Production ortamında bu script'in yerine systemd service veya supervisor
# kullanılması önerilir (`docs/ssr-deploy.md` içinde örnek config var).
#
# Kullanım:
#   php artisan inertia:start-ssr → 0.0.0.0:13714 üzerinde Node servisi açar
#   Inertia adapter bu porta HTTP POST yapar (otomatik), sayfayı SSR olarak
#   render eder ve HTML döner.

set -euo pipefail

PROJECT_DIR="/home/eloboostop/htdocs/eloboostop.com"
PID_FILE="${PROJECT_DIR}/storage/framework/ssr.pid"
LOG_FILE="${PROJECT_DIR}/storage/logs/ssr.log"

cd "${PROJECT_DIR}"

is_running() {
    if [[ -f "${PID_FILE}" ]]; then
        local pid
        pid=$(cat "${PID_FILE}")
        if kill -0 "${pid}" 2>/dev/null; then
            return 0
        fi
    fi
    return 1
}

start() {
    if is_running; then
        echo "SSR daemon zaten çalışıyor (PID: $(cat "${PID_FILE}"))"
        return 0
    fi

    echo "SSR daemon başlatılıyor..."
    nohup php artisan inertia:start-ssr >> "${LOG_FILE}" 2>&1 &
    local pid=$!

    # PHP artisan command bir parent process oluşturur, gerçek Node child'i
    # ayrı PID alır. Yine de parent PID'i kaydet.
    echo "${pid}" > "${PID_FILE}"

    # 1 saniye bekle ve hayatta mı kontrol et
    sleep 1
    if kill -0 "${pid}" 2>/dev/null; then
        echo "SSR daemon başlatıldı (PID: ${pid})"
        echo "Log: ${LOG_FILE}"
    else
        echo "SSR daemon başlatılamadı. Log: ${LOG_FILE}"
        rm -f "${PID_FILE}"
        return 1
    fi
}

stop() {
    if ! is_running; then
        echo "SSR daemon çalışmıyor"
        return 0
    fi

    local pid
    pid=$(cat "${PID_FILE}")
    echo "SSR daemon durduruluyor (PID: ${pid})..."

    # Önce parent php process'i öldür
    kill "${pid}" 2>/dev/null || true

    # Ayrıca bağlı tüm Node child'ları öldür
    pkill -f "inertia:start-ssr" 2>/dev/null || true
    pkill -f "bootstrap/ssr/ssr.js" 2>/dev/null || true

    rm -f "${PID_FILE}"
    sleep 0.5
    echo "SSR daemon durduruldu"
}

status() {
    if is_running; then
        local pid
        pid=$(cat "${PID_FILE}")
        echo "SSR daemon çalışıyor (PID: ${pid})"
        ps -fp "${pid}" 2>/dev/null || true
    else
        echo "SSR daemon çalışmıyor"
        return 1
    fi
}

case "${1:-status}" in
    start)
        start
        ;;
    stop)
        stop
        ;;
    restart)
        stop
        start
        ;;
    status)
        status
        ;;
    *)
        echo "Kullanım: $0 {start|stop|restart|status}"
        exit 1
        ;;
esac
