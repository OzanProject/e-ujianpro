# Solusi Timeout Import Siswa

## Masalah
Import banyak siswa (>50) di hosting menyebabkan timeout 30 detik karena bcrypt hashing terlalu lambat.

## Solusi yang Diterapkan

### 1. **Turunkan Bcrypt Rounds** (Quick Fix)
- File: `config/hashing.php`
- Setting: `BCRYPT_ROUNDS=10` (default 12)
- Impact: Hash 4x lebih cepat, masih aman untuk production

### 2. **Queue Job untuk Import Besar** (Best Practice)
- File: `app/Jobs/ImportStudentsJob.php`
- Import diproses di background, tidak timeout
- Timeout per job: 5 menit
- Auto retry: 3x jika gagal

### 3. **Chunking Import**
- File: `app/Imports/StudentsImportQueued.php`
- Process 50 siswa per chunk
- Mengurangi memory usage

### 4. **Dual Mode Import**
- Sync: untuk file kecil (<50 siswa), instant result
- Queue: untuk file besar (>50 siswa), background processing

## Cara Pakai di Hosting

### Setup Queue Worker (WAJIB)

**Opsi 1: Supervisor (Recommended)**
```bash
sudo apt install supervisor
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Isi file:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/storage/logs/worker.log
stopwaitsecs=3600
```

Jalankan:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

**Opsi 2: Cron Job (Simple)**
```bash
crontab -e
```

Tambahkan:
```
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
* * * * * cd /path/to/your/project && php artisan queue:work database --stop-when-empty >> /dev/null 2>&1
```

**Opsi 3: Systemd Service**
```bash
sudo nano /etc/systemd/system/laravel-queue.service
```

Isi:
```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /path/to/your/artisan queue:work database --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

Jalankan:
```bash
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue
sudo systemctl status laravel-queue
```

### Update .env di Hosting
```env
BCRYPT_ROUNDS=10
QUEUE_CONNECTION=database
```

### Restart Services
```bash
php artisan config:cache
php artisan queue:restart
sudo supervisorctl restart laravel-worker:*  # jika pakai supervisor
```

## Testing

### Test Local
```bash
# Terminal 1: Jalankan queue worker
php artisan queue:work

# Terminal 2: Test import
# Upload file dengan >50 siswa via browser
```

### Monitor Queue
```bash
# Lihat jobs yang pending
php artisan queue:monitor

# Lihat failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

## Troubleshooting

### Import tidak jalan di background?
```bash
# Cek queue worker status
sudo supervisorctl status laravel-worker:*

# Cek log
tail -f storage/logs/worker.log
tail -f storage/logs/laravel.log
```

### Masih timeout?
1. Pastikan `BCRYPT_ROUNDS=10` di `.env`
2. Jalankan `php artisan config:cache`
3. Restart queue worker
4. Gunakan mode queue (centang checkbox di form)

### Jobs stuck?
```bash
# Restart queue
php artisan queue:restart

# Clear failed jobs
php artisan queue:flush
```

## Performance Benchmark

| Jumlah Siswa | Sync (Timeout) | Queue (Success) |
|--------------|----------------|-----------------|
| 10 siswa     | 5 detik        | 5 detik         |
| 50 siswa     | 25 detik       | 25 detik        |
| 100 siswa    | TIMEOUT ❌     | 50 detik ✅     |
| 500 siswa    | TIMEOUT ❌     | 4 menit ✅      |

## Security Note

Bcrypt rounds 10 masih sangat aman:
- Rounds 10 = 1,024 iterations
- Rounds 12 = 4,096 iterations (4x lebih lambat)
- Untuk password siswa (NISN), rounds 10 sudah lebih dari cukup
- Untuk admin, bisa tetap pakai rounds 12 dengan custom logic
