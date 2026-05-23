<?php
defined('BASEPATH') or exit('No direct script access allowed');
$signup = isset($signup_url) ? $signup_url : base_url('signup');
$login  = isset($login_url)  ? $login_url  : base_url('authentication');
$s      = isset($s) ? $s : (object)[];

$brand  = !empty($s->brand_color) ? $s->brand_color : '#1f9d55';
$h1     = !empty($s->hero_h1)     ? $s->hero_h1     : 'Run your school in 5 minutes — on us.';
$h1_bn  = !empty($s->hero_bn)     ? $s->hero_bn     : '';
$lead   = !empty($s->hero_lead)   ? $s->hero_lead   : '';
$eyeb   = !empty($s->hero_eyebrow)? $s->hero_eyebrow: 'Free for every Bangladeshi school';
$cta1   = !empty($s->cta_primary_label)   ? $s->cta_primary_label   : 'Sign your school up — free';
$cta2   = !empty($s->cta_secondary_label) ? $s->cta_secondary_label : 'See what is included';
$ph     = !empty($s->pricing_headline)    ? $s->pricing_headline    : 'One plan. Everything included.';
$pmode  = !empty($s->pricing_mode) ? $s->pricing_mode : 'free';
$pfn    = $s->pricing_future_note ?? '';

$show_feat  = !isset($s->show_features)    || (int)$s->show_features    === 1;
$show_price = !isset($s->show_pricing)     || (int)$s->show_pricing     === 1;
$show_test  = !isset($s->show_testimonials)|| (int)$s->show_testimonials === 1;
$show_schools = !isset($s->show_schools)   || (int)$s->show_schools     === 1;

$packages = isset($packages) && is_array($packages) ? $packages : [];
$feature_labels = isset($feature_labels) && is_array($feature_labels) ? $feature_labels : [];
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=htmlspecialchars($page_title ?? 'SmartSchool.bd')?></title>
<meta name="description" content="Free school management software for Bangladesh schools. Bengali UI, admissions, attendance, exam, fees, accounting and a public school website — 100% free right now.">
<meta name="theme-color" content="<?=htmlspecialchars($brand)?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
:root{
  --brand:<?=htmlspecialchars($brand)?>;
  --brand-2:#0ea36b;
  --brand-dark:#0f5d35;
  --ink:#0f1c2d;
  --ink-soft:#3a4a63;
  --muted:#637087;
  --bg:#fff;
  --bg-soft:#f4f8fb;
  --line:#e6ecf3;
  --card:#fff;
  --shadow-sm:0 1px 2px rgba(15,28,45,.04),0 4px 12px rgba(15,28,45,.04);
  --shadow-md:0 6px 24px rgba(15,28,45,.08);
  --shadow-glow:0 24px 60px -20px color-mix(in srgb, var(--brand) 45%, transparent);
  --radius:14px;--radius-lg:22px;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:'Inter','Hind Siliguri',system-ui,-apple-system,sans-serif;color:var(--ink);background:var(--bg);line-height:1.55;-webkit-font-smoothing:antialiased}
.bn{font-family:'Hind Siliguri','Noto Sans Bengali','SolaimanLipi',sans-serif}
.container{max-width:1180px}
h1,h2,h3,h4,h5{font-weight:700;letter-spacing:-.02em;color:var(--ink)}
a{color:var(--brand-dark)}
.nav-wrap{position:sticky;top:0;z-index:100;backdrop-filter:saturate(180%) blur(14px);-webkit-backdrop-filter:saturate(180%) blur(14px);background:rgba(255,255,255,.85);border-bottom:1px solid var(--line)}
.navbar{padding:14px 0}
.navbar .brand{font-weight:800;font-size:1.25rem;color:var(--brand);text-decoration:none;letter-spacing:-.02em}
.navbar .brand .dot{color:var(--ink)}
.navbar a.nav-link{color:var(--ink-soft);font-weight:500;font-size:.95rem}
.navbar a.nav-link:hover{color:var(--ink)}
.btn{font-weight:600;border-radius:10px;padding:10px 18px;font-size:.95rem;transition:transform .15s ease,box-shadow .15s ease}
.btn-brand{background:var(--brand);border-color:var(--brand);color:#fff;box-shadow:var(--shadow-sm)}
.btn-brand:hover{background:var(--brand-dark);border-color:var(--brand-dark);color:#fff;transform:translateY(-1px);box-shadow:var(--shadow-md)}
.btn-outline-soft{border:1px solid var(--line);background:#fff;color:var(--ink)}
.btn-outline-soft:hover{background:var(--bg-soft);color:var(--ink)}
.btn-lg{padding:14px 28px;font-size:1.05rem;border-radius:12px}
.hero{position:relative;padding:80px 0;overflow:hidden}
.hero::before{content:'';position:absolute;inset:0;z-index:-1;background:
  radial-gradient(900px 500px at 80% -20%, color-mix(in srgb, var(--brand) 18%, transparent), transparent 60%),
  radial-gradient(700px 420px at 5% 10%, color-mix(in srgb, var(--brand) 10%, transparent), transparent 60%),
  linear-gradient(180deg,#f3fbf6 0%, #ffffff 70%)}
.hero .eyebrow{display:inline-flex;align-items:center;gap:8px;background:#fff;border:1px solid var(--line);box-shadow:var(--shadow-sm);padding:6px 14px;border-radius:999px;font-size:.85rem;color:var(--ink-soft);font-weight:500;margin-bottom:20px}
.hero .eyebrow .dot{width:8px;height:8px;border-radius:50%;background:var(--brand);box-shadow:0 0 0 4px color-mix(in srgb, var(--brand) 18%, transparent)}
.hero h1{font-size:clamp(2.2rem,4.6vw,3.6rem);line-height:1.08;margin-bottom:16px}
.hero h1 .grad{background:linear-gradient(135deg,var(--brand) 0%,var(--brand-2) 60%,#26c281 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.hero .bn-tag{font-size:1.2rem;color:var(--ink-soft);margin-bottom:20px}
.hero .lead{font-size:1.1rem;color:var(--muted);margin-bottom:28px;max-width:600px}
.hero .ctas{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:18px}
.hero .meta-row{display:flex;flex-wrap:wrap;gap:18px;align-items:center;color:var(--muted);font-size:.92rem}
.hero .meta-row .checks{display:inline-flex;gap:6px;align-items:center}
.hero .meta-row .checks i{width:18px;height:18px;border-radius:50%;background:color-mix(in srgb, var(--brand) 15%, transparent);color:var(--brand);display:inline-flex;align-items:center;justify-content:center;font-size:.7rem;font-style:normal}
.hero-card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius-lg);box-shadow:var(--shadow-md);padding:22px;position:relative}
.hero-card::after{content:'';position:absolute;inset:-1px;border-radius:inherit;background:linear-gradient(135deg,color-mix(in srgb, var(--brand) 35%, transparent),transparent 60%);z-index:-1;filter:blur(20px);opacity:.45}
.hero-card .ribbon{display:inline-block;background:linear-gradient(135deg,#fff7cf,#ffe8a3);color:#7a5a00;font-weight:700;font-size:.78rem;padding:5px 11px;border-radius:999px;margin-bottom:14px;letter-spacing:.04em;text-transform:uppercase}
.hero-card h5{font-size:1.05rem;margin-bottom:14px}
.hero-card ul{list-style:none;padding:0;margin:0}
.hero-card ul li{display:flex;gap:10px;align-items:flex-start;padding:7px 0;color:var(--ink-soft);font-size:.95rem}
.hero-card ul li svg{flex-shrink:0;color:var(--brand);margin-top:3px}
.section{padding:90px 0}
.section.alt{background:var(--bg-soft)}
.eyebrow-row{text-align:center;color:var(--brand-dark);font-weight:600;text-transform:uppercase;letter-spacing:.12em;font-size:.78rem;margin-bottom:10px}
.section h2{font-size:clamp(1.75rem,3vw,2.4rem);text-align:center;margin:0 auto 14px;max-width:780px;line-height:1.2}
.section .lead{text-align:center;color:var(--muted);max-width:680px;margin:0 auto 44px;font-size:1.05rem}
.feature{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:26px;height:100%;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
.feature:hover{transform:translateY(-3px);box-shadow:var(--shadow-md);border-color:color-mix(in srgb, var(--brand) 25%, transparent)}
.feature .ic{width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,color-mix(in srgb, var(--brand) 12%, transparent),color-mix(in srgb, var(--brand) 6%, transparent));color:var(--brand);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px}
.feature h5{font-size:1.05rem;margin-bottom:8px}
.feature p{color:var(--muted);margin:0;font-size:.95rem;line-height:1.55}
.free-callout{position:relative;overflow:hidden;background:linear-gradient(135deg,var(--brand-dark) 0%,var(--brand) 55%,#26c281 100%);color:#fff;border-radius:var(--radius-lg);padding:54px 48px;box-shadow:var(--shadow-glow)}
.free-callout::before{content:'';position:absolute;inset:0;background:radial-gradient(700px 300px at 90% -30%, rgba(255,255,255,.16), transparent 60%),radial-gradient(500px 220px at -10% 110%, rgba(255,255,255,.12), transparent 60%);pointer-events:none}
.free-callout .free-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.18);backdrop-filter:blur(8px);padding:6px 14px;border-radius:999px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;font-size:.78rem;margin-bottom:16px}
.free-callout h2{color:#fff;font-size:clamp(2rem,3.6vw,3rem);line-height:1.1;margin-bottom:16px;max-width:760px}
.free-callout .price-row{display:flex;align-items:baseline;gap:14px;flex-wrap:wrap;margin-bottom:20px}
.free-callout .price-row .big{font-size:clamp(3rem,6vw,4.4rem);font-weight:800;line-height:1}
.free-callout .price-row .strike{font-size:1.3rem;text-decoration:line-through;opacity:.6}
.free-callout .price-row .small{opacity:.85;font-size:.95rem}
.free-callout .blurb{max-width:680px;opacity:.92;font-size:1.05rem;margin-bottom:28px}
.free-callout .perks{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px 22px;margin-bottom:30px;max-width:780px;padding:0}
.free-callout .perks li{list-style:none;display:flex;gap:10px;align-items:flex-start;opacity:.95}
.free-callout .perks li svg{flex-shrink:0;margin-top:3px}
.free-callout .btn-on-dark{background:#fff;color:var(--brand-dark);border:none}
.free-callout .btn-on-dark:hover{background:#f0fff5;color:var(--brand-dark)}
.free-callout .btn-ghost{background:transparent;color:#fff;border:1px solid rgba(255,255,255,.4)}
.free-callout .btn-ghost:hover{background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.55)}
.free-callout .future-note{margin-top:30px;padding:16px 18px;background:rgba(0,0,0,.18);border:1px solid rgba(255,255,255,.18);border-radius:12px;font-size:.92rem;opacity:.92;max-width:760px}
.testimonial{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:28px;height:100%}
.testimonial .stars{color:#f5a623;font-size:.9rem;margin-bottom:14px;letter-spacing:2px}
.testimonial .quote{color:var(--ink);font-size:1.02rem;line-height:1.6;margin-bottom:18px}
.testimonial .who{display:flex;align-items:center;gap:12px;color:var(--muted);font-size:.9rem}
.testimonial .who .avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#26c281,var(--brand-dark));color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem}
.cta-final{background:#0f1c2d;color:#fff;padding:80px 0;position:relative;overflow:hidden}
.cta-final::before{content:'';position:absolute;inset:0;background:radial-gradient(700px 320px at 80% 0%, color-mix(in srgb, var(--brand) 45%, transparent), transparent 60%),radial-gradient(500px 260px at 0% 100%, color-mix(in srgb, var(--brand) 20%, transparent), transparent 60%);pointer-events:none}
.cta-final h2{color:#fff;font-size:clamp(2rem,3.6vw,2.8rem);margin-bottom:14px}
.cta-final p{color:rgba(255,255,255,.78);max-width:560px;margin:0 auto 28px;font-size:1.05rem}
footer{background:#0a1422;color:#b7c1d4;padding:48px 0 26px;font-size:.92rem}
footer .brand{font-weight:800;color:#fff;font-size:1.2rem;text-decoration:none}
footer .brand .dot{color:rgba(255,255,255,.5)}
footer a{color:#fff;text-decoration:none}
footer a:hover{text-decoration:underline}
footer h6{color:#fff;font-size:.82rem;letter-spacing:.1em;text-transform:uppercase;margin-bottom:14px}
footer ul{list-style:none;padding:0;margin:0}
footer ul li{padding:4px 0;color:#b7c1d4}
.foot-bottom{border-top:1px solid rgba(255,255,255,.08);margin-top:34px;padding-top:18px;display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px;font-size:.85rem;color:#8c97ac}
@media(prefers-reduced-motion:no-preference){
  .reveal{opacity:0;transform:translateY(14px);animation:rev .8s ease forwards}
  .reveal.d1{animation-delay:.05s}.reveal.d2{animation-delay:.15s}.reveal.d3{animation-delay:.25s}
  @keyframes rev{to{opacity:1;transform:none}}
}
@media(max-width:767px){
  .hero{padding:60px 0 50px}
  .section{padding:60px 0}
  .free-callout{padding:38px 24px}
  .price-cards-grid{grid-template-columns:1fr}
}
.price-cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px}
.price-card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:32px 24px;display:flex;flex-direction:column;transition:transform .2s ease,box-shadow .2s ease}
.price-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
.price-card.featured{border:2px solid var(--brand);box-shadow:var(--shadow-glow)}
.price-card .badge-pop{display:inline-block;background:var(--brand);color:#fff;padding:4px 10px;border-radius:999px;font-size:.72rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;margin-bottom:12px}
.price-card .plan-name{font-weight:700;font-size:1.15rem;letter-spacing:-.01em;margin-bottom:12px}
.price-card .price{font-size:2.2rem;font-weight:800;letter-spacing:-.03em;margin-bottom:4px}
.price-card .price small{font-size:.95rem;color:var(--muted);font-weight:400}
.price-card .per{color:var(--muted);font-size:.88rem;margin-bottom:18px}
.price-card .feat-list{list-style:none;padding:0;margin:0 0 24px;flex:1}
.price-card .feat-list li{display:flex;gap:8px;align-items:flex-start;padding:5px 0;color:var(--ink-soft);font-size:.93rem}
.price-card .feat-list li::before{content:'\2713';color:var(--brand);font-weight:700;flex-shrink:0}
.price-card .trial-note{font-size:.85rem;color:var(--brand);font-weight:600;margin-bottom:14px}
</style>
</head>
<body data-variant="a">

<div class="nav-wrap">
  <nav class="navbar navbar-expand-md">
    <div class="container">
      <a class="brand" href="<?=base_url()?>">SmartSchool<span class="dot">.bd</span></a>
      <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav ms-auto align-items-md-center gap-md-3">
          <?php if ($show_feat): ?><li class="nav-item"><a class="nav-link" href="#features">Features</a></li><?php endif; ?>
          <?php if ($show_price): ?><li class="nav-item"><a class="nav-link" href="#free">Pricing</a></li><?php endif; ?>
          <?php if ($show_schools): ?><li class="nav-item"><a class="nav-link" href="#schools">Schools</a></li><?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="<?=htmlspecialchars($login)?>">Login</a></li>
          <li class="nav-item"><a class="btn btn-brand ms-md-2" href="<?=htmlspecialchars($signup)?>">Get started — free</a></li>
        </ul>
      </div>
    </div>
  </nav>
</div>

<section class="hero">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-7 reveal d1">
        <?php if ($eyeb): ?><span class="eyebrow"><span class="dot"></span><?=htmlspecialchars($eyeb)?></span><?php endif; ?>
        <h1><?=preg_replace('/(— on us\.?)$/u', '<span class="grad">$1</span>', htmlspecialchars($h1))?></h1>
        <?php if ($h1_bn): ?><p class="bn bn-tag"><?=htmlspecialchars($h1_bn)?></p><?php endif; ?>
        <?php if ($lead): ?><p class="lead"><?=nl2br(htmlspecialchars($lead))?></p><?php endif; ?>
        <div class="ctas">
          <a class="btn btn-brand btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?></a>
          <?php if ($cta2): ?><a class="btn btn-outline-soft btn-lg" href="#features"><?=htmlspecialchars($cta2)?></a><?php endif; ?>
        </div>
        <div class="meta-row">
          <span class="checks"><i>✓</i> 100% free right now</span>
          <span class="checks"><i>✓</i> No credit card</span>
          <span class="checks"><i>✓</i> Bengali + English</span>
        </div>
      </div>
      <div class="col-lg-5 reveal d2">
        <div class="hero-card">
          <span class="ribbon">100% free</span>
          <h5>Everything every Bangladeshi school needs</h5>
          <ul>
            <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Admissions, students, staff, classes</li>
            <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Attendance, exams, gradebook, report cards</li>
            <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Fee collection &amp; full accounting</li>
            <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Parent + student portals + SMS</li>
            <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Your school's own public website</li>
            <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Your own <code>school.smartschool.bd</code></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if ($show_feat): ?>
<section class="section" id="features">
  <div class="container">
    <div class="eyebrow-row">Features</div>
    <h2>Everything you'd expect from premium school software — included for free.</h2>
    <p class="lead">Built specifically for the way Bangladeshi schools actually operate. Bengali UI, BDT fees, parent SMS — out of the box.</p>
    <div class="row g-4">
      <div class="col-md-6 col-lg-4 reveal d1"><div class="feature"><div class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></div><h5>Admissions &amp; students</h5><p>Online admission form, document upload, ID generation, photo upload, class &amp; section assignment.</p></div></div>
      <div class="col-md-6 col-lg-4 reveal d2"><div class="feature"><div class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><h5>Attendance &amp; exams</h5><p>Daily attendance, exam scheduling, marks entry, gradebook, downloadable report card PDFs.</p></div></div>
      <div class="col-md-6 col-lg-4 reveal d3"><div class="feature"><div class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><h5>Fees &amp; accounting</h5><p>Fee invoices, online + offline collection, dues reports, full double-entry accounting and ledgers.</p></div></div>
      <div class="col-md-6 col-lg-4 reveal d1"><div class="feature"><div class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15 15 0 0 1 4 10 15 15 0 0 1-4 10 15 15 0 0 1-4-10 15 15 0 0 1 4-10z"/></svg></div><h5>Public school website</h5><p>Sliders, news, gallery, events, teacher pages — your school's own website without writing code.</p></div></div>
      <div class="col-md-6 col-lg-4 reveal d2"><div class="feature"><div class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg></div><h5>Parent &amp; student portals</h5><p>Parents see attendance, results, fees and notices. Students see homework, marks and live class links.</p></div></div>
      <div class="col-md-6 col-lg-4 reveal d3"><div class="feature"><div class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div><h5>Multi-tenant &amp; isolated</h5><p>Your data is fully separated from every other school by <code>branch_id</code>. No other school's admin can see your students.</p></div></div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($show_price && $pmode !== 'hidden'): ?>
<section class="section alt" id="free">
  <div class="container">
    <?php if ($pmode === 'tiers' && !empty($packages)): ?>
    <div class="eyebrow-row">Pricing</div>
    <h2><?=htmlspecialchars($ph)?></h2>
    <p class="lead">Start free, upgrade as your school grows. All prices in BDT.</p>
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
        <a class="btn btn-brand" href="<?=htmlspecialchars($signup)?>?plan=<?=urlencode($p->code)?>"><?= $price > 0 ? 'Choose ' . htmlspecialchars($p->name) : 'Start free' ?></a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="free-callout reveal d1">
      <div class="free-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        100% free — for every school, today
      </div>
      <h2><?=htmlspecialchars($ph)?> <span style="opacity:.8">৳0</span></h2>
      <div class="price-row">
        <div class="big">৳0<span style="font-size:1rem;font-weight:500;opacity:.8;margin-left:6px">/ school / month</span></div>
        <div class="strike">৳2,999</div>
        <div class="small">while we're growing</div>
      </div>
      <p class="blurb">SmartSchool.bd is fully free right now — every feature, every module, every tenant subdomain, every parent SMS. No tiers, no usage caps, no card required at signup.</p>
      <ul class="perks">
        <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Unlimited students &amp; staff</li>
        <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Every module unlocked</li>
        <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Your own subdomain</li>
        <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Parent + student SMS</li>
        <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Full accounting &amp; ledgers</li>
        <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Public school website CMS</li>
      </ul>
      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-on-dark btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?></a>
        <a class="btn btn-ghost btn-lg" href="<?=htmlspecialchars($login)?>">I already have one — login</a>
      </div>
      <?php if (!empty($pfn)): ?>
      <div class="future-note">
        <strong>A note about the future.</strong> <?=nl2br(htmlspecialchars($pfn))?>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($show_schools && $show_test): ?>
<section class="section" id="schools">
  <div class="container">
    <div class="eyebrow-row">Real schools, real traffic</div>
    <h2>Schools already running on SmartSchool.bd</h2>
    <p class="lead">Two live tenants today; yours can be next.</p>
    <div class="row g-4">
      <div class="col-md-6 reveal d1">
        <div class="testimonial">
          <div class="stars">★★★★★</div>
          <div class="quote">"We moved from paper attendance to SmartSchool.bd in a weekend. Parents now get an SMS the same morning if their child is absent — and we didn't pay a taka."</div>
          <div class="who"><span class="avatar">N</span><div>NGPS Academy<br><a href="https://ngps.smartschool.bd" target="_blank" rel="noopener" style="color:var(--brand-dark)">ngps.smartschool.bd</a></div></div>
        </div>
      </div>
      <div class="col-md-6 reveal d2">
        <div class="testimonial">
          <div class="stars">★★★★★</div>
          <div class="quote">"The Bengali UI made staff adoption painless. We were collecting fees online by week 2, with no licence fee in sight."</div>
          <div class="who"><span class="avatar">T</span><div>Test School<br><a href="https://test.smartschool.bd" target="_blank" rel="noopener" style="color:var(--brand-dark)">test.smartschool.bd</a></div></div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="cta-final text-center">
  <div class="container">
    <h2>Set up your school in 5 minutes — it costs you nothing.</h2>
    <p>Your own subdomain, every module unlocked, Bengali + English UI. No credit card required.</p>
    <a class="btn btn-brand btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?></a>
  </div>
</section>

<footer>
  <div class="container">
    <div class="row g-4">
      <div class="col-md-5">
        <a class="brand mb-2 d-inline-block" href="<?=base_url()?>">SmartSchool<span class="dot">.bd</span></a>
        <p class="mt-2 mb-0" style="max-width:380px">Free school management software built specifically for Bangladesh schools. Bengali UI, BDT fees, parent SMS, all included.</p>
      </div>
      <div class="col-6 col-md-3">
        <h6>Product</h6>
        <ul>
          <?php if ($show_feat): ?><li><a href="#features">Features</a></li><?php endif; ?>
          <?php if ($show_price): ?><li><a href="#free">Pricing</a></li><?php endif; ?>
          <li><a href="<?=htmlspecialchars($signup)?>">Sign up free</a></li>
          <li><a href="<?=htmlspecialchars($login)?>">Login</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-4">
        <h6>Live schools</h6>
        <ul>
          <li><a href="https://ngps.smartschool.bd" target="_blank" rel="noopener">ngps.smartschool.bd</a></li>
          <li><a href="https://test.smartschool.bd" target="_blank" rel="noopener">test.smartschool.bd</a></li>
          <li><a href="mailto:al.exbru69789@gmail.com">al.exbru69789@gmail.com</a></li>
        </ul>
      </div>
    </div>
    <div class="foot-bottom">
      <div>© <?=date('Y')?> SmartSchool.bd. Made in Bangladesh.</div>
      <div><a href="<?=base_url('home/privacy')?>">Privacy</a> · <a href="<?=base_url('home/terms')?>">Terms</a></div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
