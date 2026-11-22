<?php
$page_title = 'Kullanım Koşulları | GH Çalıştay';
$page_description = 'GH Çalıştay platformunun üyelik ve kullanım koşulları.';
include __DIR__ . '/_head.php';
?>
<section class="ghd-legal-section">
    <article class="ghd-legal-card">
        <p class="ghd-eyebrow"><span></span> Yasal bilgilendirme</p>
        <h1>Kullanım Koşulları</h1>
        <p class="ghd-legal-meta">Son güncelleme: 22 Kasım 2026 · Global Hukukçular Derneği</p>

        <h2>1. Platformun amacı</h2>
        <p>GH Çalıştay, Global Hukukçular Derneği'nin üyelerine yönelik görev, proje ve etkinlik çalışmalarını ortak bir panoda yürütmek için sunulan üyelere özel bir çalışma alanıdır. Platform, hukuki danışmanlık veya avukatlık hizmeti sunmaz; panolarda yer alan içerikler dernek çalışmalarına ilişkin organizasyon bilgileridir.</p>

        <h2>2. Üyelik ve erişim</h2>
        <p>Platforma erişim, başvurunun otomatik olarak onaylanmasına bağlıdır; dernek yönetimi belgeleri sonradan inceleyebilir ve üyeliği iptal edebilir. Başvuruda doğru, güncel ve size ait bilgiler vermekle yükümlüsünüz. Hesap bilgilerinizi üçüncü kişilerle paylaşmamalı; yetkisiz erişim şüphesini derhal yönetime bildirmelisiniz. Dernek, başvuruyu gerekçe göstermeden reddetme veya üyeliği askıya alma hakkını saklı tutar.</p>

        <h2>3. Kabul edilebilir kullanım</h2>
        <ul>
            <li>Platformu yalnızca dernek çalışmaları ve üyelik kapsamındaki amaçlarla kullanın.</li>
            <li>Hukuka aykırı, hak ihlali içeren, tehdit, hakaret veya ayrımcılık barındıran içerik paylaşmayın.</li>
            <li>Başkalarının kişisel verilerini, belgelerini veya gizli bilgilerini izinsiz yüklemeyin.</li>
            <li>Platformun güvenliğini, erişim kontrollerini veya diğer kullanıcıların deneyimini bozacak girişimlerde bulunmayın.</li>
            <li>Otomatik araçlarla izinsiz veri toplama, kazıma veya yük bindirme yapmayın.</li>
        </ul>

        <h2>4. İçerik ve fikri mülkiyet</h2>
        <p>Platformun adı, logosu, tasarımı ve yazılım altyapısı Global Hukukçular Derneği'ne veya ilgili hak sahiplerine aittir. Açık kaynak bileşenler kendi lisansları kapsamında kullanılır. Kullanıcıların panolara eklediği içerikler kullanıcıya aittir; kullanıcı, bu içerikleri platformun işletilmesi amacıyla derneğe kullanma izni vermiş sayılır.</p>

        <h2>5. Hesap güvenliği ve veri koruma</h2>
        <p>Şifrenizi güçlü tutmak ve hesabınızı korumak sizin sorumluluğunuzdadır. Kişisel verilerin işlenmesine ilişkin ayrıntılar <a href="<?= $this->url->href('MembershipController', 'kvkk', array('plugin' => 'GhdMembership')) ?>">KVKK Aydınlatma Metni</a>'nde açıklanmıştır. e-Devlet belgeleri yalnızca yetkili yöneticiler tarafından görüntülenebilir.</p>

        <h2>6. Hizmetin sürekliliği</h2>
        <p>Dernek, platformun kesintisiz veya hatasız çalışacağını garanti etmez. Bakım, güvenlik veya teknik nedenlerle erişim geçici olarak kısıtlanabilir. Hizmetin tamamen veya kısmen sonlandırılması halinde üyelere makul süre içinde bilgi verilir.</p>

        <h2>7. Sorumluluğun sınırlandırılması</h2>
        <p>Platformda paylaşılan bilgilerin doğruluğu, güncelliği veya belirli bir amaca uygunluğu konusunda azami özen gösterilir; ancak dernek, kullanıcıların platforma eklediği içeriklerden veya platformun kullanımından doğan dolaylı zararlardan sorumlu tutulamaz. Hukuki bir uyuşmazlık için profesyonel danışmanlık almanız gerekir.</p>

        <h2>8. Değişiklikler</h2>
        <p>Bu koşullar zaman zaman güncellenebilir. Önemli değişiklikler platformda yayımlanır. Değişiklikten sonra platformu kullanmaya devam etmeniz, güncel koşulları kabul ettiğiniz anlamına gelir.</p>

        <h2>9. İletişim</h2>
        <p>Sorularınız ve talepleriniz için <a href="mailto:info@globalhukukcular.com">info@globalhukukcular.com</a> adresine e-posta gönderebilirsiniz.</p>
    </article>
</section>
<?php include __DIR__ . '/_foot.php'; ?>
