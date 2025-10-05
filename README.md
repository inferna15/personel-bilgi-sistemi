# Personel Bilgi Sistemi (PBS)

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vue.js&logoColor=4FC08D)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

**Personel Bilgi Sistemi (PBS)**, Laravel ve Vue.js kullanılarak geliştirilmiş, modern ve kapsamlı bir insan kaynakları (İK) yönetimi uygulamasıdır. Bu platform, şirket içi personel, birim, izin, maaş ve duyuru gibi temel İK süreçlerini dijitalleştirmeyi ve merkezileştirmeyi hedefler.

Proje, rol bazlı erişim kontrolü sunarak farklı yetkilere sahip kullanıcı (Admin, Yönetici, Personel) profillerini destekler ve harici servislerle RESTful API üzerinden entegre olarak veri alışverişi yapabilir.

## Ekran Görüntüleri

<table>
  <tr>
    <td align="center"><strong>Gösterge Paneli</strong></td>
    <td align="center"><strong>Personel Listesi</strong></td>
  </tr>
  <tr>
    <td><img width="1999" height="1333" alt="image2" src="https://github.com/user-attachments/assets/3f8138b6-5e41-41d2-b597-34c2545feb65" /></td>
    <td><img width="1999" height="1333" alt="image5" src="https://github.com/user-attachments/assets/fbf75f83-1fc9-4c2e-a831-dfb157bc3aa4" /></td>
  </tr>
  <tr>
    <td align="center"><strong>Kart ve Bakiye Yönetimi</strong></td>
    <td align="center"><strong>Duyuru Yönetimi</strong></td>
  </tr>
  <tr>
    <td><img width="1999" height="1333" alt="image10" src="https://github.com/user-attachments/assets/4db3899c-f3ae-42d9-b919-6e2f3099321c" /></td>
    <td><img width="1999" height="1333" alt="image18" src="https://github.com/user-attachments/assets/b8fb8b84-c4d4-4208-a0d3-a8177d3047f9" /></td>
  </tr>
    <tr>
    <td align="center" colspan="2"><strong>Personel Detay Ekranı</strong></td>
  </tr>
    <tr>
    <td colspan="2"><img width="1999" height="1333" alt="image7" src="https://github.com/user-attachments/assets/a81f367b-5d3e-4812-b95a-44b5bb6ab51f" /></td>
  </tr>
</table>

## Özellikler

### Rol Bazlı Erişim Kontrolü
Sistem, üç farklı kullanıcı rolü ile güvenli ve yetkilendirilmiş bir yapı sunar:
-   **Admin:** Tüm sistem üzerinde tam yetkiye sahiptir.
-   **Yönetici (Manager):** Kendi birimindeki personelleri yönetebilir ve izin taleplerini onaylayabilir.
-   **Personel (Employee):** Kendi kişisel bilgilerini görüntüleyebilir ve izin talebinde bulunabilir.

### Modüller
-   **Personel Yönetimi:** Personel ekleme, düzenleme, silme ve detaylı arama.
-   **Birim Yönetimi:** Şirket departmanlarını oluşturma ve yönetme.
-   **İzin Yönetimi:** İzin talebi oluşturma ve onaylama iş akışı.
-   **Maaş Yönetimi:** Maaş ve bordro bilgilerini görüntüleme.
-   **Duyuru Sistemi:** Şirket geneli duyurular yayınlama.
-   **Harici Entegrasyon:** RESTful API ile Araç ve Kart bilgilerini anlık olarak çekme.

## Kullanılan Teknolojiler

-   **Backend:** PHP (8.x), Laravel (10.x)
-   **Frontend:** Vue.js (3.x), Vite
-   **Veritabanı:** MySQL / PostgreSQL (yapılandırılabilir)
-   **API:** RESTful

## Kullanılan Önemli Paketler

Projenin backend'i, işlevselliği artıran ve geliştirmeyi hızlandıran bazı temel Laravel paketlerinden yararlanmaktadır. Kendi `composer.json` dosyanıza bakarak bu listeyi düzenleyebilirsiniz:

-   **`laravel/sanctum`**: Vue.js gibi SPA (Single Page Application) ön yüzleri için basit ve güvenli API kimlik doğrulaması (authentication) sağlar.
-   **`spatie/laravel-permission`**: "Admin", "Yönetici" ve "Personel" gibi rolleri ve bu rollerin yetkilerini veritabanı üzerinden dinamik olarak yönetmeyi kolaylaştırır.

## Lisans

Bu proje MIT Lisansı altında lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakınız.
