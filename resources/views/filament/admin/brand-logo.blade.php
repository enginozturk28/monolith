{{-- Tipografik brand logo.

     Filament admin paneli kendi pre-build CSS'ini kullanır ve bizim Vite build'imizdeki
     Tailwind utility class'ları burada çalışmaz. Bu yüzden tamamen inline style
     kullanıyoruz — Filament build'ine bağımlı değil, garantili render.

     Tasarım ekibi gerçek logoyu hazırlayıp panelden yüklediğinde, bu view
     otomatik olarak o görselle değiştirilir. --}}
<div style="display:flex;align-items:center;gap:0.625rem;line-height:1.2;">
    <div style="
        flex-shrink:0;
        width:2.25rem;
        height:2.25rem;
        background-color:#0B1F3A;
        border-radius:0.375rem;
        display:flex;
        align-items:center;
        justify-content:center;
        box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);
    ">
        <span style="
            color:#F5F1EA;
            font-family:'Cormorant Garamond','Lora',Georgia,ui-serif,serif;
            font-size:1.25rem;
            font-weight:600;
            letter-spacing:-0.02em;
            line-height:1;
        ">L</span>
    </div>
    <div style="display:flex;flex-direction:column;gap:1px;">
        <span style="
            font-family:'Cormorant Garamond','Lora',Georgia,ui-serif,serif;
            font-size:0.9375rem;
            font-weight:600;
            letter-spacing:-0.01em;
            color:inherit;
            line-height:1.1;
        ">Loğoğlu Hukuk Bürosu</span>
        <span style="
            font-family:'Inter',ui-sans-serif,system-ui,sans-serif;
            font-size:0.625rem;
            font-weight:500;
            text-transform:uppercase;
            letter-spacing:0.14em;
            color:#8C8B86;
            line-height:1;
        ">Yönetim Paneli</span>
    </div>
</div>
