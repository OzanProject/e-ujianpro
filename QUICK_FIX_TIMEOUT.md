# 🚀 Quick Fix: Timeout Import Siswa

## ⚡ Solusi Cepat (5 Menit)

### Di Hosting:

```bash
# 1. Update .env
nano .env
# Ubah: BCRYPT_ROUNDS=10
# Ubah: QUEUE_CONNECTION=database

# 2. Clear cache
php artisan config:cache

# 3. Setup queue worker (pilih salah satu)

# Opsi A: Supervisor (Recommended)
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
# Copy config dari SOLUSI_TIMEOUT_IMPORT.md
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*

# Opsi B: Cron (Simple)
crontab -e
# Tambahkan:
* * * * * cd /path/to/project && php artisan queue:work database --stop-when-empty >> /dev/null 2>&1
```

### Di Local (Testing):

```bash
# Windows
test-queue-local.bat

# Linux/Mac
php artisan queue:work
```

## 📊 Cara Pakai

1. **Import Kecil (<50 siswa)**: Jangan centang queue, langsung import
2. **Import Besar (>50 siswa)**: Centang "Gunakan Queue", tunggu beberapa menit

## 🔍 Monitoring

```bash
# Lihat status queue
php artisan queue:monitor

# Lihat failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Restart queue worker
sudo supervisorctl restart laravel-worker:*  # hosting
# atau Ctrl+C lalu jalankan ulang (local)
```

## ✅ Hasil

| Sebelum | Sesudah |
|---------|---------|
| ❌ Timeout 30 detik | ✅ No timeout |
| ❌ Max 10-15 siswa | ✅ Unlimited siswa |
| ❌ Hash lambat (12 rounds) | ✅ Hash cepat (10 rounds) |
| ❌ Sync blocking | ✅ Background processing |

## 📚 Dokumentasi Lengkap

Lihat: `SOLUSI_TIMEOUT_IMPORT.md`

## 🆘 Troubleshooting

**Import tidak jalan?**
```bash
# Cek queue worker
sudo supervisorctl status laravel-worker:*

# Cek log
tail -f storage/logs/worker.log
tail -f storage/logs/laravel.log
```

**Masih timeout?**
1. Pastikan `BCRYPT_ROUNDS=10` di `.env`
2. Jalankan `php artisan config:cache`
3. Restart queue worker
4. Centang checkbox "Gunakan Queue"

**Jobs stuck?**
```bash
php artisan queue:restart
php artisan queue:flush
```
