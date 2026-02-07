# Setup Horizon FĂRĂ SUDO - Ghid Complet

> [!IMPORTANT]
> Acest ghid este pentru configurarea Horizon **fără nicio comandă sudo sau root**. Totul va rula sub utilizatorul tău normal.

---

## 🎯 Ce Vom Face

1. Verificăm dacă `supervisord` este disponibil
2. Creăm configurație privată pentru Supervisor
3. Pornim Supervisor sub utilizatorul tău
4. Configurăm auto-start cu cron
5. Testăm că totul funcționează

**Zero comenzi sudo! Zero acces root!**

---

## Pasul 1: Conectare la VPS

```bash
ssh utilizator@paradise-agency.ro
```

**Înlocuiește `utilizator` cu username-ul tău real.**

---

## Pasul 2: Navigare la Aplicație

```bash
cd /calea/catre/mailflow
```

**Exemple comune:**
- `cd /home/utilizator/mailflow`
- `cd /home/utilizator/public_html/mailflow`
- `cd /var/www/mailflow`

**Pentru a găsi calea exactă:**
```bash
pwd
# Afișează calea curentă
```

---

## Pasul 3: Verificare Supervisor

Verifică dacă `supervisord` este disponibil pe server:

```bash
which supervisord
```

**Posibile răspunsuri:**

### ✅ Răspuns: `/usr/bin/supervisord` sau `/usr/local/bin/supervisord`
**Perfect! Supervisor este instalat. Continuă la Pasul 4.**

### ❌ Răspuns: nimic (gol)
**Supervisor nu este instalat la nivel de sistem.**

**Soluții:**

#### Opțiunea 1: Instalare Supervisor Local (fără sudo)

```bash
# Instalează Supervisor în directorul utilizatorului
pip install --user supervisor

# SAU dacă nu ai pip:
python3 -m pip install --user supervisor

# Verifică instalarea
~/.local/bin/supervisord --version
```

Dacă funcționează, folosește `~/.local/bin/supervisord` în loc de `/usr/bin/supervisord` în comenzile următoare.

#### Opțiunea 2: Contactează Hosting Provider

Dacă instalarea locală nu funcționează, contactează hosting provider-ul și cere-le să instaleze Supervisor la nivel de sistem (ei pot face asta fără să-ți dea acces root).

#### Opțiunea 3: Alternativă - Folosește `screen` sau `tmux`

Dacă Supervisor nu poate fi instalat deloc, poți folosi `screen` pentru a rula Horizon în background (mai puțin robust, dar funcționează):

```bash
# Instalează screen (dacă nu e instalat, cere hosting provider-ului)
# Nu necesită sudo pentru utilizare

# Pornește o sesiune screen
screen -S horizon

# În screen, pornește Horizon
cd /calea/catre/mailflow
php artisan horizon

# Detașează screen: apasă Ctrl+A apoi D
# Horizon va continua să ruleze în background

# Pentru a te reconecta:
screen -r horizon
```

**Pentru restul ghidului, presupunem că ai Supervisor disponibil.**

---

## Pasul 4: Verificare Redis

Redis **TREBUIE** să ruleze. Verifică:

```bash
redis-cli ping
```

**Răspuns așteptat:** `PONG`

**Dacă nu funcționează:**
- Contactează hosting provider-ul să pornească Redis
- SAU verifică dacă Redis rulează pe alt port/host

---

## Pasul 5: Verificare `.env`

```bash
cd /calea/catre/mailflow
cat .env | grep -E "QUEUE_CONNECTION|REDIS|APP_URL"
```

**Trebuie să vezi:**
```env
APP_URL=https://mailflow.paradise-agency.ro
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

**Dacă lipsește ceva, editează `.env`:**

```bash
nano .env
```

**Modifică/adaugă:**
```env
APP_URL=https://mailflow.paradise-agency.ro
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=1
```

Salvează: `Ctrl+O`, `Enter`, `Ctrl+X`

---

## Pasul 6: Clear Cache Laravel

```bash
cd /calea/catre/mailflow

# Determină ce comandă PHP folosești
php -v
# SAU
ea-php82 -v
```

**Dacă `php -v` funcționează, folosește `php`:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

**Dacă ești pe cPanel și ai `ea-php82`:**
```bash
ea-php82 artisan config:clear
ea-php82 artisan cache:clear
ea-php82 artisan config:cache
```

**Pentru restul ghidului, înlocuiește `php` cu `ea-php82` dacă e cazul.**

---

## Pasul 7: Creare Configurație Supervisor Privat

> [!NOTE]
> Fișierul `user_supervisor.conf` este specific serverului și este ignorat de Git (vezi `.gitignore`). Acest lucru previne conflictele când tragi actualizări de pe GitHub.

### Opțiunea A: Copiază din Template (Recomandat)

Dacă ai un fișier template în repository:

```bash
cd /calea/catre/mailflow
cp user_supervisor.conf.example user_supervisor.conf
nano user_supervisor.conf
```

### Opțiunea B: Creează Manual

```bash
cd /calea/catre/mailflow
nano user_supervisor.conf
```

**Adaugă următorul conținut:**

```ini
[supervisord]
logfile=%(here)s/storage/logs/supervisord.log
pidfile=%(here)s/storage/supervisord.pid
nodaemon=false

[program:horizon]
command=php %(here)s/artisan horizon
autostart=true
autorestart=true
user=utilizatorul_tau
numprocs=1
redirect_stderr=true
stdout_logfile=%(here)s/storage/logs/horizon.log
stopwaitsecs=3600
```

**Modificări necesare:**

1. **Înlocuiește `utilizatorul_tau`** cu username-ul tău real:
   ```bash
   whoami
   # Afișează username-ul tău
   ```

2. **Dacă folosești `ea-php82`**, schimbă linia `command`:
   ```ini
   command=ea-php82 %(here)s/artisan horizon
   ```

**Exemplu complet pentru cPanel:**
```ini
[supervisord]
logfile=%(here)s/storage/logs/supervisord.log
pidfile=%(here)s/storage/supervisord.pid
nodaemon=false

[program:horizon]
command=ea-php82 %(here)s/artisan horizon
autostart=true
autorestart=true
user=deployer
numprocs=1
redirect_stderr=true
stdout_logfile=%(here)s/storage/logs/horizon.log
stopwaitsecs=3600
```

Salvează: `Ctrl+O`, `Enter`, `Ctrl+X`

---

## Pasul 8: Pornire Supervisor

```bash
cd /calea/catre/mailflow

# Pornește Supervisor
/usr/bin/supervisord -c user_supervisor.conf
```

**Dacă primești eroare "command not found":**

Găsește calea către `supervisord`:
```bash
which supervisord
```

Folosește calea returnată, de exemplu:
```bash
/usr/local/bin/supervisord -c user_supervisor.conf
# SAU
~/.local/bin/supervisord -c user_supervisor.conf
```

**Dacă primești eroare "already running":**
```bash
# Oprește instanța existentă
kill $(cat storage/supervisord.pid)

# Așteaptă 2 secunde
sleep 2

# Pornește din nou
/usr/bin/supervisord -c user_supervisor.conf
```

---

## Pasul 9: Verificare

### Verifică procesul Supervisor

```bash
ps aux | grep supervisord
```

**Ar trebui să vezi ceva similar cu:**
```
utilizator  12345  ... /usr/bin/supervisord -c user_supervisor.conf
```

### Verifică procesul Horizon

```bash
ps aux | grep "artisan horizon"
```

**Ar trebui să vezi:**
```
utilizator  12346  ... php artisan horizon
```

### Verifică log-urile

```bash
cd /calea/catre/mailflow

# Log Supervisor
tail -f storage/logs/supervisord.log

# Apasă Ctrl+C pentru a opri

# Log Horizon
tail -f storage/logs/horizon.log
```

**Nu ar trebui să vezi erori!**

---

## Pasul 10: Auto-Start cu Cron

Pentru ca Supervisor să pornească automat după restart server:

```bash
crontab -e
```

**Dacă te întreabă ce editor să folosești, alege `nano` (de obicei opțiunea 1).**

**Adaugă la sfârșitul fișierului:**

```cron
* * * * * cd /calea/catre/mailflow && pgrep -f "supervisord -c user_supervisor.conf" || /usr/bin/supervisord -c user_supervisor.conf
```

**Înlocuiește:**
- `/calea/catre/mailflow` cu calea reală
- `/usr/bin/supervisord` cu calea reală dacă e diferită

**Exemplu complet:**
```cron
* * * * * cd /home/deployer/mailflow && pgrep -f "supervisord -c user_supervisor.conf" || /usr/bin/supervisord -c user_supervisor.conf
```

**Ce face această linie:**
- Verifică la fiecare minut dacă Supervisor rulează
- Dacă NU rulează → îl pornește automat
- Dacă DA rulează → nu face nimic

Salvează: `Ctrl+O`, `Enter`, `Ctrl+X`

---

## ✅ Testare

### Test 1: Verifică Horizon Dashboard

Accesează în browser:
```
https://mailflow.paradise-agency.ro/horizon
```

**Ar trebui să vezi:**
- Status: **Active** ✅
- Supervisors: **1** ✅
- Processes: **1** ✅

### Test 2: Verifică Procesele

```bash
ps aux | grep horizon | grep -v grep
```

**Ar trebui să vezi UN proces activ.**

### Test 3: Test Queue Manual

```bash
cd /calea/catre/mailflow
php artisan tinker
```

În Tinker:
```php
dispatch(function() {
    \Log::info('Test job from queue!');
});
exit
```

Verifică log-urile:
```bash
tail -f storage/logs/laravel.log
```

**Ar trebui să vezi mesajul "Test job from queue!"**

### Test 4: Testează Campania

1. Accesează aplicația în browser
2. Mergi la campania ta
3. Apasă butonul **"Pornește"**
4. Verifică în Horizon Dashboard - ar trebui să vezi job-uri în procesare
5. Verifică log-urile:
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## 🔧 Comenzi Utile (FĂRĂ SUDO)

### Verificare Status

```bash
# Verifică Supervisor
ps aux | grep supervisord | grep -v grep

# Verifică Horizon
ps aux | grep horizon | grep -v grep

# Verifică PID
cat storage/supervisord.pid
```

### Restart Horizon (după modificări cod)

```bash
cd /calea/catre/mailflow
php artisan horizon:terminate
```

**Supervisor îl va reporni automat în 2-3 secunde.**

### Oprire Supervisor

```bash
cd /calea/catre/mailflow
kill $(cat storage/supervisord.pid)
```

### Pornire Supervisor

```bash
cd /calea/catre/mailflow
/usr/bin/supervisord -c user_supervisor.conf
```

### Verificare Log-uri

```bash
cd /calea/catre/mailflow

# Horizon logs (live)
tail -f storage/logs/horizon.log

# Laravel logs (live)
tail -f storage/logs/laravel.log

# Supervisor logs (live)
tail -f storage/logs/supervisord.log

# Ultimele 50 linii
tail -n 50 storage/logs/horizon.log
```

---

## 🚨 Troubleshooting

### Problema: "supervisord: command not found"

**Soluție 1: Găsește calea corectă**
```bash
which supervisord
find /usr -name supervisord 2>/dev/null
```

Folosește calea găsită în comenzi.

**Soluție 2: Instalează local**
```bash
pip install --user supervisor
~/.local/bin/supervisord -c user_supervisor.conf
```

**Soluție 3: Contactează hosting provider**

### Problema: Emailurile nu se trimit

**Verifică în ordine:**

1. **Redis rulează?**
   ```bash
   redis-cli ping
   ```

2. **Horizon rulează?**
   ```bash
   ps aux | grep horizon
   ```

3. **Queue connection în .env?**
   ```bash
   cat .env | grep QUEUE_CONNECTION
   # Trebuie: QUEUE_CONNECTION=redis
   ```

4. **Configurație SMTP corectă?**
   ```bash
   cat .env | grep MAIL_
   ```

5. **Job-uri failed?**
   ```bash
   php artisan queue:failed
   ```

6. **Log-uri:**
   ```bash
   tail -f storage/logs/horizon.log
   tail -f storage/logs/laravel.log
   ```

### Problema: "Error: Another program is already listening"

Supervisor deja rulează. Oprește-l mai întâi:

```bash
kill $(cat storage/supervisord.pid)
sleep 2
/usr/bin/supervisord -c user_supervisor.conf
```

### Problema: Horizon nu pornește

**Test manual pentru a vedea erorile:**
```bash
cd /calea/catre/mailflow
php artisan horizon
```

Verifică ce erori apar și rezolvă-le.

---

## 📋 Checklist Final

- [ ] Redis rulează (`redis-cli ping` → PONG)
- [ ] `.env` are `QUEUE_CONNECTION=redis`
- [ ] `.env` are `APP_URL=https://mailflow.paradise-agency.ro`
- [ ] Cache Laravel recreat (`php artisan config:cache`)
- [ ] `user_supervisor.conf` creat cu configurația corectă
- [ ] Supervisor pornit (`ps aux | grep supervisord`)
- [ ] Horizon rulează (`ps aux | grep horizon`)
- [ ] Cron job configurat pentru auto-start
- [ ] Horizon Dashboard accesibil și arată "Active"
- [ ] Test campanie funcționează

---

## 🎉 Gata!

Acum ai:
- ✅ Supervisor privat care rulează sub utilizatorul tău
- ✅ Zero comenzi sudo sau root
- ✅ Horizon activ și gata să proceseze emailuri
- ✅ Auto-start configurat cu cron
- ✅ Complet izolat și independent

Când apeși **"Pornește"** pe o campanie, emailurile vor fi trimise! 🚀

---

## 📝 Script de Deploy Viitor

Salvează acest script pentru deploy-uri viitoare:

```bash
#!/bin/bash
# deploy.sh

cd /calea/catre/mailflow

echo "🚀 Starting deployment..."

# Pull code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear cache
php artisan config:clear
php artisan cache:clear

# Recreate cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart Horizon
php artisan horizon:terminate

echo "✅ Deployment complete!"
echo "⏳ Waiting for Horizon to restart..."
sleep 3

# Verify Horizon is running
if ps aux | grep -q "artisan horizon"; then
    echo "✅ Horizon is running"
else
    echo "⚠️ WARNING: Horizon is not running!"
fi
```

Fă-l executabil:
```bash
chmod +x deploy.sh
```

Folosește-l:
```bash
./deploy.sh
```
