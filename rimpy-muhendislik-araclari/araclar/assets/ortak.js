const $=id=>document.getElementById(id);
const fmt=(v,d=0)=>Number(v).toLocaleString('tr-TR',{maximumFractionDigits:d,minimumFractionDigits:d});
const kpis=(id,arr)=>{$(id).innerHTML=arr.map(([v,l])=>`<div class="kpi"><b>${v}</b><span>${l}</span></div>`).join('')};
const notes=(id,arr)=>{$(id).innerHTML=arr.map(x=>`<li>${x}</li>`).join('')};
const pill=(ok,t)=>`<span class="pill ${ok?'ok':'warn'}">${t}</span>`;
const live=(ids,fn)=>{ids.forEach(i=>$(i).addEventListener('input',fn));document.querySelectorAll('form').forEach(f=>f.addEventListener('submit',e=>{e.preventDefault();fn()}));fn();};
// Metrik kaba diş: [ölçü, d mm, hatve mm, somun ağzı mm]
const MET=[["M8",8,1.25,13],["M10",10,1.5,16],["M12",12,1.75,18],["M14",14,2,21],["M16",16,2,24],["M20",20,2.5,30],["M22",22,2.5,34],["M24",24,3,36],["M27",27,3,41],["M30",30,3.5,46],["M33",33,3.5,50],["M36",36,4,55],["M39",39,4,60],["M42",42,4.5,65],["M45",45,4.5,70],["M48",48,5,75],["M52",52,5,80],["M56",56,5.5,85],["M60",60,5.5,90],["M64",64,6,95]];
const INC=[["1/2\"",.5,13],["5/8\"",.625,11],["3/4\"",.75,10],["7/8\"",.875,9],["1\"",1,8],["1-1/8\"",1.125,8],["1-1/4\"",1.25,8],["1-3/8\"",1.375,8],["1-1/2\"",1.5,8],["1-5/8\"",1.625,8],["1-3/4\"",1.75,8],["1-7/8\"",1.875,8],["2\"",2,8],["2-1/4\"",2.25,8],["2-1/2\"",2.5,8],["2-3/4\"",2.75,8],["3\"",3,8],["3-1/4\"",3.25,8],["3-1/2\"",3.5,8],["3-3/4\"",3.75,8],["4\"",4,8]];
function bolt(sys,i){ const r=(sys==='M'?MET:INC)[i];
  if(sys==='M') return {lbl:r[0],d:r[1],As:Math.PI/4*Math.pow(r[1]-0.9382*r[2],2),af:r[3]};
  return {lbl:r[0],d:r[1]*25.4,As:0.7854*Math.pow(r[1]-0.9743/r[2],2)*645.16,af:Math.round((1.5*r[1]+0.125)*25.4)}; }
function contactHTML(){return `<div class="contact" id="contact">
  <h3>Rapor ve kayıt</h3>
  <div class="row"><label for="c_ad">Ad soyad<input id="c_ad" autocomplete="name"></label><label for="c_firma">Firma<input id="c_firma" autocomplete="organization"></label></div>
  <div class="row"><label for="c_mail">E-posta<input id="c_mail" type="email" autocomplete="email"></label><label for="c_tel">Telefon<input id="c_tel" type="tel" autocomplete="tel"></label></div>
  <label for="c_not">Not (opsiyonel)<input id="c_not"></label>
  <input class="hp" id="c_web" tabindex="-1" autocomplete="off" aria-hidden="true">
  <label class="check"><input id="c_kvkk" type="checkbox"> <span>Bilgilerimin teklif ve teknik destek amacıyla işlenmesini kabul ediyorum (<a href="${KVKK_URL}" target="_blank" rel="noopener">KVKK aydınlatma metni</a>).</span></label>
  <div class="actions"><button type="button" class="btn primary" id="c_send">PDF indir ve teklif iste</button><button type="button" class="btn" id="c_pdf">Yalnızca PDF</button></div>
  <div class="status" id="c_status" role="status"></div></div>`;}
const esc=s=>String(s??'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
function reportNo(p){const d=new Date();return p+'-'+d.getFullYear().toString().slice(2)+String(d.getMonth()+1).padStart(2,'0')+String(d.getDate()).padStart(2,'0')+'-'+Math.random().toString(36).slice(2,6).toUpperCase();}
function contactVals(){return {ad:$('c_ad').value.trim(),firma:$('c_firma').value.trim(),eposta:$('c_mail').value.trim(),telefon:$('c_tel').value.trim(),not:$('c_not').value.trim()};}
function printReport(title,no,bodyHTML,c){
  let r=document.getElementById('report'); if(!r){r=document.createElement('div');r.id='report';document.body.appendChild(r);}
  const d=new Date().toLocaleString('tr-TR');
  r.innerHTML=`<div class="rh"><div><h1>${esc(title)}</h1><div>Rapor no: ${no} · ${d}</div></div><div style="text-align:right"><b>${esc(FIRMA)}</b><br>${esc(FIRMA_ILETISIM)}</div></div>`+
   (c&&(c.ad||c.firma)?`<table><tr><th>Hazırlayan</th><td>${esc(c.ad)}</td><th>Firma</th><td>${esc(c.firma)}</td></tr><tr><th>E-posta</th><td>${esc(c.eposta)}</td><th>Telefon</th><td>${esc(c.telefon)}</td></tr></table>`:'')+
   bodyHTML+`<div class="ft">Bu rapor ${esc(FIRMA)} çevrim içi hesaplama aracıyla oluşturulmuştur. Değerler bilgilendirme amaçlıdır; kritik uygulamalarda üretici verileri ve yetkili mühendis onayı esas alınmalıdır.</div>`;
  try{window.print();}catch(e){}
}
// getReport(): {title, prefix, html, data}
function wireContact(getReport){
  const st=$('c_status');
  $('c_pdf').onclick=()=>{const r=getReport(); if(!r) return; printReport(r.title,reportNo(r.prefix),r.html,contactVals()); st.textContent='Yazdırma penceresinde "PDF olarak kaydet"i seçin.';};
  $('c_send').onclick=async()=>{
    const r=getReport(); if(!r) return; const c=contactVals();
    if(!c.ad||!c.firma||!/^\S+@\S+\.\S+$/.test(c.eposta)){st.innerHTML=pill(0,'Eksik')+' Ad soyad, firma ve geçerli bir e-posta girin.';return;}
    if(!$('c_kvkk').checked){st.innerHTML=pill(0,'Onay')+' Devam etmek için KVKK onay kutusunu işaretleyin.';return;}
    const no=reportNo(r.prefix);
    if(KAYIT_URL){
      st.textContent='Kaydediliyor…';
      try{const res=await fetch(KAYIT_URL,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({rapor_no:no,arac:r.title,iletisim:c,veri:r.data,sayfa:location.href,hp:$('c_web').value})});
        if(!res.ok) throw new Error(res.status);
        st.innerHTML=pill(1,'Alındı')+` Talebiniz kaydedildi (${no}). Ekibimiz sizinle iletişime geçecek.`;}
      catch(e){st.innerHTML=pill(0,'Gönderilemedi')+` Talep iletilemedi; PDF'i ${esc(FIRMA_ILETISIM)} adresine gönderebilirsiniz.`;}
    } else st.innerHTML=pill(0,'Önizleme')+' Kayıt adresi tanımlı değil; yalnızca PDF oluşturuldu.';
    printReport(r.title,no,r.html,c);
  };
}
const tableHTML=(head,rows)=>`<table><tr>${head.map(h=>`<th>${esc(h)}</th>`).join('')}</tr>${rows.map(r=>`<tr>${r.map(c=>`<td>${esc(c)}</td>`).join('')}</tr>`).join('')}</table>`;
{const s=document.getElementById('contactSlot'); if(s) s.outerHTML=contactHTML();}

// iframe içinde gömülüyse yüksekliği üst sayfaya bildir (embed.js ile otomatik boyut)
(function(){ if(window.parent===window) return; const send=()=>parent.postMessage({rimpyArac:location.pathname,h:document.documentElement.scrollHeight},'*');
  new ResizeObserver(send).observe(document.body); window.addEventListener('load',send); })();
