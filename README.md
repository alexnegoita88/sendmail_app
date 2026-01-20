# 📧 SendMail App - Platforma Profesionistă de Email Marketing

**Bun venit!** 👋

Aceasta este o aplicație web completă pentru trimiterea de emailuri în masă, construită cu **Laravel 12** și **Livewire**. Dacă nu știi nimic despre programare sau email marketing, nu-ți face griji! Acest ghid te va ghida pas cu pas.

---

## ❓ Ce Este Această Aplicație?

**SendMail App** este ca un "Gmail pentru afaceri" - îți permite să:
- 📤 **Trimiți emailuri către mii de persoane** simultan
- 🎨 **Creezi emailuri frumoase** cu imagini și culori
- 📊 **Urmărești cine deschide emailurile** tale
- 📈 **Vezi statistici** despre succesul campaniilor
- ⚡ **Trimiți automat** fără să stai să apeși "Trimite" de mii de ori

### 🎯 Pentru Cine Este?
- **Afaceri mici** care vor să contacteze clienții
- **Magazine online** care vor să anunțe promoții
- **Profesioniști** care organizează evenimente
- **Oricine** vrea să comunice cu mulți oameni eficient

---

## 🚀 INSTALARE PAS CU PAS (Pentru Începători)

### Pasul 1: Ce Ai Nevoie?
Înainte să începi, ai nevoie de:
- **Calculator** cu Windows/Mac/Linux
- **Internet** pentru descărcări
- **Spațiu liber** pe hard disk (~2GB)

### Pasul 2: Instalează Laragon (Server Local)
Laragon este ca un "calculator magic" care rulează site-uri pe computerul tău.

1. **Descarcă Laragon** de pe: https://laragon.org/download/
2. **Instalează-l** făcând dublu-click pe fișier
3. **Pornește Laragon** și apasă **"Start All"**

### Pasul 3: Descarcă Aplicația

1. **Deschide Laragon** și navighează la folderul rădăcină
2. **Descarcă proiectul** de pe GitHub în folderul rădăcină
3. **Redenumește folderul** în `sendmail_app`

### Pasul 4: Configurare Tehnică

** Deschide Command Prompt/Terminal în folderul proiectului:**

```bash
# 1. Instalează toate componentele necesare
composer install

# 2. Instalează JavaScript și CSS
npm install

# 3. Copiază fișierul de configurare
copy .env.example .env

# 4. Generează cheie de securitate
php artisan key:generate
```

### Pasul 5: Configurează Baza de Date

1. **Deschide Laragon** → **Database** → **phpMyAdmin**
2. **Creează bază de date** numită: `sendmail_app`
3. **Editează fișierul `.env`** și schimbă:
   ```
   DB_DATABASE=sendmail_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Pasul 6: Configurează Emailul (IMPORTANT!)

**Editează fișierul `.env`** și adaugă datele tale de email:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=adresa-ta@gmail.com
MAIL_PASSWORD=parola-aplicatiei-gmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=adresa-ta@gmail.com
MAIL_FROM_NAME="Numele Companiei Tale"
```

**⚠️ IMPORTANT:** Pentru Gmail, trebuie să:
1. Activezi **"Verificarea în 2 pași"**
2. Generezi o **"Parolă pentru aplicații"**
3. Folosești acea parolă în `MAIL_PASSWORD`

### Pasul 7: Finalizează Instalarea

```bash
# Creează tabelele bazei de date
php artisan migrate:fresh --seed

# Compilează CSS și JavaScript
npm run build

# Curăță cache-ul
php artisan cache:clear
php artisan config:clear
```

### Pasul 8: PORNEȘTE APLICAȚIA! 🎉

În Laragon, apasă **"WWW"** → găsește `sendmail_app` și apasă **"WWW"**

**Sau accesează:** http://sendmail_app.test

---

## 🔐 PRIMUL LOG IN

### Utilizatori Pre-configurați:
- **Email:** admin@radacini-grup.ro
- **Parolă:** admin123

### Sau creează utilizator nou:
```bash
php artisan user:create emailul-tau@gmail.com parola-ta
```

---

## 📚 CUM SE FOLOSEȘTE APLICAȚIA (GHID COMPLET)

### PASUL 1: Dashboard-ul Principal
Când intri, vezi:
- **Statistici rapide** (număr de liste, șabloane, campanii)
- **Status sistem** (dacă totul funcționează)
- **Carduri de navigare** către diferite secțiuni

### PASUL 2: Încarcă Lista de Emailuri

1. **Apasă "Încărcare Fișiere"**
2. **Creează fișier Excel** cu coloanele:
   ```
   Nume     | Email
   Ion Pop  | ion@gmail.com
   Maria I  | maria@gmail.com
   ```
3. **Încarcă fișierul** (.xlsx, .csv, sau .json)
4. **Sistemul validează** automat emailurile
5. **Vezi statistici** despre ce s-a încărcat

### PASUL 3: Creează Șablon Email

1. **Apasă "Șabloane Email"**
2. **Apasă "Creează Șablon Nou"**
3. **Completează:**
   - **Nume:** "Newsletter Mai 2024"
   - **Subiect:** "Ofertă Specială!"
4. **Scrie conținutul** în editorul HTML:
   ```html
   <h1>Salut {name}!</h1>
   <p>Ai o reducere specială la produsele noastre!</p>
   <a href="https://siteul-tau.ro">Vezi oferta</a>
   ```
5. **Apasă "Salvează"**

### PASUL 4: Lansează Campania

1. **Apasă "Campanii"**
2. **Apasă "Creează Campanie Nouă"**
3. **Alege:**
   - **Nume:** "Campania Mai 2024"
   - **Șablon:** Alege șablonul creat
   - **Listă emailuri:** Alege lista încărcată
4. **Apasă "Creează"**
5. **Apasă "Porneste"** când ești gata

### PASUL 5: Urmărește Rezultatele

1. **Apasă "Statistici"**
2. **Vezi:**
   - Câte emailuri s-au trimis
   - Câte s-au deschis
   - De pe ce dispozitive
   - Din ce locații

---

## 🏗️ CUM FUNCȚIONEAZĂ APLICAȚIA (TEHNIC)

### Arhitectura Simplificată:

```
👤 TU (Browser)
    ↓
🌐 Laravel (Server PHP)
    ↓
🗄️ MySQL (Bază de date)
    ↓
📧 SMTP Server (Trimite emailuri)
    ↓
📨 Gmail/Outlook/etc (Inbox destinatar)
```

### Procesul de Trimitere Email:

1. **Apăși "Porneste"** → Se creează job în coadă
2. **Job-ul rulează** → Preia 50 emailuri odată
3. **Trimite emailuri** → Cu pauză între ele (rate limiting)
4. **Urmărește deschideri** → Pixel invizibil în email
5. **Salvează statistici** → În baza de date

### Securitate:
- ✅ **Autentificare obligatorie**
- ✅ **Validare emailuri**
- ✅ **Rate limiting** (50/minut)
- ✅ **Protecție CSRF**
- ✅ **Sanitizare input**

---

## 🔧 DEPANARE (Dacă Nu Funcționează)

### Problema: "Composer nu este recunoscut"
**Soluție:** Instalează Composer de pe https://getcomposer.org/

### Problema: "NPM nu este recunoscut"
**Soluție:** Instalează Node.js de pe https://nodejs.org/

### Problema: "Cannot connect to database"
**Soluție:** Verifică că ai pornit MySQL în Laragon

### Problema: "Emailurile nu se trimit"
**Soluție:**
1. Verifică datele SMTP în `.env`
2. Pentru Gmail: folosește **"App Password"** nu parola normală
3. Verifică că ai activat **"Less secure app access"**

### Problema: "403 Forbidden"
**Soluție:** Permisiuni fișiere incorecte
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

---

## 📖 GLOSAR (Termeni Tehnici Explicați)

- **SMTP:** Sistemul care trimite emailuri (ca poștașul)
- **Rate Limiting:** Limită de viteză (50 emailuri/minut)
- **Queue:** Coadă de așteptare pentru joburi
- **Tracking Pixel:** Imagine invizibilă care spune când se deschide emailul
- **Migration:** Script care creează tabele în baza de date
- **Seeder:** Date de test pentru aplicație
- **Composer:** Manager de pachete PHP (ca Play Store pentru PHP)
- **NPM:** Manager de pachete JavaScript
- **Laravel:** Framework PHP (ca șablon pentru site-uri)
- **Livewire:** Face site-ul interactiv fără JavaScript complicat

---

## 🎯 PRO TIPS

1. **Testează întotdeauna** cu emailul tău înainte de campanii mari
2. **Folosește șabloane** pentru a economisi timp
3. **Verifică statistici** regulat pentru a vedea ce funcționează
4. **Backup regulat** al bazei de date
5. **Monitorizează rate limiting** pentru a nu fi blocat de furnizor

---

## 🚨 IMPORTANT DE ȘTIUT

- **Rate Limit:** Maximum 50 emailuri pe minut
- **Format Email:** Suportă HTML complet cu imagini
- **Tracking:** Funcționează automat pentru toate emailurile
- **Securitate:** Toate parolele sunt criptate
- **Backup:** Fă backup regulat al folderului `storage/`

---

## 💬 SUPORT

Dacă ai probleme:
1. **Citește erorile** din terminal/command prompt
2. **Verifică logurile** în `storage/logs/laravel.log`
3. **Testează pașii** din secțiunea "Depanare"
4. **Contactează echipa** dacă nu rezolvi

---

**🎉 Felicitări! Acum poți trimite emailuri profesionale către mii de persoane!**

Happy emailing! 📧✨
