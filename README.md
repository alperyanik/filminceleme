Film İnceleme Sitesi - Kurulum Kılavuzu

Bu proje, PHP ve Microsoft SQL Server kullanılarak geliştirilmiş bir film inceleme web sitesidir.

Gereksinimler:
- PHP 7.4 veya üzeri
- Microsoft SQL Server 2019 veya üzeri
- PHP SQL Server sürücüsü (sqlsrv)
- Web sunucusu (Apache/Nginx)

Kurulum Adımları:

1. Veritabanı Kurulumu:
   - Microsoft SQL Server Management Studio'yu açın
   - database/movie_review_db.sql dosyasını çalıştırın
   - Veritabanı ve tablolar otomatik olarak oluşturulacaktır

2. Veritabanı Bağlantı Ayarları:
   - config/database.php dosyasını açın
   - Veritabanı bağlantı bilgilerinizi güncelleyin:
     * host: SQL Server sunucu adresi
     * db_name: movie_review_db
     * username: SQL Server kullanıcı adı
     * password: SQL Server şifresi

3. Dosya İzinleri:
   - uploads/posters/ klasörü oluşturun
   - Klasöre yazma izni verin:
     * Windows: Klasöre sağ tıklayın -> Özellikler -> Güvenlik -> Düzenle -> Ekle -> Herkes -> Tam Denetim
     * Linux: chmod 777 uploads/posters/

4. Web Sunucusu Yapılandırması:
   - Proje dosyalarını web sunucunuzun kök dizinine kopyalayın
   - Apache için .htaccess dosyası otomatik olarak yapılandırılmıştır
   - Nginx için aşağıdaki yapılandırmayı ekleyin:
     location / {
         try_files $uri $uri/ /index.php?$query_string;
     }

5. Varsayılan Admin Hesabı:
   - Kullanıcı adı: admin
   - Şifre: Admin123!
   - İlk girişten sonra şifrenizi değiştirmeniz önerilir

Güvenlik Notları:
- config/database.php dosyasındaki veritabanı bilgilerini güvenli bir şekilde saklayın
- uploads/ klasörüne doğrudan erişimi engelleyin
- Düzenli olarak yedek alın
- PHP ve SQL Server güvenlik güncellemelerini takip edin

Hata Ayıklama:
- PHP hata günlüklerini kontrol edin
- SQL Server hata günlüklerini kontrol edin
- Veritabanı bağlantısını test edin
- Dosya izinlerini kontrol edin

İletişim:
Herhangi bir sorun veya öneriniz için lütfen iletişime geçin.

Not: Bu proje akademik amaçlı geliştirilmiştir ve eğitim amaçlı kullanılmalıdır. 
