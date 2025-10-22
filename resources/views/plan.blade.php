<!doctype html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <title>Companythrone — Početni plan</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root{
            --ink:#111; --muted:#444; --gold:#d4af37; --gold-d:#b38e2f; --bg:#fff;
            --h1:26px; --h2:18px; --p:12.5px;
        }
        html,body{margin:0;background:var(--bg);color:var(--ink);font:400 14px/1.55 system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;}
        .page{max-width:840px;margin:0 auto;padding:32px 28px;}
        .cover{display:flex;min-height:88vh;align-items:center;justify-content:center;text-align:center}
        .cover h1{font-size:40px;margin:18px 0 8px}
        .cover p{color:var(--muted);margin:4px 0 0}
        .logo{width:220px;height:80px;margin:0 auto}
        h2{font-size:var(--h2);margin:28px 0 10px}
        p{font-size:var(--p);margin:10px 0}
        ul{margin:10px 0 10px 20px}
        .muted{color:var(--muted)}
        hr{border:0;border-top:1px solid #e5e7eb;margin:24px 0}
        .toc h2{margin-top:0}
        .toc ol{counter-reset: toc; list-style:none; padding-left:0}
        .toc li{margin:6px 0}
        .toc a{text-decoration:none;color:inherit;border-bottom:1px dotted #bbb}
        .section{break-inside:avoid}
        .foot{margin-top:32px;font-size:12px;color:#666}

        /* Ispis: brojevi stranica u podnožju */
        @page{
            size:A4;
            margin:18mm 16mm 18mm 16mm;
        }
        @media print{
            .page{max-width:none;padding:0}
            .no-print{display:none!important}
            footer.print-footer{
                position:fixed; bottom:0; left:0; right:0;
                font:12px/1.4 system-ui; color:#666; text-align:right;
                padding:4mm 16mm;
            }
            footer.print-footer .pageno:after{content:"Stranica " counter(page)}
        }

        /* Mali “brand” naslovnice */
        .crown path{stroke:var(--gold-d);fill:var(--gold)}
        .wordmark{font-weight:700;letter-spacing:.2px}
    </style>
</head>
<body>

<!-- Naslovnica -->
<section class="page cover" id="pocetna">
    <div>
        <svg class="logo" viewBox="0 0 300 100" aria-label="Companythrone">
            <!-- kruna -->
            <g class="crown" transform="translate(70,28) scale(1.1)">
                <path d="M0 40 L22 10 L44 40 L66 5 L88 40 L110 10 L132 40 Z" stroke-width="3"/>
            </g>
            <!-- wordmark -->
            <text x="155" y="63" text-anchor="middle" class="wordmark" font-size="28" font-family="Inter, Segoe UI, Arial">Companythrone</text>
        </svg>
        <h1>Početni plan (specifikacija)</h1>
        <p class="muted" id="datum"></p>
    </div>
</section>

<hr class="page">

<!-- Sadržaj -->
<section class="page toc">
    <h2>Sadržaj</h2>
    <ol>
        <li><a href="#naziv-projekta">Naziv projekta</a></li>
        <li><a href="#cilj">Cilj</a></li>
        <li><a href="#javne-stranice">Javne stranice</a></li>
        <li><a href="#pretraga">Pretraga</a></li>
        <li><a href="#onboarding">Dodavanje tvrtke (onboarding)</a></li>
        <li><a href="#vidljivost">Vidljivost i aktivacija linka</a></li>
        <li><a href="#preporuke">Preporuke (referrals)</a></li>
        <li><a href="#nivoi">Nivoi i raspodjela klikova</a></li>
        <li><a href="#rotacija-antispam">Rotacija i anti-spam smjernice</a></li>
        <li><a href="#fallback">Stalno aktivni linkovi (fallback)</a></li>
        <li><a href="#povratne">Povratne informacije korisniku</a></li>
        <li><a href="#admin">Administracija</a></li>
        <li><a href="#moderiranje">Sadržaj i moderiranje</a></li>
        <li><a href="#jezici">Jezici</a></li>
        <li><a href="#tehnologije">Tehnologije</a></li>
        <li><a href="#placanja">Plaćanja i obnova</a></li>
        <li><a href="#tok">Korisnička priča — dnevni tok</a></li>
        <li><a href="#napomene">Dodatne napomene</a></li>
    </ol>
</section>

<hr class="page">

<!-- Sekcije -->
<section class="page">
    <div class="section" id="naziv-projekta">
        <h2>Naziv projekta</h2>
        <p>Companythrone — mrežni poslovni katalog s gamificiranom razmjenom posjeta.</p>
    </div>

    <div class="section" id="cilj">
        <h2>Cilj</h2>
        <p>Povećanje prometa na web stranicama klijenata te održavanje i proširenje poslovnih kontakata.</p>
    </div>

    <div class="section" id="javne-stranice">
        <h2>Javne stranice</h2>
        <ul>
            <li><strong>Naslovnica:</strong> tražilica, nekoliko bannera (s kalendarom/zakazivanjem prikaza), gumb „Dodaj svoju tvrtku”, FAQ, Kontakt, O nama, te svi pravno obvezni elementi (Uvjeti korištenja, Privatnost, Kolačići).</li>
            <li><strong>Stranica tvrtke:</strong> logotip, link na web, osnovni podaci; dodatno prikaz nekoliko logotipa drugih tvrtki iz sustava (ravnomjerna rotacija prema algoritmu).</li>
        </ul>
    </div>

    <div class="section" id="pretraga">
        <h2>Pretraga</h2>
        <p>Pretraživanje po nazivu tvrtke, ključnim riječima (max 5) i OIB-u.</p>
    </div>

    <div class="section" id="onboarding">
        <h2>Dodavanje tvrtke (onboarding)</h2>
        <ul>
            <li>Obrazac: Naziv, Ulica, Broj, Mjesto, Država, E-mail, Telefon, Logotip (upload).</li>
            <li>Nakon pregleda logotipa i podataka šalje se ponuda za uplatu ({{ app_settings()->getPrice() }} € godišnje). Načini plaćanja: račun, kartica.</li>
            <li>Nakon isteka godine, aplikacija automatski šalje e-mail s novom ponudom (obnova).</li>
        </ul>
    </div>

    <div class="section" id="vidljivost">
        <h2>Vidljivost i aktivacija linka</h2>
        <ul>
            <li>Nakon uplate logotip je vidljiv, ali <strong>link nije aktivan</strong> dok se ne izvrše dnevni zadaci.</li>
            <li>Prijavljena tvrtka dnevno dobiva <strong>25 tipki</strong> (brojevi 1–25). Aplikacija na svaku tipku povezuje link na stranicu neke druge tvrtke iz sustava.</li>
            <li><strong>Link tvrtke postaje aktivan tek nakon što klikne svih 25 tipki</strong> (tj. posjeti svih 25 ponuđenih stranica taj dan).</li>
            <li>Tvrtka može dnevno ostvariti do <strong>780 posjeta</strong> na svoju stranicu (ovisno o popunjenosti i rotaciji).</li>
        </ul>
    </div>

    <div class="section" id="preporuke">
        <h2>Preporuke (referrals)</h2>
        <p>Za aktivaciju linka na svoju stranicu potrebno je <strong>preporučiti najmanje 5 drugih tvrtki</strong> (nivo 2). Dovoljno je jednom ispuniti uvjet (5 preporuka).</p>
    </div>

    <div class="section" id="nivoi">
        <h2>Nivoi i raspodjela klikova</h2>
        <ul>
            <li>Sustav koristi 5 nivoa (1–5). Aplikacija „vuče” linkove s ukupno 5 nivoa (~625 tvrtki) kako bi <strong>ravnomjerno rasporedila</strong> klikove, dajući prednost ranije uključenima.</li>
            <li>Dnevnih 25 tipki puni se po pravilima:
                <ul>
                    <li>prvih 5 tipki → linkovi iz <strong>nivoa 5</strong></li>
                    <li>drugih 5 tipki → linkovi iz <strong>nivoa 4</strong></li>
                    <li>trećih 5 tipki → linkovi iz <strong>nivoa 3</strong></li>
                    <li>četvrtih 5 tipki → linkovi iz <strong>nivoa 2</strong></li>
                    <li>petih 5 tipki → linkovi iz <strong>nivoa 1</strong></li>
                </ul>
            </li>
            <li>Linkovi idu s nivoa ispod bez obzira tko je koga uključio (neovisno o referral odnosu).</li>
        </ul>
    </div>

    <div class="section" id="rotacija-antispam">
        <h2>Rotacija i anti-spam smjernice</h2>
        <ul>
            <li>Periodična <strong>rotacija pozicija tvrtki</strong> između nivoa kako bi iz različitih smjerova dobivale klikove.</li>
            <li>Zaštite da se <strong>e-mail s linkovima ne tretira kao spam</strong> (poželjno dohvat linkova kroz aplikaciju, npr. 5 po 5; DMARC/SPF/DKIM usklađenost).</li>
            <li><strong>Google-safe</strong> posjete: nasumični redoslijed, vremenski jitter, ograničenja po IP-u/UA-u, detekcija botova, rate-limit, CAPTCHA, logiranje i audit.</li>
        </ul>
    </div>

    <div class="section" id="fallback">
        <h2>Stalno aktivni linkovi (fallback)</h2>
        <p>Mogućnost postavljanja ~100 linkova koji su <strong>stalno aktivni</strong> u slučaju da u određenom razdoblju nitko ne klikće (osigurava minimalnu vidljivost).</p>
    </div>

    <div class="section" id="povratne">
        <h2>Povratne informacije korisniku</h2>
        <p>Ako tvrtka <strong>nije kliknula svih 25 tipki</strong>, <strong>ne obavještavati</strong> je izravno da link nije aktivan — cilj je poticati češće vraćanje na platformu i ručnu provjeru statusa.</p>
    </div>

    <div class="section" id="admin">
        <h2>Administracija</h2>
        <ul>
            <li>Pregledna administracija s pregledom uplata, tvrtki po nivoima i <strong>mogućnošću ručnog</strong> postavljanja po nivoima.</li>
            <li>Napredna pretraga po nazivu, OIB-u, mjestu, državi.</li>
            <li>Upravljanje bannerima s rasporedom (datumsko zakazivanje).</li>
        </ul>
    </div>

    <div class="section" id="moderiranje">
        <h2>Sadržaj i moderiranje</h2>
        <ul>
            <li><strong>Ne objavljujemo</strong> logotipe/sadržaj za: alkohol, pornografiju, nasilje, politiku, vojsku, oružje ili drugi neprimjereni sadržaj.</li>
            <li>Objavu tvrtke/logotipa možemo <strong>odbijati bez objašnjenja</strong>.</li>
            <li>Ista tvrtka može biti objavljena <strong>samo jednom</strong> (po OIB-u).</li>
        </ul>
    </div>

    <div class="section" id="jezici">
        <h2>Jezici</h2>
        <p>HR i EN. (Iako tražilice nude prijevod, službeno podržati oba jezika na frontu.)</p>
    </div>

    <div class="section" id="tehnologije">
        <h2>Tehnologije</h2>
        <p>Laravel + Livewire; MySQL 8; integracije: slugs, lokalizacija, medijske datoteke, (opcionalno) permisije i uloge.</p>
    </div>

    <div class="section" id="placanja">
        <h2>Plaćanja i obnova</h2>
        <ul>
            <li>Godišnja pretplata <strong>{{ app_settings()->getPrice() }} €</strong>. Nakon uplate kreće razdoblje vidljivosti.</li>
            <li>Po isteku godine automatizirano <strong>slanje ponude za obnovu</strong>; nakon uplate se obnavlja razdoblje.</li>
        </ul>
    </div>

    <div class="section" id="tok">
        <h2>Korisnička priča — dnevni tok</h2>
        <ol>
            <li>Tvrtka uplati i prijavi se.</li>
            <li>Svaki dan otvara 25 tipki i klikne sve ponuđene linkove.</li>
            <li>Nakon 25/25, <strong>link tvrtke je aktivan</strong> do kraja dana.</li>
            <li>Kako se mreža puni, broj dolaznih klikova raste do ciljanog maksimuma.</li>
            <li>Jednokratno: tvrtka preporuči ≥5 drugih tvrtki (nivo 2).</li>
        </ol>
    </div>

    <div class="section" id="napomene">
        <h2>Dodatne napomene</h2>
        <ul>
            <li>Bannere prikazivati po rasporedu (pozicije na naslovnici, vrijeme start/stop).</li>
            <li>Algoritam za „nekoliko logotipa ostalih tvrtki” na stranici tvrtke mora osigurati <strong>ravnomjernu rotaciju</strong>.</li>
            <li>Logiranje svih klikova i dnevnih sesija (auditable).</li>
        </ul>
    </div>

    <div class="foot muted no-print">
        Savjet: File → Print → Save as PDF (brojevi stranica se dodaju pri ispisa).
    </div>
</section>

<footer class="print-footer"><span class="pageno"></span></footer>

<script>
    // datum na naslovnici
    const d = new Date();
    const ds = d.toLocaleDateString('hr-HR', {day:'2-digit', month:'2-digit', year:'numeric'});
    document.getElementById('datum').textContent = ds;
</script>
</body>
</html>
