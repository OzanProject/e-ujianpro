#!/bin/bash

# ============================================
# Script Setup Queue Worker untuk Hosting
# ============================================

echo "=========================================="
echo "  Setup Queue Worker - E-Ujian System"
echo "=========================================="
echo ""

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Deteksi path project
PROJECT_PATH=$(pwd)
echo -e "${YELLOW}Project Path:${NC} $PROJECT_PATH"
echo ""

# 1. Update .env
echo -e "${YELLOW}[1/5]${NC} Updating .env configuration..."
if grep -q "BCRYPT_ROUNDS=" .env; then
    sed -i 's/BCRYPT_ROUNDS=.*/BCRYPT_ROUNDS=10/' .env
    echo -e "${GREEN}✓${NC} BCRYPT_ROUNDS updated to 10"
else
    echo "BCRYPT_ROUNDS=10" >> .env
    echo -e "${GREEN}✓${NC} BCRYPT_ROUNDS added to .env"
fi

if grep -q "QUEUE_CONNECTION=" .env; then
    sed -i 's/QUEUE_CONNECTION=.*/QUEUE_CONNECTION=database/' .env
    echo -e "${GREEN}✓${NC} QUEUE_CONNECTION updated to database"
else
    echo "QUEUE_CONNECTION=database" >> .env
    echo -e "${GREEN}✓${NC} QUEUE_CONNECTION added to .env"
fi
echo ""

# 2. Clear cache
echo -e "${YELLOW}[2/5]${NC} Clearing cache..."
php artisan config:cache
php artisan cache:clear
echo -e "${GREEN}✓${NC} Cache cleared"
echo ""

# 3. Run migrations
echo -e "${YELLOW}[3/5]${NC} Running migrations..."
php artisan migrate --force
echo -e "${GREEN}✓${NC} Migrations completed"
echo ""

# 4. Setup Supervisor
echo -e "${YELLOW}[4/5]${NC} Setting up Supervisor..."
SUPERVISOR_CONF="/etc/supervisor/conf.d/laravel-worker.conf"

if [ -f "$SUPERVISOR_CONF" ]; then
    echo -e "${YELLOW}⚠${NC} Supervisor config already exists"
else
    echo "Creating supervisor config..."
    sudo tee $SUPERVISOR_CONF > /dev/null <<EOF
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $PROJECT_PATH/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=$PROJECT_PATH/storage/logs/worker.log
stopwaitsecs=3600
EOF
    
    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start laravel-worker:*
    echo -e "${GREEN}✓${NC} Supervisor configured and started"
fi
echo ""

# 5. Check status
echo -e "${YELLOW}[5/5]${NC} Checking queue worker status..."
sudo supervisorctl status laravel-worker:*
echo ""

echo "=========================================="
echo -e "${GREEN}✓ Setup Complete!${NC}"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Test import dengan file kecil (<50 siswa) tanpa centang queue"
echo "2. Test import dengan file besar (>50 siswa) dengan centang queue"
echo "3. Monitor queue: php artisan queue:monitor"
echo "4. Check logs: tail -f storage/logs/worker.log"
echo ""
echo "Troubleshooting:"
echo "- Restart queue: sudo supervisorctl restart laravel-worker:*"
echo "- View logs: tail -f storage/logs/laravel.log"
echo "- Failed jobs: php artisan queue:failed"
echo ""
