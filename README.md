# 🌸 Blossom

Blossom; görevleri, yaklaşan ödemeleri, alışkanlık serilerini ve odak oturumlarını tek bir arayüzden yönetmek için geliştirilmiş modern bir kişisel planlama uygulamasıdır.
Daha önce gelştirmiş olduğum TO-DO uygulamsı basit bir arayüze sahipken üzerinde biraz geliştirme yapılmış ve " Blossom " ortaya çıkmıştır

Proje, **Vue 3 + Vite** tabanlı bağımsız bir istemci ile **Laravel 13** tabanlı REST API'den oluşur. Görev ve ödeme kayıtları backend üzerinden veritabanında saklanırken alışkanlık ve odak verileri tarayıcıda yerel olarak tutulur.

## Özellikler

- Görev ekleme, tamamlama ve silme
- Görevleri iş, ev ve okul kategorilerine ayırma
- Düşük, orta ve yüksek öncelik seviyeleri
- Son tarih ve durum bazlı görev takibi
- Bekleyen ve tamamlanan kayıtları filtreleme
- Tutar ve son ödeme tarihiyle ödeme/fatura takibi
- Yaklaşan ödemeler ve toplam bekleyen tutar özeti
- Kişiselleştirilebilir alışkanlık serileri
- 25, 45 ve 60 dakikalık odak zamanlayıcısı
- Açık/koyu tema desteği
- Mobil cihazlara uyumlu, animasyonlu arayüz

## Kullanılan Teknolojiler

### Frontend

- Vue 3
- Vite 8
- Axios
- OGL
- JavaScript

### Backend

- PHP 8.3+
- Laravel 13
- SQLite (varsayılan)
- Pest
- Laravel Pint ve Larastan

## Proje Yapısı

```text
TO-DO/
├── app-frontend/              # Vue 3 istemci uygulaması
│   ├── public/
│   └── src/
│       ├── App.vue            # Ana uygulama ekranı ve iş mantığı
│       ├── AnimatedList.vue   # Animasyonlu kayıt listesi
│       ├── Dock.vue           # Alt uygulama menüsü
│       ├── PillNav.vue        # Sekme ve kategori navigasyonu
│       ├── SoftAurora.vue     # Arka plan efekti
│       ├── services/api.js    # Axios API istemcisi
│       └── main.js            # Vue başlangıç noktası
├── app-backend/               # Laravel REST API
│   ├── app/
│   │   ├── Http/Controllers/Api/TodoController.php
│   │   └── Models/Todo.php
│   ├── database/migrations/   # Todo tablo şeması
│   └── routes/api.php         # API rotaları
└── README.md
```

## Gereksinimler

Projeyi yerel ortamda çalıştırmak için aşağıdakiler kurulu olmalıdır:

- PHP `8.3` veya üzeri
- Composer
- Node.js `22.18.x` veya `24.12+`
- npm
- PHP SQLite eklentisi (`pdo_sqlite`)

## Kurulum

### 1. Projeyi klonlayın

```bash
git clone <repository-url>
cd TO-DO
```

### 2. Backend'i hazırlayın

```powershell
cd app-backend
composer install
Copy-Item .env.example .env
php artisan key:generate
if (-not (Test-Path database/database.sqlite)) { New-Item -ItemType File -Path database/database.sqlite }
php artisan migrate
php artisan serve
```

macOS veya Linux kullanıyorsanız `.env` ve SQLite dosyasını şu komutlarla oluşturabilirsiniz:

```bash
cp .env.example .env
touch database/database.sqlite
```

Backend varsayılan olarak `http://127.0.0.1:8000` adresinde çalışır.

### 3. Frontend'i hazırlayın

Yeni bir terminal açın:

```powershell
cd app-frontend
npm install
Copy-Item .env.example .env
```

`app-frontend/.env` dosyasına çalışan backend adresini ekleyin:

```env
VITE_API_URL=http://127.0.0.1:8000/api
```

Ardından geliştirme sunucusunu başlatın:

```bash
npm run dev
```

## Ortam Değişkenleri

Backend ayarları `app-backend/.env` üzerinden yönetilir. Proje varsayılan olarak MySQL kullanır. SQLite veya PostgreSQL kullanmak için Laravel'in `DB_*` değişkenlerini ilgili bağlantı bilgileriyle güncelleyin ve migrasyonları yeniden çalıştırın.

## Kullanılabilir Komutlar

### Frontend

```bash
npm run dev       # Geliştirme sunucusunu başlatır
npm run build     # Üretim derlemesi oluşturur
npm run preview   # Üretim derlemesini yerel olarak önizler
```

### Backend

```bash
php artisan serve       # API sunucusunu başlatır
php artisan migrate     # Veritabanı migrasyonlarını çalıştırır
php artisan test        # Backend testlerini çalıştırır
composer run lint       # PHP kodunu Pint ile biçimlendirir
composer run types:check # Statik analizi çalıştırır
```

## API

Tüm endpoint'ler `/api` önekini kullanır.

| Metot | Endpoint | Açıklama |
| --- | --- | --- |
| `GET` | `/api/todos` | Tüm görev ve ödemeleri listeler |
| `POST` | `/api/todos` | Yeni görev veya ödeme oluşturur |
| `PUT` | `/api/todos/{id}` | Bir kaydı günceller veya tamamlandı durumunu değiştirir |
| `DELETE` | `/api/todos/{id}` | Bir kaydı siler |

### Görev oluşturma örneği

```json
{
  "title": "Haftalık raporu hazırla",
  "type": "task",
  "category": "work",
  "priority": "high",
  "due_date": "2026-07-25"
}
```

### Ödeme oluşturma örneği

```json
{
  "title": "İnternet faturası",
  "type": "payment",
  "amount": 649.90,
  "due_date": "2026-07-28"
}
```

## Üretim Derlemesi

Frontend'i derlemek için:

```bash
cd app-frontend
npm run build
```

Üretilen statik dosyalar `app-frontend/dist` dizinine yazılır. Backend'i üretime almadan önce Laravel'in standart önbellek ve güvenlik ayarlarını uygulayın; `APP_DEBUG=false` kullanın ve web sunucusunun belge kökünü `app-backend/public` olarak yapılandırın.

## Sorun Giderme

- **Frontend verileri yükleyemiyorsa:** Laravel sunucusunun çalıştığını ve `VITE_API_URL` değerinin `/api` ile bittiğini kontrol edin.
- **SQLite hatası alıyorsanız:** `app-backend/database/database.sqlite` dosyasının varlığını ve PHP `pdo_sqlite` eklentisinin etkin olduğunu doğrulayın.
- **Değişken güncellenmesine rağmen bağlantı değişmiyorsa:** `.env` değişikliğinden sonra Vite geliştirme sunucusunu yeniden başlatın.
- **Migrasyon hatası alıyorsanız:** Backend dizininde `php artisan migrate:status` komutuyla mevcut durumu kontrol edin.

<img width="1024" height="1536" alt="blossom png" src="https://github.com/user-attachments/assets/b5bfa4d7-1167-40f9-b31f-3c999fdcc705" />


