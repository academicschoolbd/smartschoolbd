<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Landing variant D — "Playful / Illustration".
 *
 * Soft pastel backgrounds tinted from the brand colour, rounded card layout,
 * subtle blob-shaped decorations and friendly Notion/Canva-ish typography.
 * Reads exactly the same fields off $s as variants A and B, plus the tiered
 * packages array when pricing_mode === 'tiers'.
 *
 * @author SmartSchool.bd
 */
$signup = isset($signup_url) ? $signup_url : base_url('signup');
$login  = isset($login_url)  ? $login_url  : base_url('authentication');
$s      = isset($s) ? $s : (object)[];

$brand  = !empty($s->brand_color) ? $s->brand_color : '#1f9d55';
$h1     = !empty($s->hero_h1)     ? $s->hero_h1     : 'Run your school in 5 minutes — on us.';
$h1_bn  = !empty($s->hero_bn)     ? $s->hero_bn     : '';
$lead   = !empty($s->hero_lead)   ? $s->hero_lead   : '';
$eyeb   = !empty($s->hero_eyebrow)? $s->hero_eyebrow: '';
$cta1   = !empty($s->cta_primary_label)   ? $s->cta_primary_label   : 'Sign your school up — free';
$cta2   = !empty($s->cta_secondary_label) ? $s->cta_secondary_label : 'See what is included';
$ph     = !empty($s->pricing_headline)    ? $s->pricing_headline    : 'One plan. Everything included.';
$pmode  = !empty($s->pricing_mode) ? $s->pricing_mode : 'free';
$pfn    = $s->pricing_future_note ?? '';

$show_feat    = !isset($s->show_features)    || (int)$s->show_features    === 1;
$show_price   = !isset($s->show_pricing)     || (int)$s->show_pricing     === 1;
$show_test    = !isset($s->show_testimonials)|| (int)$s->show_testimonials === 1;
$show_schools = !isset($s->show_schools)     || (int)$s->show_schools     === 1;

$packages       = isset($packages) && is_array($packages) ? $packages : [];
$feature_labels = isset($feature_labels) && is_array($feature_labels) ? $feature_labels : [];
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=htmlspecialchars($page_title ?? 'SmartSchool.bd')?></title>
<meta name="description" content="Friendly school management software for Bangladesh. Pastel, rounded, easy to love.">
<meta name="theme-color" content="<?=htmlspecialchars($brand)?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap">
<style>
:root{
  --brand:<?=htmlspecialchars($brand)?>;
  --brand-tint-1: color-mix(in srgb, var(--brand) 14%, #fff);
  --brand-tint-2: color-mix(in srgb, var(--brand) 8%, #fff);
  --brand-tint-3: color-mix(in srgb, var(--brand) 22%, #fff);
  --ink:#1d2433;
  --ink-soft:#3e475c;
  --muted:#6b7488;
  --bg:#fff8f1;
  --bg-2:#fffdf9;
  --line:#efe7dc;
  --shadow-sm:0 2px 8px rgba(45,30,15,.05);
  --shadow-md:0 18px 40px -12px rgba(45,30,15,.12);
  --radius:18px;
  --radius-lg:28px;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:'Plus Jakarta Sans','Hind Siliguri',system-ui,-apple-system,sans-serif;color:var(--ink);background:var(--bg);line-height:1.6;-webkit-font-smoothing:antialiased;margin:0}
.bn{font-family:'Hind Siliguri','Noto Sans Bengali',sans-serif}
.container{max-width:1140px;margin:0 auto;padding:0 24px}
a{color:var(--ink);text-decoration:none}
a:hover{color:var(--brand)}
h1,h2,h3,h4,h5{font-weight:800;letter-spacing:-.02em;color:var(--ink);margin:0}
p{color:var(--ink-soft);margin:0}

/* Decorative blobs */
.blob{position:absolute;border-radius:50%;filter:blur(40px);opacity:.55;pointer-events:none;z-index:0}

/* Nav */
.nav{position:sticky;top:0;background:rgba(255,248,241,.85);backdrop-filter:saturate(180%) blur(12px);-webkit-backdrop-filter:saturate(180%) blur(12px);border-bottom:1px solid var(--line);z-index:100}
.nav .row{display:flex;align-items:center;justify-content:space-between;padding:16px 0}
.nav .brand{font-weight:800;font-size:1.1rem;color:var(--ink);letter-spacing:-.02em;display:inline-flex;align-items:center;gap:8px}
.nav .brand .mark{width:30px;height:30px;border-radius:9px;background:var(--brand);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.95rem;transform:rotate(-6deg)}
.nav .links{display:flex;gap:26px;align-items:center;font-size:.95rem;color:var(--ink-soft)}
.nav .links a{color:var(--ink-soft);font-weight:500}
.nav .links a:hover{color:var(--ink)}

.btn{display:inline-flex;align-items:center;gap:8px;border:2px solid transparent;border-radius:999px;padding:10px 20px;font-weight:700;font-size:.93rem;cursor:pointer;transition:transform .15s,box-shadow .15s,background .15s,color .15s,border-color .15s}
.btn-primary{background:var(--brand);color:#fff;border-color:var(--brand);box-shadow:var(--shadow-sm)}
.btn-primary:hover{filter:brightness(1.06);transform:translateY(-2px);box-shadow:var(--shadow-md);color:#fff}
.btn-ghost{background:#fff;color:var(--ink);border-color:var(--line)}
.btn-ghost:hover{border-color:var(--brand);color:var(--brand)}
.btn-lg{padding:14px 26px;font-size:1rem}

/* Hero */
.hero{position:relative;padding:90px 0 80px;overflow:hidden}
.hero .blob.b1{width:380px;height:380px;background:var(--brand-tint-3);top:-120px;right:-80px}
.hero .blob.b2{width:300px;height:300px;background:#ffd9a8;bottom:-100px;left:-60px;opacity:.6}
.hero .grid{position:relative;z-index:1;display:grid;grid-template-columns:1.15fr .85fr;gap:60px;align-items:center}
.hero .eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:.85rem;color:var(--ink-soft);font-weight:600;margin-bottom:22px;padding:6px 14px;border-radius:999px;background:#fff;border:1px solid var(--line);box-shadow:var(--shadow-sm)}
.hero .eyebrow .dot{width:8px;height:8px;border-radius:50%;background:var(--brand)}
.hero h1{font-size:clamp(2.4rem,5.2vw,4rem);line-height:1.05;margin-bottom:22px;letter-spacing:-.035em}
.hero h1 .accent{background:linear-gradient(120deg,var(--brand),#f59e0b 95%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.hero .bn-tag{font-size:1.05rem;color:var(--muted);margin-bottom:22px;max-width:520px}
.hero .lead{font-size:1.1rem;color:var(--ink-soft);margin-bottom:32px;max-width:560px;line-height:1.6}
.hero .ctas{display:flex;flex-wrap:wrap;gap:14px;align-items:center}
.hero .stat-row{margin-top:36px;display:flex;flex-wrap:wrap;gap:20px}
.hero .stat-pill{background:#fff;border:1px solid var(--line);border-radius:14px;padding:12px 16px;display:flex;flex-direction:column;gap:2px;box-shadow:var(--shadow-sm)}
.hero .stat-pill .num{font-weight:800;font-size:1.15rem;color:var(--ink)}
.hero .stat-pill .lbl{font-size:.78rem;color:var(--muted);font-weight:600}

/* Hero illustration card */
.illus{position:relative;background:#fff;border-radius:var(--radius-lg);padding:28px;border:1px solid var(--line);box-shadow:var(--shadow-md);transform:rotate(1.5deg)}
.illus .top{display:flex;gap:6px;margin-bottom:16px}
.illus .top span{width:10px;height:10px;border-radius:50%;background:var(--line)}
.illus .top span:first-child{background:#ff8a8a}.illus .top span:nth-child(2){background:#ffce5c}.illus .top span:nth-child(3){background:#7ad28b}
.illus .row-mock{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:12px;background:var(--brand-tint-2);margin-bottom:10px}
.illus .row-mock .av{width:36px;height:36px;border-radius:50%;background:var(--brand);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem}
.illus .row-mock .lines{flex:1;display:flex;flex-direction:column;gap:6px}
.illus .row-mock .lines span{height:8px;border-radius:4px;background:#e6dfd2;display:block}
.illus .row-mock .lines span:first-child{width:65%}
.illus .row-mock .lines span:last-child{width:40%;background:#f1ead9}
.illus .row-mock .pill{font-size:.7rem;font-weight:700;padding:3px 8px;border-radius:999px;background:var(--brand);color:#fff}
.illus .row-mock.alt{background:#fff;border:1px dashed var(--line)}
.illus .stamp{position:absolute;top:-14px;right:-10px;background:#f59e0b;color:#fff;padding:6px 12px;border-radius:999px;font-size:.75rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;transform:rotate(8deg);box-shadow:var(--shadow-sm)}

/* Section primitives */
.section{padding:90px 0;position:relative}
.section .head{margin-bottom:54px;max-width:680px}
.section .eyebrow{font-size:.78rem;font-weight:700;letter-spacing:.16em;color:var(--brand);text-transform:uppercase;margin-bottom:14px}
.section h2{font-size:clamp(1.9rem,3.8vw,2.7rem);line-height:1.15;letter-spacing:-.025em;margin-bottom:14px}
.section .lead-p{font-size:1.05rem;color:var(--ink-soft);max-width:580px}

/* Features — pastel rounded cards */
.feat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:22px}
.feat-grid .item{padding:30px 26px;background:#fff;border:1px solid var(--line);border-radius:var(--radius);transition:transform .2s,box-shadow .2s}
.feat-grid .item:hover{transform:translateY(-4px) rotate(-.4deg);box-shadow:var(--shadow-md)}
.feat-grid .item .icon{width:46px;height:46px;border-radius:14px;background:var(--brand-tint-1);color:var(--brand);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;font-weight:800;font-size:1.05rem}
.feat-grid .item:nth-child(2n) .icon{background:#fde9c8;color:#b8730d}
.feat-grid .item:nth-child(3n) .icon{background:#e3f0ff;color:#1f5fc6}
.feat-grid .item h5{font-size:1.1rem;margin-bottom:10px;letter-spacing:-.02em}
.feat-grid .item p{font-size:.95rem;color:var(--ink-soft);line-height:1.6}

/* Pricing — free callout */
.price-block{display:grid;grid-template-columns:1.1fr 1fr;gap:48px;align-items:center;background:#fff;border:1px solid var(--line);border-radius:var(--radius-lg);padding:48px;box-shadow:var(--shadow-sm);position:relative;overflow:hidden}
.price-block .blob{width:340px;height:340px;background:var(--brand-tint-3);top:-140px;right:-120px;opacity:.5}
.price-block > *{position:relative;z-index:1}
.price-block .badge{display:inline-block;font-size:.78rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--brand);background:var(--brand-tint-1);padding:6px 12px;border-radius:999px;margin-bottom:18px}
.price-block h2{font-size:clamp(2rem,4vw,2.6rem);line-height:1.1;margin-bottom:18px}
.price-block .big-price{font-size:clamp(4.5rem,11vw,7rem);font-weight:800;letter-spacing:-.05em;line-height:1;color:var(--ink);margin-bottom:14px}
.price-block .big-price .unit{font-size:1rem;font-weight:500;color:var(--muted);letter-spacing:0;margin-left:8px;display:inline-block;vertical-align:middle}
.price-block .strike{color:var(--muted);font-size:1rem;text-decoration:line-through;margin-bottom:30px;display:block}
.price-block .perks{list-style:none;padding:0;margin:0 0 24px;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px 24px}
.price-block .perks li{display:flex;gap:8px;align-items:flex-start;color:var(--ink-soft);font-size:.95rem}
.price-block .perks li svg{color:var(--brand);flex-shrink:0;margin-top:3px}
.price-block .future-note{margin-top:20px;padding:16px 18px;background:var(--brand-tint-2);border-radius:14px;font-size:.92rem;color:var(--ink-soft);line-height:1.55}
.price-block .future-note strong{color:var(--ink)}

/* Pricing — tier cards */
.price-cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:22px}
.price-card{background:#fff;border:1px solid var(--line);border-radius:var(--radius);padding:30px 26px;display:flex;flex-direction:column;transition:transform .15s,box-shadow .15s}
.price-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-md)}
.price-card.featured{border:2px solid var(--brand);background:linear-gradient(180deg,var(--brand-tint-2),#fff 60%)}
.price-card .badge-pop{display:inline-block;background:var(--brand);color:#fff;padding:4px 12px;border-radius:999px;font-size:.72rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;margin-bottom:12px;align-self:flex-start}
.price-card .plan-name{font-weight:700;font-size:1.1rem;margin-bottom:12px}
.price-card .price{font-size:2.2rem;font-weight:800;letter-spacing:-.03em;margin-bottom:4px}
.price-card .price small{font-size:.95rem;color:var(--muted);font-weight:400}
.price-card .per{color:var(--muted);font-size:.88rem;margin-bottom:18px}
.price-card .feat-list{list-style:none;padding:0;margin:0 0 24px;flex:1}
.price-card .feat-list li{display:flex;gap:8px;align-items:flex-start;padding:5px 0;color:var(--ink-soft);font-size:.93rem}
.price-card .feat-list li::before{content:'\2713';color:var(--brand);font-weight:700;flex-shrink:0}
.price-card .trial-note{font-size:.85rem;color:var(--brand);font-weight:600;margin-bottom:14px}

/* Testimonials */
.t-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:22px}
.t-grid .t{padding:30px 28px;background:#fff;border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow-sm)}
.t-grid .t:nth-child(2n){background:var(--brand-tint-2)}
.t-grid .t .mark{font-size:3rem;line-height:1;color:var(--brand);font-family:Georgia,serif;margin-bottom:-6px;display:block}
.t-grid .t .q{font-size:1.05rem;line-height:1.55;color:var(--ink);margin-bottom:20px;letter-spacing:-.005em}
.t-grid .t .by{font-size:.92rem;color:var(--ink-soft);display:flex;align-items:center;gap:10px}
.t-grid .t .by .av{width:34px;height:34px;border-radius:50%;background:var(--brand);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.82rem}
.t-grid .t .by a{color:var(--brand);font-weight:600}

/* Final CTA */
.cta-strip{padding:80px 0;text-align:center}
.cta-strip .inner{position:relative;background:#fff;border:1px solid var(--line);border-radius:var(--radius-lg);padding:64px 32px;overflow:hidden;box-shadow:var(--shadow-sm)}
.cta-strip .inner .blob.x{width:340px;height:340px;background:var(--brand-tint-3);top:-100px;right:-100px;opacity:.55}
.cta-strip .inner .blob.y{width:280px;height:280px;background:#ffd9a8;bottom:-110px;left:-80px;opacity:.55}
.cta-strip .inner > *{position:relative;z-index:1}
.cta-strip h2{font-size:clamp(2rem,4.4vw,3rem);line-height:1.1;letter-spacing:-.03em;margin-bottom:14px;max-width:18ch;margin-left:auto;margin-right:auto}
.cta-strip p{color:var(--ink-soft);font-size:1.05rem;margin-bottom:26px;max-width:520px;margin-left:auto;margin-right:auto}

/* Footer */
footer{padding:48px 0 32px;border-top:1px solid var(--line);background:var(--bg-2)}
footer .row{display:grid;grid-template-columns:2fr 1fr 1fr;gap:48px;margin-bottom:32px}
footer .col h6{font-size:.78rem;font-weight:700;letter-spacing:.12em;color:var(--muted);text-transform:uppercase;margin-bottom:14px}
footer .col ul{list-style:none;padding:0;margin:0}
footer .col ul li{padding:5px 0}
footer .col ul li a{color:var(--ink-soft);font-size:.95rem}
footer .col ul li a:hover{color:var(--ink)}
footer .brand{display:inline-flex;align-items:center;gap:8px;font-weight:800;font-size:1.05rem;letter-spacing:-.02em;color:var(--ink);margin-bottom:10px}
footer .brand .mark{width:28px;height:28px;border-radius:9px;background:var(--brand);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.85rem;transform:rotate(-6deg)}
footer .blurb{color:var(--muted);font-size:.92rem;max-width:340px;line-height:1.55}
footer .bot{display:flex;flex-wrap:wrap;justify-content:space-between;border-top:1px solid var(--line);padding-top:24px;font-size:.85rem;color:var(--muted);gap:8px}

@media(prefers-reduced-motion:no-preference){
  .reveal{opacity:0;transform:translateY(10px);animation:rev .9s ease forwards}
  .reveal.d1{animation-delay:.05s}.reveal.d2{animation-delay:.18s}
  @keyframes rev{to{opacity:1;transform:none}}
}
@media(max-width:900px){
  .hero .grid{grid-template-columns:1fr;gap:40px}
  .illus{transform:none}
}
@media(max-width:767px){
  .hero{padding:64px 0 60px}
  .section{padding:64px 0}
  .nav .links a:not(.btn){display:none}
  .price-block{grid-template-columns:1fr;gap:32px;padding:32px 24px}
  footer .row{grid-template-columns:1fr;gap:32px}
  .price-cards-grid{grid-template-columns:1fr}
}
</style>
</head>
<body data-variant="d">

<nav class="nav">
  <div class="container">
    <div class="row">
      <a class="brand" href="<?=base_url()?>"><span class="mark">S</span> SmartSchool.bd</a>
      <div class="links">
        <?php if ($show_feat): ?><a href="#features">Features</a><?php endif; ?>
        <?php if ($show_price): ?><a href="#pricing">Pricing</a><?php endif; ?>
        <?php if ($show_schools): ?><a href="#schools">Schools</a><?php endif; ?>
        <a href="<?=htmlspecialchars($login)?>">Login</a>
        <a class="btn btn-primary" href="<?=htmlspecialchars($signup)?>">Get started</a>
      </div>
    </div>
  </div>
</nav>

<section class="hero">
  <span class="blob b1"></span>
  <span class="blob b2"></span>
  <div class="container">
    <div class="grid">
      <div class="reveal d1">
        <?php if ($eyeb): ?><div class="eyebrow"><span class="dot"></span><?=htmlspecialchars($eyeb)?></div><?php endif; ?>
        <h1><?=htmlspecialchars($h1)?> <span class="accent">For free.</span></h1>
        <?php if ($h1_bn): ?><p class="bn bn-tag"><?=htmlspecialchars($h1_bn)?></p><?php endif; ?>
        <?php if ($lead): ?><p class="lead"><?=htmlspecialchars($lead)?></p><?php endif; ?>
        <div class="ctas">
          <a class="btn btn-primary btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?></a>
          <a class="btn btn-ghost btn-lg" href="#features"><?=htmlspecialchars($cta2)?></a>
        </div>
        <div class="stat-row">
          <div class="stat-pill"><span class="num">2 schools</span><span class="lbl">Live today</span></div>
          <div class="stat-pill"><span class="num">৳0/mo</span><span class="lbl">No card</span></div>
          <div class="stat-pill"><span class="num">5 min</span><span class="lbl">To launch</span></div>
          <div class="stat-pill"><span class="num">BN + EN</span><span class="lbl">Fully bilingual</span></div>
        </div>
      </div>
      <div class="reveal d2">
        <div class="illus">
          <span class="stamp">Free</span>
          <div class="top"><span></span><span></span><span></span></div>
          <div class="row-mock"><span class="av">A</span><div class="lines"><span></span><span></span></div><span class="pill">Present</span></div>
          <div class="row-mock alt"><span class="av" style="background:#f59e0b">R</span><div class="lines"><span></span><span></span></div><span class="pill" style="background:#f59e0b">A+</span></div>
          <div class="row-mock"><span class="av" style="background:#1f5fc6">F</span><div class="lines"><span></span><span></span></div><span class="pill" style="background:#1f5fc6">Paid</span></div>
          <div class="row-mock alt"><span class="av">N</span><div class="lines"><span></span><span></span></div><span class="pill">Sent</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if ($show_feat): ?>
<section class="section" id="features">
  <div class="container">
    <div class="head reveal d1">
      <div class="eyebrow">Features</div>
      <h2>Everything you need — nothing in your way.</h2>
      <p class="lead-p">Friendly, modern tools for every part of running your school.</p>
    </div>
    <div class="feat-grid">
      <div class="item"><span class="icon">01</span><h5>Admissions &amp; students</h5><p>Online admission form, documents, ID generation, class &amp; section assignment.</p></div>
      <div class="item"><span class="icon">02</span><h5>Attendance &amp; exams</h5><p>Daily attendance, exam schedule, marks entry, gradebook, downloadable report cards.</p></div>
      <div class="item"><span class="icon">03</span><h5>Fees &amp; accounting</h5><p>Fee invoices, online + offline collection, dues, full double-entry accounting.</p></div>
      <div class="item"><span class="icon">04</span><h5>Public school website</h5><p>Sliders, news, gallery, events, teacher pages — without writing code.</p></div>
      <div class="item"><span class="icon">05</span><h5>Parent &amp; student portals</h5><p>Parents see attendance, results, fees, notices. Students see homework, marks, live classes.</p></div>
      <div class="item"><span class="icon">06</span><h5>Multi-tenant &amp; isolated</h5><p>Your data is fully separated from every other school. Zero cross-tenant access.</p></div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($show_price && $pmode !== 'hidden'): ?>
<section class="section" id="pricing">
  <div class="container">
    <?php if ($pmode === 'tiers' && !empty($packages)): ?>
    <div class="head reveal d1">
      <div class="eyebrow">Pricing</div>
      <h2><?=htmlspecialchars($ph)?></h2>
      <p class="lead-p">Start free, upgrade as your school grows. All prices in BDT.</p>
    </div>
    <div class="price-cards-grid">
      <?php foreach ($packages as $p):
        $featured = (strtolower($p->code) === 'starter');
        $price = (float)$p->price_bdt;
        $per = $p->billing_period === 'yearly' ? '/year' : ($p->billing_period === 'lifetime' ? ' one-time' : '/month');
        $feats = is_array($p->features) ? $p->features : [];
        $highlights = [];
        foreach (array_slice($feats, 0, 7) as $f) {
            $highlights[] = isset($feature_labels[$f]) ? $feature_labels[$f] : str_replace('_', ' ', $f);
        }
      ?>
      <div class="price-card <?= $featured ? 'featured' : '' ?>">
        <?php if ($featured): ?><span class="badge-pop">Most popular</span><?php endif; ?>
        <div class="plan-name"><?=htmlspecialchars($p->name)?></div>
        <div class="price"><?php if ($price > 0): ?>&#2547;<?=number_format($price)?><small><?=htmlspecialchars($per)?></small><?php else: ?>Free<?php endif; ?></div>
        <?php if ($price > 0): ?><div class="per">per school <?=htmlspecialchars($per)?></div><?php endif; ?>
        <?php if ((int)$p->trial_days > 0): ?><div class="trial-note"><?=(int)$p->trial_days?>-day free trial</div><?php endif; ?>
        <ul class="feat-list">
          <?php foreach ($highlights as $h): ?><li><?=htmlspecialchars($h)?></li><?php endforeach; ?>
        </ul>
        <a class="btn btn-primary" href="<?=htmlspecialchars($signup)?>?plan=<?=urlencode($p->code)?>"><?= $price > 0 ? 'Choose ' . htmlspecialchars($p->name) : 'Start free' ?></a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="price-block reveal d1">
      <span class="blob"></span>
      <div>
        <div class="badge">100% free, today</div>
        <h2><?=htmlspecialchars($ph)?></h2>
        <div class="big-price">৳0<span class="unit">/ school / month</span></div>
        <span class="strike">Future tier: ৳2,999 / month</span>
        <div style="display:flex;gap:14px;flex-wrap:wrap">
          <a class="btn btn-primary btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?></a>
          <a class="btn btn-ghost btn-lg" href="<?=htmlspecialchars($login)?>">Login</a>
        </div>
      </div>
      <div>
        <ul class="perks">
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Unlimited students &amp; staff</li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Every module unlocked</li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Your own subdomain</li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Parent + student SMS</li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Full accounting</li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Public website CMS</li>
        </ul>
        <?php if (!empty($pfn)): ?>
        <div class="future-note"><strong>A note about the future.</strong> <?=htmlspecialchars($pfn)?></div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($show_schools && $show_test): ?>
<section class="section" id="schools">
  <div class="container">
    <div class="head reveal d1">
      <div class="eyebrow">Schools</div>
      <h2>Real schools, real traffic.</h2>
      <p class="lead-p">Two tenants serving live parents today.</p>
    </div>
    <div class="t-grid">
      <div class="t reveal d1">
        <span class="mark">"</span>
        <p class="q">We moved from paper attendance to SmartSchool.bd in a weekend. Parents now get an SMS the same morning if their child is absent — and we didn't pay a taka.</p>
        <div class="by"><span class="av">N</span><div>NGPS Academy · <a href="https://ngps.smartschool.bd" target="_blank" rel="noopener">ngps.smartschool.bd</a></div></div>
      </div>
      <div class="t reveal d2">
        <span class="mark">"</span>
        <p class="q">The Bengali UI made staff adoption painless. We were collecting fees online by week 2, with no licence fee in sight.</p>
        <div class="by"><span class="av">T</span><div>Test School · <a href="https://test.smartschool.bd" target="_blank" rel="noopener">test.smartschool.bd</a></div></div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="cta-strip">
  <div class="container">
    <div class="inner">
      <span class="blob x"></span>
      <span class="blob y"></span>
      <h2>Set up your school in 5 minutes. It costs you nothing.</h2>
      <p>Your own subdomain, every module unlocked, Bengali + English UI. No credit card.</p>
      <a class="btn btn-primary btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?> →</a>
    </div>
  </div>
</section>

<footer>
  <div class="container">
    <div class="row">
      <div class="col">
        <a class="brand" href="<?=base_url()?>"><span class="mark">S</span> SmartSchool.bd</a>
        <p class="blurb">Free school management software built specifically for Bangladesh schools. Bengali UI, BDT fees, parent SMS, all included.</p>
      </div>
      <div class="col">
        <h6>Product</h6>
        <ul>
          <?php if ($show_feat): ?><li><a href="#features">Features</a></li><?php endif; ?>
          <?php if ($show_price): ?><li><a href="#pricing">Pricing</a></li><?php endif; ?>
          <li><a href="<?=htmlspecialchars($signup)?>">Sign up free</a></li>
          <li><a href="<?=htmlspecialchars($login)?>">Login</a></li>
        </ul>
      </div>
      <div class="col">
        <h6>Live schools</h6>
        <ul>
          <li><a href="https://ngps.smartschool.bd" target="_blank" rel="noopener">ngps.smartschool.bd</a></li>
          <li><a href="https://test.smartschool.bd" target="_blank" rel="noopener">test.smartschool.bd</a></li>
          <li><a href="mailto:al.exbru69789@gmail.com">al.exbru69789@gmail.com</a></li>
        </ul>
      </div>
    </div>
    <div class="bot">
      <div>© <?=date('Y')?> SmartSchool.bd. Made in Bangladesh.</div>
      <div><a href="<?=base_url('home/privacy')?>">Privacy</a> · <a href="<?=base_url('home/terms')?>">Terms</a></div>
    </div>
  </div>
</footer>

</body>
</html>
