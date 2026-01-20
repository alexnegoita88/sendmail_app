# SendMail App

O aplicație Laravel pentru trimiterea de emailuri în masă cu funcții avansate de urmărire și management.

## Caracteristici

### ✅ Autentificare și Securitate
- Sistem de autentificare Laravel Breeze
- Doi utilizatori pre-configurați prin seeder
- Comandă Artisan pentru adăugarea de utilizatori noi
- Redirecționare automată la login la accesarea rădăcinii

### ✅ Încărcare Fișiere
- Suport pentru fișiere Excel (.xlsx), JSON și CSV
- Validare automată a adreselor de email
- Statistici detaliate despre fișierele încărcate
- Stocare sigură a fișierelor

### ✅ Șabloane Email
- Editor HTML pentru crearea de emailuri personalizate
- Sistem de preview înainte de trimitere
- Suport pentru linkuri și formatare avansată
- Salvare și reutilizare șabloane

### ✅ Management Campanii
- Rate limiting: 50 emailuri pe minut
- Progres în timp real al trimiterii
- Monitorizare status emailuri (trimise, eșuate, deschise)
- Sistem de cozi pentru trimitere eficientă

### ✅ Urmărire Avansată
- Sistem de tracking pixel pentru emailuri deschise
- Analiză detaliată: IP, browser, dispozitiv, locație
- Statistici în timp real
- Dashboard intuitiv

### ✅ Design Modern
- Interfață construită cu Tailwind CSS
- Livewire pentru interactivitate
- Responsive design
- Dashboard elegant cu statistici rapide

## Instalare

### Cerințe Sistem
- PHP 8.2+
- MySQL 5.7+
- Composer
- Node.js 16+

### Pași Instalare

1. **Clonare proiect**
   ```bash
   git clone <repository-url>
   cd sendmail_app
   ```

2. **Instalare dependențe**
   ```bash
   composer install
   npm install
   ```

3. **Configurare mediu**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurare baza de date**
   - Editați `.env` cu datele MySQL:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sendmail_app
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Configurare SMTP (Office 365)**
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.office365.com
   MAIL_PORT=587
   MAIL_USERNAME=noreply@radacini-grup.ro
   MAIL_PASSWORD=D5;XTycxCne]uU($@B2}JR
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@radacini-grup.ro
   MAIL_FROM_NAME="SendMail App"
   ```

6. **Rulare migrări și seeder**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Compilare frontend**
   ```bash
   npm run build
   ```

8. **Pornire aplicație**
   - Folosiți Laragon: http://sendmail_app.test

## Utilizare

### Autentificare
- Accesați http://sendmail_app.test
- Veți fi redirecționat automat la pagina de login
- Utilizatori pre-configurați:
  - admin@radacini-grup.ro / admin123
  - manager@radacini-grup.ro / manager123

### Adăugare Utilizatori
```bash
php artisan user:create email@exemplu.com parola
```

### Flux Lucru

1. **Încărcare Listă Emailuri**
   - Accesați secțiunea "Încărcare Fișiere"
   - Încărcați fișier Excel, JSON sau CSV
   - Sistemul validează automat adresele

2. **Creare Șablon Email**
   - Accesați secțiunea "Șabloane Email"
   - Creați email HTML cu editorul integrat
   - Preview înainte de salvare

3. **Lansare Campanie**
   - Selectați șablon și listă de emailuri
   - Sistemul trimite cu rate limiting (50/min)
   - Urmăriți progresul în timp real

4. **Analiză Rezultate**
   - Accesați secțiunea "Statistici"
   - Vizualizați emailuri deschise, click-uri
   - Analizați performanța campaniilor

## Structură Proiect

```
app/
├── Console/Commands/     # Comenzi Artisan
├── Http/Livewire/        # Componente Livewire
├── Models/              # Modele Eloquent
└── Services/            # Servicii logice

database/
├── migrations/          # Migrări baze de date
├── seeders/            # Seeding date
└── factories/          # Factory modele

resources/
├── views/              # Blade templates
│   ├── livewire/       # View-uri Livewire
│   └── layouts/        # Layout-uri
└── js/                 # JavaScript/Livewire

routes/
├── web.php             # Rute web
└── api.php             # Rute API
```

## Modele Bază de Date

- **users** - Utilizatori aplicație
- **email_lists** - Fișiere încărcate cu emailuri
- **email_templates** - Șabloane email HTML
- **campaigns** - Campanii de trimitere
- **email_recipients** - Destinatari individuali
- **email_trackings** - Date urmărire emailuri
- **rate_limit_logs** - Log-uri limitare rată

## Comenzi Utile

```bash
# Creare utilizator
php artisan user:create email@exemplu.com parola

# Rulare migrări
php artisan migrate

# Seeding date
php artisan db:seed

# Curățare cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Dezvoltare

### Adăugare Funcționalități
1. Creați modele noi în `app/Models/`
2. Adăugați migrări în `database/migrations/`
3. Creați componente Livewire în `app/Http/Livewire/`
4. Actualizați view-urile în `resources/views/`

### Testare
```bash
# Testare unitări
php artisan test

# Testare feature
php artisan test --filter=Feature
```

## Securitate

- Validare input utilizator
- Sanitizare emailuri
- Rate limiting SMTP
- Autentificare obligatorie
- Acces controlat resurse

## Contribuție

1. Fork proiect
2. Creează branch feature
3. Commit modificări
4. Push la branch
5. Creează Pull Request

## Licență

Acest proiect este licențiat sub licența MIT.

## Support

Pentru suport tehnic sau întrebări, contactați echipa de dezvoltare.
