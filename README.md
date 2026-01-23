# 📧 MailFlow - Platforma Profesională de Email Marketing

**MailFlow** este o soluție robustă și performantă de email marketing construită pe **Laravel 12**, concepută pentru a gestiona campanii masive cu viteză, securitate și monitorizare în timp real.

---

## ✨ Funcționalități Principale

### 📊 Monitorizare Avansată cu Laravel Horizon
Aplicația utilizează **Laravel Horizon** pentru gestionarea cozilor de mesaje (Queues) prin Redis.
- **Tablou de bord în timp real**: Vizualizează joburile pending, procesate sau eșuate.
- **Statistici de performanță**: Monitorizează throughput-ul și timpul de procesare.
- **Retry instant**: Gestionarea rapidă a mesajelor care au întâmpinat erori SMTP.

### 🔐 Securitate și Protecție
- **2FA prin Email (Obligatoriu)**: La fiecare autentificare, sistemul trimite un cod unic de 6 cifre pe emailul utilizatorului. Codul este valid timp de 10 minute.
- **Acces Protejat**: Dashboard-ul principal și interfața Horizon sunt accesibile doar utilizatorilor autentificați (middleware `auth`).

### 📈 Analytics & Tracking Pixel
- **Tracking Deschideri**: Fiecare email trimis conține un pixel invizibil care înregistrează momentul deschiderii, IP-ul, dispozitivul și browserul destinatarului.
- **Analytics în timp real**: Grafice și statistici detaliate pentru fiecare campanie în parte.

### 🚀 Performanță și Scalabilitate
- **Rate Limiting Dinamic**: Configurare ușoară a limitei de trimitere pe minut direct din `.env`.
- **Procesare Asincronă**: Trimiterea mailurilor nu blochează interfața, totul se întâmplă în fundal.

---

## 🛠️ Cerințe Sistem (VPS)

Pentru a rula MailFlow în condiții optime pe un server de producție, sunt necesare următoarele:
- **PHP 8.2+** (recomandat `ea-php82` pe sistemele cPanel/CentOS)
- **Extensii PHP**: `pcntl`, `redis`, `pdo_mysql`, `mbstring`, `xml`
- **Redis Server**: Obligatoriu pentru gestionarea cozilor de mesaje.
- **Supervisor**: Pentru monitorizarea și repornirea automată a proceselor de fundal.
- **Composer & NPM**: Pentru gestionarea dependințelor.

---

## 🚀 Ghid de Deployment pe VPS (Pas cu Pas)

Atunci când tragi modificări noi de pe GitHub pe serverul de producție, urmează această secvență exactă de comenzi:

### 1. Actualizare Cod
```bash
git pull origin [nume_branch]
```

### 2. Actualizare Dependențe
Dacă au fost adăugate pachete noi:
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Migrarea Bazei de Date
Dacă au apărut modificări în structura tabelelor:
```bash
ea-php82 artisan migrate --force
```

### 4. Optimizare și Cache
**CRITIC:** Laravel stochează configurațiile în cache. Rulează aceste comenzi pentru a te asigura că noile setări din `.env` sunt preluate:
```bash
ea-php82 artisan config:cache
ea-php82 artisan route:cache
ea-php82 artisan view:cache
```

### 5. Restart Horizon (Motorul de trimitere)
Horizon rulează procese persistente în memorie. Pentru a încărca noul cod, trebuie să îl oprești:
```bash
ea-php82 artisan horizon:terminate
```
*Dacă ai Supervisor configurat corect, acesta va reporni procesul instantaneu.*

---

## ⚙️ Configurare Supervisor (Instanță Privată)

Dacă nu ai acces `sudo` pe server, am configurat o instanță privată de Supervisor care rulează sub utilizatorul tău.

### Configurația `user_supervisor.conf`:
Asigură-te că fișierul conține următoarele (ajustează căile dacă este necesar):
```ini
[supervisord]
logfile=%(here)s/storage/logs/supervisord.log
pidfile=%(here)s/storage/supervisord.pid
nodaemon=false

[program:horizon]
command=ea-php82 %(here)s/artisan horizon
autostart=true
autorestart=true
user=[utilizatorul_tau]
redirect_stderr=true
stdout_logfile=%(here)s/storage/logs/horizon.log
stopwaitsecs=3600
```

### Comenzi de Gestiune:
- **Pornire Supervisor**: `/usr/bin/supervisord -c user_supervisor.conf`
- **Oprire Supervisor**: `kill $(cat storage/supervisord.pid)`
- **Verificare Status (Manual)**: `ps aux | grep horizon`

---

## ⏰ Configurare Cron Jobs

Pentru ca sistemul să funcționeze automat (trimitere campanii programate + menținere Supervisor), adaugă următoarele linii în Terminal (`crontab -e`):

```bash
# Asigură-te că Supervisor rulează mereu
* * * * * cd /calea/catre/proiect && pgrep -f "supervisord -c user_supervisor.conf" || /usr/bin/supervisord -c user_supervisor.conf

# Rulează scheduler-ul Laravel la fiecare minut
* * * * * cd /calea/catre/proiect && ea-php82 artisan schedule:run >> /dev/null 2>&1
```

---

## 📝 Variabile Importante în `.env`

- `APP_URL`: Trebuie să fie adresa reală (ex: `https://email-app.subaru.ro`). **Esențial pentru pixelul de tracking!**
- `EMAIL_RATE_LIMIT`: Numărul maxim de mailuri permise pe minut (implicit `50`).
- `QUEUE_CONNECTION=redis`: Trebuie să rămână pe `redis` pentru a activa Horizon.
- `REDIS_CLIENT=predis`: Folosit pentru compatibilitate maximă pe diverse sisteme.

---

## 💬 Mentenanță și Depanare

- **Log-uri Aplicație**: `storage/logs/laravel.log` (erori de cod/mailuri)
- **Log-uri Horizon**: `storage/logs/horizon.log` (erori de procesare/cozi)
- **Log-uri Supervisor**: `storage/logs/supervisord.log` (erori de pornire sistem)

**Sfat:** Dacă mailurile nu pleacă, primul pas este să verifici dacă Redis rulează și dacă procesele `horizon` apar în `ps aux`.

---

**🎉 MailFlow este acum documentat și pregătit pentru producție!**
Spor la campanii! 📧✨
