/* Rimpy araçlarını iframe ile gömerken yüksekliği otomatik ayarlar.
   Kullanım (sitenizin herhangi bir sayfasında):
     <iframe class="rimpy-arac" src="/araclar/tork-hesaplayici.html" title="Cıvata Tork Hesaplayıcı" style="width:100%;border:0" loading="lazy"></iframe>
     <script src="/araclar/assets/embed.js" defer></script>
*/
(function(){
  window.addEventListener('message', function(e){
    if(!e.data || typeof e.data.h !== 'number') return;
    document.querySelectorAll('iframe.rimpy-arac').forEach(function(f){
      if(f.contentWindow === e.source) f.style.height = (e.data.h + 4) + 'px';
    });
  });
  document.querySelectorAll('iframe.rimpy-arac').forEach(function(f){ if(!f.style.height) f.style.height = '900px'; });
})();
