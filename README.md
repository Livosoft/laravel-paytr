# LARAVEL PAYTR ENTEGRASYONU
Controller - Route - Ödeme Ekranı / Tüm Sayfalar Paylaşılmış olup, benim yaptığım projede bakiye sistemi olduğu için sepete 1 ürün eklenmiş gösteriyorum ve paytr dönüşü işlemim üyeye yüklediği bakiyeyi aktarmak oluyor.
-------------------------------------------------------------------------------------------------------
- Bakiye.blade.php sayfasından yüklenecek miktarı yazıp devam ediyorum
- Açılan sayfayı usercontroller'da odeme_ekraniq alanına gönderiyorum burada eğer magaza bilgileri ve diğer bilgilerde bi sorun yoksa
- odeme.blade.php sayfasına gönderiyorum bu sayfada paytrnin ödeme ekranı mevcut ödemeyi tamamladıktan sonra
- paytr tarafında geri dönüş url'ini siteadresi.com/odeme-basarili routuna gönderiyorum burada işlemi onaylıyorum sistem üzerinde yapmak istediğim işlemi yapıyorum ve
- odeme_ekraniq da belirttiğim "$merchant_ok_url" route'ya yönlendirmesini gerçekleştirip işlemi başarılı bir şekilde bitiriyorum.
