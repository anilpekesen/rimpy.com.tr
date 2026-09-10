<?php
/**
 * Zengin metin. İçerik yönetici tarafından girildiği için işaretleme
 * korunur; ancak betik ve olay öznitelikleri temizlenir — panelde bir hesap
 * ele geçse bile ziyaretçinin tarayıcısında kod çalışamaz.
 */
$html = \App\Support\Html::sanitize((string) ($settings['body'] ?? ''));
?>

<div class="tx"><?php echo $html; ?></div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/text.blade.php ENDPATH**/ ?>