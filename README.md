# 🚀 MailFlow - Platforma Profesională de Email Marketing

**MailFlow** este o soluție robustă și performantă de email marketing construită pe **Laravel 12**, concepută pentru a gestiona campanii masive cu viteză, securitate și monitorizare în timp real.

---

## ✨ Funcționalități Principale

### 📊 Monitorizare Avanasată (Horizon)
Aplicația folosește **Laravel Horizon** pentru gestionarea cozilor de mesaje (Queues). 
- **Tablou de bord în timp real**: Vezi exact câte mailuri sunt în așteptare, câte procesează și care sunt erorile.
- **Auto-scaling**: Sistemul ajustează singur numărul de "roboței" (workeri) în funcție de volumul de mailuri.
- **Retry instant**: Poți retrimite mailurile eșuate dintr-un singur buton.

### 🔐 Securitate de Grad Bancar
- **2FA prin Email**: Autentificare obligatorie în doi pași. Primești un cod unic de 6 cifre pe email la fiecare login.
- **Protecție Dashboard**: Toate rutele sensibile (Horizon, Analytics) sunt protejate prin autentificare.

### 📈 Analytics & Tracking
- **Pixel Tracking**: Urmărire automată a deschiderilor de mail.
- **Statistici per Campanie**: Fiecare campanie este independentă. Vezi rata de deschidere, dispozitivele folosite (Mobil/Desktop) și browserele.
- **Rate Limiting Dinamic**: Configurare ușoară a numărului de mailuri trimise pe minut pentru a evita blocarea SMTP-ului.

### 📁 Management Liste & Șabloane
- **Import Inteligent**: Suport pentru Excel (.xlsx), CSV și JSON.
- **Validare Automată**: Detectează mailurile scrise greșit înainte de trimitere.
- **Editor HTML Premium**: Creează șabloane vizuale complexe cu suport pentru atașamente.

---

## 🛠️ Infrastructură Necesară (Server VPS)

Pentru a rula MailFlow în condiții optime, serverul tău trebuie să aibă:
- **PHP 8.2+** (cu extensiile `pcntl`, `redis`, `pdo_mysql`)
- **Redis Server** (obligatoriu pentru Horizon)
- **Supervisor** (pentru a ține procesele pornite în fundal)
- **Cron** (pentru programarea campaniilor)

---

## 🚀 Ghid de Deployment (Lux de amănunte)

Când tragi modificări noi de pe GitHub pe serverul de producție, urmărește exact acești pași pentru a asigura stabilitatea:

### 1. Actualizare Cod
```bash
git pull origin main
```

### 2. Actualizare Dependențe (Dacă s-au adăugat pachete noi)
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Migrarea Bazei de Date
Dacă au apărut tabele noi sau coloane noi:
```bash
ea-php82 artisan migrate --force
```

### 4. Optimizare Configurație
**FOARTE IMPORTANT:** Laravel ține configurația în cache. Dacă ai schimbat ceva în `.env` (ex: `EMAIL_RATE_LIMIT` sau `APP_URL`), trebuie să rulezi:
```bash
ea-php82 artisan config:cache
ea-php82 artisan route:cache
ea-php82 artisan view:cache
```

### 5. Restart Horizon (Motorul de trimitere)
Horizon rulează din memorie. Dacă nu îi dai restart, el va rula în continuare "codul vechi".
```bash
ea-php82 artisan horizon:terminate
```
*Supervisor va detecta oprirea și va reporni Horizon instantaneu cu noul cod.*

---

## 📦 Configurare Supervisor (Pentru Developer)

Dacă ai o instanță privată de Supervisor pe un server CentOS (fără sudo), folosește fișierul `user_supervisor.conf` creat în rădăcina proiectului:

**Pornire manuală:**
```bash
/usr/bin/supervisord -c user_supervisor.conf
```

**Verificare status:**
```bash
ps aux | grep horizon
```

---

## ⚙️ Configurare `.env` (Variabile cheie)

- `APP_URL`: Adresa completă (ex: https://mailflow.ro) - Esențială pentru tracking pixel!
- `QUEUE_CONNECTION=redis`: Trebuie să rămână pe `redis` pentru Horizon.
- `REDIS_CLIENT=predis`: Recomandat pentru compatibilitate maximă.
- `EMAIL_RATE_LIMIT=50`: Câte mailuri trimite sistemul pe minut.

---

## 💬 Suport & Mentenanță
- **Log-uri Aplicație**: `storage/logs/laravel.log`
- **Log-uri Horizon**: `storage/logs/horizon.log`
- **Log-uri Supervisor**: `storage/logs/supervisord.log`

---

**🎉 MailFlow este acum gata să ducă afacerea ta la următorul nivel!**
Happy emailing! 📧✨
