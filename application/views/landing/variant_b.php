<?php
defined('BASEPATH') or exit('No direct script access allowed');
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
<meta name="description" content="Free school management software for Bangladesh schools. Clean, fast, no nonsense.">
<meta name="theme-color" content="<?=htmlspecialchars($brand)?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap">
<style>
:root{
  --brand:<?=htmlspecialchars($brand)?>;
  --ink:#0b0d12;
  --ink-soft:#262a33;
  --muted:#5d6573;
  --line:#ececef;
  --bg:#ffffff;
  --bg-soft:#fafafa;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:'Manrope','Hind Siliguri',system-ui,-apple-system,sans-serif;color:var(--ink);background:var(--bg);line-height:1.6;-webkit-font-smoothing:antialiased;margin:0}
.bn{font-family:'Hind Siliguri','Noto Sans Bengali',sans-serif}
.container{max-width:1100px;margin:0 auto;padding:0 24px}
a{color:var(--ink);text-decoration:none}
a:hover{color:var(--brand)}
h1,h2,h3,h4,h5{font-weight:700;letter-spacing:-.025em;color:var(--ink);margin:0}
p{color:var(--ink-soft);margin:0}

/* Nav */
.nav{position:sticky;top:0;background:rgba(255,255,255,.9);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border-bottom:1px solid var(--line);z-index:100}
.nav .row{display:flex;align-items:center;justify-content:space-between;padding:18px 0}
.nav .brand{font-weight:800;font-size:1.05rem;letter-spacing:-.02em;color:var(--ink)}
.nav .brand .dot{color:var(--brand)}
.nav .links{display:flex;gap:30px;align-items:center;font-size:.92rem;color:var(--ink-soft)}
.nav .links a{color:var(--ink-soft);font-weight:500}
.nav .links a:hover{color:var(--ink)}
.btn{display:inline-flex;align-items:center;gap:8px;border:1px solid transparent;border-radius:6px;padding:10px 18px;font-weight:600;font-size:.92rem;cursor:pointer;transition:opacity .15s,background .15s,color .15s}
.btn-primary{background:var(--brand);color:#fff;border-color:var(--brand)}
.btn-primary:hover{background:var(--brand);color:#fff;opacity:.88}
.btn-ghost{background:transparent;color:var(--ink);border-color:var(--line)}
.btn-ghost:hover{background:var(--bg-soft);color:var(--ink)}
.btn-link{background:transparent;border:none;color:var(--ink);font-weight:600;padding:10px 0;border-bottom:1px solid var(--ink)}
.btn-link:hover{color:var(--brand);border-color:var(--brand)}
.btn-lg{padding:14px 24px;font-size:1rem}

/* Hero */
.hero{padding:120px 0 100px;border-bottom:1px solid var(--line)}
.hero .eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:.85rem;color:var(--muted);font-weight:500;margin-bottom:24px;padding:5px 0}
.hero .eyebrow .dot{width:8px;height:8px;border-radius:50%;background:var(--brand)}
.hero h1{font-size:clamp(2.5rem,5.5vw,4.4rem);line-height:1;margin-bottom:24px;max-width:18ch;letter-spacing:-.04em}
.hero h1 .accent{color:var(--brand)}
.hero .bn-tag{font-size:1.05rem;color:var(--muted);margin-bottom:24px;max-width:520px}
.hero .lead{font-size:1.15rem;color:var(--muted);margin-bottom:40px;max-width:600px;line-height:1.6}
.hero .ctas{display:flex;flex-wrap:wrap;gap:14px;align-items:center}
.hero .meta-row{margin-top:60px;display:flex;flex-wrap:wrap;gap:48px;align-items:flex-start;padding-top:32px;border-top:1px solid var(--line)}
.hero .stat .num{font-size:1.8rem;font-weight:700;letter-spacing:-.03em;color:var(--ink);line-height:1.1}
.hero .stat .lbl{font-size:.82rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-top:4px}

/* Section primitives */
.section{padding:96px 0;border-bottom:1px solid var(--line)}
.section.alt{background:var(--bg-soft)}
.section .head{margin-bottom:64px;max-width:680px}
.section .eyebrow{font-size:.78rem;font-weight:600;letter-spacing:.16em;color:var(--brand);text-transform:uppercase;margin-bottom:14px}
.section h2{font-size:clamp(1.8rem,3.6vw,2.6rem);line-height:1.15;letter-spacing:-.03em;margin-bottom:14px}
.section .lead-p{font-size:1.05rem;color:var(--muted);max-width:560px}

/* Features — minimal list-style cards */
.feat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:0;border-top:1px solid var(--line);border-left:1px solid var(--line)}
.feat-grid .item{padding:36px 32px;border-right:1px solid var(--line);border-bottom:1px solid var(--line);background:#fff;transition:background .2s}
.feat-grid .item:hover{background:var(--bg-soft)}
.feat-grid .item .num{font-size:.78rem;font-weight:700;color:var(--brand);letter-spacing:.12em;margin-bottom:18px;display:block}
.feat-grid .item h5{font-size:1.1rem;margin-bottom:10px;letter-spacing:-.02em}
.feat-grid .item p{font-size:.95rem;color:var(--muted);line-height:1.6}

/* Price block — minimal, white, big number */
.price-block{display:grid;grid-template-columns:1.1fr 1fr;gap:80px;align-items:center}
.price-block .badge{display:inline-block;font-size:.78rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--brand);background:rgba(31,157,85,.08);padding:6px 12px;border-radius:4px;margin-bottom:24px}
.price-block h2{font-size:clamp(2rem,4vw,3rem);line-height:1.1;margin-bottom:18px;letter-spacing:-.03em}
.price-block .big-price{font-size:clamp(5rem,12vw,8rem);font-weight:800;letter-spacing:-.06em;line-height:1;color:var(--ink);margin-bottom:14px}
.price-block .big-price .unit{font-size:1rem;font-weight:500;color:var(--muted);letter-spacing:0;margin-left:8px;display:inline-block;vertical-align:middle}
.price-block .strike{color:var(--muted);font-size:1rem;text-decoration:line-through;margin-bottom:30px;display:block}
.price-block .perks{list-style:none;padding:0;margin:0 0 32px;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px 24px}
.price-block .perks li{display:flex;gap:8px;align-items:flex-start;color:var(--ink-soft);font-size:.95rem}
.price-block .perks li svg{color:var(--brand);flex-shrink:0;margin-top:3px}
.price-block .future-note{margin-top:24px;padding:18px 20px;background:var(--bg-soft);border-left:3px solid var(--brand);border-radius:0 4px 4px 0;font-size:.92rem;color:var(--ink-soft);line-height:1.55}
.price-block .future-note strong{color:var(--ink)}

/* Testimonials — quoted, minimal */
.t-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:40px}
.t-grid .t{padding:0}
.t-grid .t .mark{font-size:3rem;line-height:1;color:var(--brand);font-family:Georgia,serif;margin-bottom:-10px;display:block}
.t-grid .t .q{font-size:1.1rem;line-height:1.55;color:var(--ink);margin-bottom:20px;letter-spacing:-.01em}
.t-grid .t .by{font-size:.92rem;color:var(--muted);display:flex;align-items:center;gap:10px}
.t-grid .t .by .av{width:32px;height:32px;border-radius:50%;background:var(--ink);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.78rem}
.t-grid .t .by a{color:var(--brand);font-weight:600}

/* Final CTA — minimal stripe */
.cta-strip{padding:96px 0;border-bottom:1px solid var(--line);text-align:center}
.cta-strip h2{font-size:clamp(2rem,4.4vw,3.2rem);line-height:1.05;letter-spacing:-.04em;margin-bottom:18px;max-width:18ch;margin-left:auto;margin-right:auto}
.cta-strip p{color:var(--muted);font-size:1.05rem;margin-bottom:30px;max-width:520px;margin-left:auto;margin-right:auto}

/* Footer */
footer{padding:48px 0 32px}
footer .row{display:grid;grid-template-columns:2fr 1fr 1fr;gap:48px;margin-bottom:32px}
footer .col h6{font-size:.78rem;font-weight:700;letter-spacing:.12em;color:var(--muted);text-transform:uppercase;margin-bottom:14px}
footer .col ul{list-style:none;padding:0;margin:0}
footer .col ul li{padding:5px 0}
footer .col ul li a{color:var(--ink-soft);font-size:.95rem}
footer .col ul li a:hover{color:var(--ink)}
footer .brand{display:inline-block;font-weight:800;font-size:1.05rem;letter-spacing:-.02em;color:var(--ink);margin-bottom:10px}
footer .brand .dot{color:var(--brand)}
footer .blurb{color:var(--muted);font-size:.92rem;max-width:340px;line-height:1.55}
footer .bot{display:flex;flex-wrap:wrap;justify-content:space-between;border-top:1px solid var(--line);padding-top:24px;font-size:.85rem;color:var(--muted);gap:8px}

@media(prefers-reduced-motion:no-preference){
  .reveal{opacity:0;transform:translateY(10px);animation:rev .9s ease forwards}
  .reveal.d1{animation-delay:.05s}.reveal.d2{animation-delay:.18s}
  @keyframes rev{to{opacity:1;transform:none}}
}
@media(max-width:767px){
  .hero{padding:80px 0 60px}
  .section{padding:64px 0}
  .nav .links a:not(.btn){display:none}
  .price-block{grid-template-columns:1fr;gap:40px}
  footer .row{grid-template-columns:1fr;gap:32px}
  .hero .meta-row{gap:24px}
  .price-cards-grid{grid-template-columns:1fr}
}
.price-cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px}
.price-card{background:#fff;border:1px solid var(--line);border-radius:8px;padding:32px 24px;display:flex;flex-direction:column;transition:transform .15s,box-shadow .15s}
.price-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.06)}
.price-card.featured{border:2px solid var(--brand)}
.price-card .badge-pop{display:inline-block;background:var(--brand);color:#fff;padding:4px 10px;border-radius:999px;font-size:.72rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;margin-bottom:12px}
.price-card .plan-name{font-weight:700;font-size:1.1rem;margin-bottom:12px}
.price-card .price{font-size:2.2rem;font-weight:700;letter-spacing:-.03em;margin-bottom:4px}
.price-card .price small{font-size:.95rem;color:var(--muted);font-weight:400}
.price-card .per{color:var(--muted);font-size:.88rem;margin-bottom:18px}
.price-card .feat-list{list-style:none;padding:0;margin:0 0 24px;flex:1}
.price-card .feat-list li{display:flex;gap:8px;align-items:flex-start;padding:5px 0;color:var(--ink-soft);font-size:.93rem}
.price-card .feat-list li::before{content:'\2713';color:var(--brand);font-weight:700;flex-shrink:0}
.price-card .trial-note{font-size:.85rem;color:var(--brand);font-weight:600;margin-bottom:14px}
</style>
</head>
<body data-variant="b">

<nav class="nav">
  <div class="container">
    <div class="row">
      <a class="brand" href="<?=base_url()?>">SmartSchool<span class="dot">.bd</span></a>
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
  <div class="container">
    <div class="reveal d1">
      <?php if ($eyeb): ?><div class="eyebrow"><span class="dot"></span><?=htmlspecialchars($eyeb)?></div><?php endif; ?>
      <h1><?=htmlspecialchars(str_replace('— on us.', '', $h1))?><br><span class="accent">Free for every Bangladeshi school.</span></h1>
      <?php if ($h1_bn): ?><p class="bn bn-tag"><?=htmlspecialchars($h1_bn)?></p><?php endif; ?>
      <?php if ($lead): ?><p class="lead"><?=htmlspecialchars($lead)?></p><?php endif; ?>
      <div class="ctas">
        <a class="btn btn-primary btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?></a>
        <a class="btn btn-link" href="#features"><?=htmlspecialchars($cta2)?> →</a>
      </div>
      <div class="meta-row">
        <div class="stat"><div class="num">2</div><div class="lbl">Live schools</div></div>
        <div class="stat"><div class="num">৳0</div><div class="lbl">Per month, today</div></div>
        <div class="stat"><div class="num">5 min</div><div class="lbl">To go live</div></div>
        <div class="stat"><div class="num">BN + EN</div><div class="lbl">Languages</div></div>
      </div>
    </div>
  </div>
</section>

<?php if ($show_feat): ?>
<section class="section" id="features">
  <div class="container">
    <div class="head reveal d1">
      <div class="eyebrow">Features</div>
      <h2>Everything you'd expect from premium school software — included for free.</h2>
      <p class="lead-p">Built for the way Bangladeshi schools actually operate.</p>
    </div>
    <div class="feat-grid">
      <div class="item"><span class="num">01</span><h5>Admissions &amp; students</h5><p>Online admission form, documents, ID generation, class &amp; section assignment.</p></div>
      <div class="item"><span class="num">02</span><h5>Attendance &amp; exams</h5><p>Daily attendance, exam schedule, marks entry, gradebook, downloadable report cards.</p></div>
      <div class="item"><span class="num">03</span><h5>Fees &amp; accounting</h5><p>Fee invoices, online + offline collection, dues, full double-entry accounting.</p></div>
      <div class="item"><span class="num">04</span><h5>Public school website</h5><p>Sliders, news, gallery, events, teacher pages — without writing code.</p></div>
      <div class="item"><span class="num">05</span><h5>Parent &amp; student portals</h5><p>Parents see attendance, results, fees, notices. Students see homework, marks, live classes.</p></div>
      <div class="item"><span class="num">06</span><h5>Multi-tenant &amp; isolated</h5><p>Your data is fully separated from every other school. Zero cross-tenant access.</p></div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($show_price && $pmode !== 'hidden'): ?>
<section class="section alt" id="pricing">
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
      <div>
        <div class="badge">100% free, today</div>
        <h2><?=htmlspecialchars($ph)?></h2>
        <div class="big-price">৳0<span class="unit">/ school / month</span></div>
        <span class="strike">Future tier: ৳2,999 / month</span>
        <div class="d-flex" style="display:flex;gap:14px;flex-wrap:wrap">
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
    <h2>Set up your school in 5 minutes. It costs you nothing.</h2>
    <p>Your own subdomain, every module unlocked, Bengali + English UI. No credit card.</p>
    <a class="btn btn-primary btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?> →</a>
  </div>
</section>

<footer>
  <div class="container">
    <div class="row">
      <div class="col">
        <a class="brand" href="<?=base_url()?>">SmartSchool<span class="dot">.bd</span></a>
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
