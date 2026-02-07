# Monitorizare Supervisor și Horizon - Ghid Complet

> [!IMPORTANT]
> Acest ghid prezintă toate metodele disponibile pentru a verifica statusul Supervisor, Horizon, workers și jobs, **în afară de Horizon Dashboard**.

---

## 📊 Metode de Monitorizare

1. **Comenzi Shell** (SSH pe server)
2. **Artisan Commands** (Laravel CLI)
3. **Verificare Programatică** (în cod Laravel)
4. **API Endpoints** (pentru monitoring extern)
5. **Log Files** (verificare manuală)

---

## 1. Comenzi Shell (SSH)

### Verificare Supervisor Activ

```bash
# Metodă 1: Verifică procesul
ps aux | grep supervisord | grep -v grep

# Metodă 2: Verifică PID file
cat storage/supervisord.pid && echo "Supervisor PID exists"

# Metodă 3: Verifică dacă procesul cu PID-ul respectiv rulează
kill -0 $(cat storage/supervisord.pid) 2>/dev/null && echo "Supervisor is running" || echo "Supervisor is NOT running"
```

### Verificare Horizon Activ

```bash
# Metodă 1: Verifică procesul
ps aux | grep "artisan horizon" | grep -v grep

# Metodă 2: Numără procesele Horizon
ps aux | grep "artisan horizon" | grep -v grep | wc -l
# Output: 1 (dacă rulează un worker)

# Metodă 3: Verifică cu pgrep
pgrep -f "artisan horizon" && echo "Horizon is running" || echo "Horizon is NOT running"
```

### Verificare Număr Workers

```bash
# Numără procesele Horizon active
ps aux | grep "artisan horizon" | grep -v grep | wc -l

# Detalii despre fiecare worker
ps aux | grep "artisan horizon" | grep -v grep
```

### Verificare Jobs în Procesare

```bash
# Conectează-te la Redis și verifică queue-urile
redis-cli

# În Redis CLI:
LLEN queues:default
# Returnează numărul de jobs în așteptare

# Verifică toate key-urile Horizon
KEYS *horizon*

# Ieși din Redis
exit
```

### Script Complet de Verificare

```bash
#!/bin/bash
# check_horizon.sh

echo "=== Horizon Status Check ==="
echo ""

# Check Supervisor
if pgrep -f "supervisord -c user_supervisor.conf" > /dev/null; then
    echo "✅ Supervisor: RUNNING"
    SUPERVISOR_PID=$(pgrep -f "supervisord -c user_supervisor.conf")
    echo "   PID: $SUPERVISOR_PID"
else
    echo "❌ Supervisor: NOT RUNNING"
fi

echo ""

# Check Horizon
if pgrep -f "artisan horizon" > /dev/null; then
    echo "✅ Horizon: RUNNING"
    HORIZON_PID=$(pgrep -f "artisan horizon")
    echo "   PID: $HORIZON_PID"
    
    # Count workers
    WORKER_COUNT=$(ps aux | grep "artisan horizon" | grep -v grep | wc -l)
    echo "   Workers: $WORKER_COUNT"
else
    echo "❌ Horizon: NOT RUNNING"
fi

echo ""

# Check Redis
if redis-cli ping > /dev/null 2>&1; then
    echo "✅ Redis: RUNNING"
    
    # Check queue length
    QUEUE_LENGTH=$(redis-cli LLEN queues:default 2>/dev/null || echo "0")
    echo "   Jobs in queue: $QUEUE_LENGTH"
else
    echo "❌ Redis: NOT RUNNING"
fi

echo ""
echo "=== End of Status Check ==="
```

Fă scriptul executabil și folosește-l:
```bash
chmod +x check_horizon.sh
./check_horizon.sh
```

---

## 2. Artisan Commands (Laravel CLI)

### Verificare Status Horizon

```bash
cd /calea/catre/mailflow

# Verifică dacă Horizon rulează (indirect)
php artisan horizon:status
# Dacă nu există această comandă, creează-o (vezi mai jos)

# Listează toate job-urile failed
php artisan queue:failed

# Monitorizează queue-ul în timp real
php artisan queue:monitor redis:default --max=100

# Verifică configurația Horizon
php artisan config:show horizon
```

### Comenzi Utile pentru Queue

```bash
# Listează job-urile failed
php artisan queue:failed

# Retry toate job-urile failed
php artisan queue:retry all

# Retry un job specific
php artisan queue:retry JOB_ID

# Șterge toate job-urile failed
php artisan queue:flush

# Clear queue-ul (șterge job-urile pending)
php artisan queue:clear redis

# Statistici despre queue
php artisan queue:work --once --verbose
```

---

## 3. Verificare Programatică (în Laravel)

### Creează o Comandă Artisan Personalizată

```bash
php artisan make:command CheckHorizonStatus
```

Editează `app/Console/Commands/CheckHorizonStatus.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CheckHorizonStatus extends Command
{
    protected $signature = 'horizon:check';
    protected $description = 'Check Horizon and Supervisor status';

    public function handle()
    {
        $this->info('=== Horizon Status Check ===');
        $this->newLine();

        // Check if Supervisor is running
        $supervisorRunning = $this->checkSupervisor();
        $this->displayStatus('Supervisor', $supervisorRunning);

        // Check if Horizon is running
        $horizonRunning = $this->checkHorizon();
        $this->displayStatus('Horizon', $horizonRunning);

        // Check Redis
        $redisRunning = $this->checkRedis();
        $this->displayStatus('Redis', $redisRunning);

        if ($redisRunning) {
            // Check queue length
            $queueLength = $this->getQueueLength();
            $this->info("Jobs in queue: {$queueLength}");
        }

        // Check workers count
        $workersCount = $this->getWorkersCount();
        $this->info("Active workers: {$workersCount}");

        $this->newLine();
        
        return $supervisorRunning && $horizonRunning ? 0 : 1;
    }

    protected function checkSupervisor(): bool
    {
        $pidFile = storage_path('supervisord.pid');
        
        if (!file_exists($pidFile)) {
            return false;
        }

        $pid = trim(file_get_contents($pidFile));
        
        // Check if process is running
        exec("kill -0 {$pid} 2>/dev/null", $output, $returnCode);
        
        return $returnCode === 0;
    }

    protected function checkHorizon(): bool
    {
        exec("pgrep -f 'artisan horizon'", $output, $returnCode);
        
        return $returnCode === 0 && !empty($output);
    }

    protected function checkRedis(): bool
    {
        try {
            Redis::ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getQueueLength(): int
    {
        try {
            return (int) Redis::llen('queues:default');
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getWorkersCount(): int
    {
        exec("ps aux | grep 'artisan horizon' | grep -v grep | wc -l", $output);
        
        return (int) ($output[0] ?? 0);
    }

    protected function displayStatus(string $service, bool $running): void
    {
        $status = $running ? '<info>✅ RUNNING</info>' : '<error>❌ NOT RUNNING</error>';
        $this->line("{$service}: {$status}");
    }
}
```

**Folosește comanda:**
```bash
php artisan horizon:check
```

### Verificare în Controller sau Service

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class HorizonMonitor
{
    public function isHorizonRunning(): bool
    {
        exec("pgrep -f 'artisan horizon'", $output, $returnCode);
        
        return $returnCode === 0 && !empty($output);
    }

    public function isSupervisorRunning(): bool
    {
        $pidFile = storage_path('supervisord.pid');
        
        if (!file_exists($pidFile)) {
            return false;
        }

        $pid = trim(file_get_contents($pidFile));
        exec("kill -0 {$pid} 2>/dev/null", $output, $returnCode);
        
        return $returnCode === 0;
    }

    public function getWorkersCount(): int
    {
        exec("ps aux | grep 'artisan horizon' | grep -v grep | wc -l", $output);
        
        return (int) ($output[0] ?? 0);
    }

    public function getQueueLength(string $queue = 'default'): int
    {
        try {
            return (int) Redis::llen("queues:{$queue}");
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getProcessingJobsCount(): int
    {
        try {
            // Horizon stochează job-urile în procesare în Redis
            $keys = Redis::keys('*:current');
            return count($keys);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getStatus(): array
    {
        return [
            'supervisor_running' => $this->isSupervisorRunning(),
            'horizon_running' => $this->isHorizonRunning(),
            'workers_count' => $this->getWorkersCount(),
            'queue_length' => $this->getQueueLength(),
            'processing_jobs' => $this->getProcessingJobsCount(),
        ];
    }
}
```

**Folosește în controller:**

```php
use App\Services\HorizonMonitor;

class DashboardController extends Controller
{
    public function status(HorizonMonitor $monitor)
    {
        $status = $monitor->getStatus();
        
        return view('dashboard.status', compact('status'));
    }
}
```

---

## 4. API Endpoints pentru Monitoring

### Creează un Controller pentru Status

```bash
php artisan make:controller Api/MonitoringController
```

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HorizonMonitor;
use Illuminate\Http\JsonResponse;

class MonitoringController extends Controller
{
    public function __construct(
        private HorizonMonitor $monitor
    ) {}

    public function status(): JsonResponse
    {
        $status = $this->monitor->getStatus();
        
        return response()->json([
            'status' => 'ok',
            'data' => $status,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function health(): JsonResponse
    {
        $isHealthy = $this->monitor->isHorizonRunning() 
                  && $this->monitor->isSupervisorRunning();
        
        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'horizon' => $this->monitor->isHorizonRunning(),
            'supervisor' => $this->monitor->isSupervisorRunning(),
        ], $isHealthy ? 200 : 503);
    }
}
```

**Adaugă rutele în `routes/api.php`:**

```php
use App\Http\Controllers\Api\MonitoringController;

Route::get('/monitoring/status', [MonitoringController::class, 'status']);
Route::get('/monitoring/health', [MonitoringController::class, 'health']);
```

**Testează endpoint-urile:**

```bash
# Status complet
curl https://mailflow.paradise-agency.ro/api/monitoring/status

# Health check simplu
curl https://mailflow.paradise-agency.ro/api/monitoring/health
```

---

## 5. Verificare prin Log Files

### Verificare Logs

```bash
cd /calea/catre/mailflow

# Horizon logs (ultimele 50 linii)
tail -n 50 storage/logs/horizon.log

# Horizon logs (live monitoring)
tail -f storage/logs/horizon.log

# Supervisor logs
tail -n 50 storage/logs/supervisord.log

# Laravel logs
tail -n 50 storage/logs/laravel.log

# Caută erori în logs
grep -i error storage/logs/horizon.log
grep -i exception storage/logs/laravel.log

# Verifică când a fost ultima modificare (ultima activitate)
ls -lh storage/logs/horizon.log
stat storage/logs/horizon.log
```

### Script pentru Verificare Logs

```bash
#!/bin/bash
# check_logs.sh

echo "=== Recent Horizon Activity ==="
echo ""

# Check if log file exists
if [ -f "storage/logs/horizon.log" ]; then
    echo "Last 10 lines of Horizon log:"
    tail -n 10 storage/logs/horizon.log
    
    echo ""
    echo "Last modified:"
    stat -f "%Sm" storage/logs/horizon.log 2>/dev/null || stat -c "%y" storage/logs/horizon.log
    
    echo ""
    echo "Errors in last 100 lines:"
    tail -n 100 storage/logs/horizon.log | grep -i error | wc -l
else
    echo "❌ Horizon log file not found!"
fi
```

---

## 6. Integrare în Aplicație (UI)

### Blade Component pentru Status

Creează `resources/views/components/horizon-status.blade.php`:

```blade
@php
    $monitor = app(\App\Services\HorizonMonitor::class);
    $status = $monitor->getStatus();
@endphp

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">System Status</h3>
    
    <div class="space-y-3">
        <!-- Supervisor Status -->
        <div class="flex items-center justify-between">
            <span class="text-gray-700">Supervisor</span>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $status['supervisor_running'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $status['supervisor_running'] ? '✅ Running' : '❌ Stopped' }}
            </span>
        </div>

        <!-- Horizon Status -->
        <div class="flex items-center justify-between">
            <span class="text-gray-700">Horizon</span>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $status['horizon_running'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $status['horizon_running'] ? '✅ Running' : '❌ Stopped' }}
            </span>
        </div>

        <!-- Workers Count -->
        <div class="flex items-center justify-between">
            <span class="text-gray-700">Active Workers</span>
            <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                {{ $status['workers_count'] }}
            </span>
        </div>

        <!-- Queue Length -->
        <div class="flex items-center justify-between">
            <span class="text-gray-700">Jobs in Queue</span>
            <span class="px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                {{ $status['queue_length'] }}
            </span>
        </div>

        <!-- Processing Jobs -->
        <div class="flex items-center justify-between">
            <span class="text-gray-700">Processing</span>
            <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                {{ $status['processing_jobs'] }}
            </span>
        </div>
    </div>

    <div class="mt-4 pt-4 border-t">
        <a href="{{ url('/horizon') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            View Horizon Dashboard →
        </a>
    </div>
</div>
```

**Folosește în view-uri:**

```blade
<x-horizon-status />
```

---

## 7. Monitoring Automat cu Alerting

### Script de Monitoring cu Email Alert

```bash
#!/bin/bash
# monitor_and_alert.sh

ALERT_EMAIL="admin@paradise-agency.ro"
APP_PATH="/calea/catre/mailflow"

cd $APP_PATH

# Check if Horizon is running
if ! pgrep -f "artisan horizon" > /dev/null; then
    # Horizon is not running - send alert
    echo "Horizon is not running on $(hostname) at $(date)" | \
        mail -s "ALERT: Horizon Down on mailflow.paradise-agency.ro" $ALERT_EMAIL
    
    # Try to restart
    /usr/bin/supervisord -c user_supervisor.conf
fi

# Check queue length
QUEUE_LENGTH=$(redis-cli LLEN queues:default 2>/dev/null || echo "0")

if [ "$QUEUE_LENGTH" -gt 1000 ]; then
    echo "Queue length is $QUEUE_LENGTH on $(hostname) at $(date)" | \
        mail -s "WARNING: High Queue Length on mailflow.paradise-agency.ro" $ALERT_EMAIL
fi
```

**Adaugă în crontab pentru verificare la fiecare 5 minute:**

```bash
crontab -e
```

```cron
*/5 * * * * /calea/catre/mailflow/monitor_and_alert.sh
```

---

## 📋 Rezumat Comenzi Rapide

### Verificări Rapide

```bash
# Supervisor running?
pgrep -f supervisord && echo "✅ Running" || echo "❌ Stopped"

# Horizon running?
pgrep -f "artisan horizon" && echo "✅ Running" || echo "❌ Stopped"

# Workers count
ps aux | grep "artisan horizon" | grep -v grep | wc -l

# Jobs in queue
redis-cli LLEN queues:default

# Redis running?
redis-cli ping
```

### One-Liner Status Check

```bash
echo "Supervisor: $(pgrep -f supervisord > /dev/null && echo '✅' || echo '❌') | Horizon: $(pgrep -f 'artisan horizon' > /dev/null && echo '✅' || echo '❌') | Workers: $(ps aux | grep 'artisan horizon' | grep -v grep | wc -l) | Queue: $(redis-cli LLEN queues:default 2>/dev/null || echo '0')"
```

---

## 🎯 Best Practices

1. **Monitoring Regular**: Verifică statusul zilnic sau configurează monitoring automat
2. **Logs Review**: Verifică log-urile periodic pentru erori
3. **Alerting**: Configurează alerte pentru când Horizon cade
4. **Health Checks**: Folosește endpoint-uri de health check pentru monitoring extern
5. **Dashboard Integration**: Integrează status-ul în dashboard-ul aplicației

---

Acum ai **toate instrumentele** necesare pentru a monitoriza Supervisor și Horizon în orice moment! 🚀
