<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Yeni Yıl Tatili Duyurusu',
                'date' => '2024-12-20',
                'content' => '2024 yılının son çalışma gününü 29 Aralık olarak planladık. Yeni yıl tatili 1-3 Ocak tarihleri arasında olacaktır.'
            ],
            [
                'title' => 'Çalışan Memnuniyeti Anketi',
                'date' => '2024-11-15',
                'content' => 'Tüm çalışanlarımızın katılımını beklediğimiz memnuniyet anketi başlamıştır. Lütfen İK departmanına başvurunuz.'
            ],
            [
                'title' => 'Ofis Taşınma Duyurusu',
                'date' => '2024-10-01',
                'content' => 'Bilgi İşlem departmanı yeni binada 3. katta yer alacaktır. Taşınma işlemleri 15 Ekim tarihinde tamamlanacaktır.'
            ],
            [
                'title' => 'Güvenlik Güncellemesi',
                'date' => '2024-09-25',
                'content' => 'Tüm sistem parolalarının güncellenmesi gerekmektedir. Yeni parola politikası hakkında bilgi için İK departmanına başvurunuz.'
            ],
            [
                'title' => 'Eğitim Programı',
                'date' => '2024-09-01',
                'content' => 'Mesleki gelişim eğitim programımız başlamıştır. Katılmak isteyenler departman yöneticileri ile görüşebilir.'
            ],
            [
                'title' => 'Yaz Tatili Planlaması',
                'date' => '2024-06-01',
                'content' => 'Yaz tatili planlarınızı en geç 15 Haziran tarihine kadar departman yöneticilerinize bildirmeniz gerekmektedir.'
            ]
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }
    }
}
