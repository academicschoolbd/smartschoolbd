<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Landing variant E — "Corporate / Formal".
 *
 * Traditional, established look — serif headings (Playfair Display), navy
 * primary tone, brand colour reserved for accents and CTAs.  Suited to
 * government, military and long-established schools.  Reads exactly the
 * same fields off $s as variants A and B, plus the tiered packages array
 * when pricing_mode === 'tiers'.
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
<meta name="description" content="A formal, professional school management platform for Bangladesh's established institutions.">
<meta name="theme-color" content="#0c2447">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Source+Sans+3:wght@400;500;600;700&family=Hind+Siliguri:wght@400;500;600;700&display=swap">
<style>
:root{
  --brand:<?=htmlspecialchars($brand)?>;
  --navy:#0c2447;
  --navy-2:#142e57;
  --navy-soft:#37486a;
  --ink:#0c1a30;
  --ink-soft:#2a3a55;
  --muted:#6b7689;
  --line:#dbe1eb;
  --line-strong:#c5cfdf;
  --bg:#fff;
  --bg-soft:#f4f6fa;
  --bg-cream:#faf7f1;
  --gold:#b78a3a;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:'Source Sans 3','Hind Siliguri',Georgia,system-ui,sans-serif;color:var(--ink);background:var(--bg);line-height:1.65;-webkit-font-smoothing:antialiased;margin:0}
.bn{font-family:'Hind Siliguri','Noto Sans Bengali',sans-serif}
.serif{font-family:'Playfair Display',Georgia,serif;font-weight:600;letter-spacing:.005em}
.container{max-width:1140px;margin:0 auto;padding:0 24px}
a{color:var(--navy);text-decoration:none}
a:hover{color:var(--brand)}
h1,h2,h3,h4,h5{font-family:'Playfair Display',Georgia,serif;font-weight:600;color:var(--ink);margin:0;letter-spacing:.005em}
p{color:var(--ink-soft);margin:0}

/* Top utility bar */
.utility{background:var(--navy);color:#cbd6e6;font-size:.82rem}
.utility .row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;flex-wrap:wrap;gap:8px}
.utility a{color:#cbd6e6}
.utility a:hover{color:#fff}
.utility .left span{margin-right:18px}
.utility .left .sep{color:#4a5d80;margin:0 10px}

/* Nav */
.nav{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:100}
.nav .row{display:flex;align-items:center;justify-content:space-between;padding:18px 0}
.nav .brand{font-family:'Playfair Display',Georgia,serif;font-weight:700;font-size:1.25rem;color:var(--navy);letter-spacing:.01em}
.nav .brand .dot{color:var(--brand)}
.nav .links{display:flex;gap:28px;align-items:center;font-size:.95rem}
.nav .links a{color:var(--ink-soft);font-weight:500}
.nav .links a:hover{color:var(--navy)}

.btn{display:inline-flex;align-items:center;gap:8px;border:1px solid transparent;border-radius:2px;padding:10px 22px;font-weight:600;font-size:.93rem;cursor:pointer;transition:opacity .15s,background .15s,color .15s,border-color .15s;letter-spacing:.02em;text-transform:none}
.btn-primary{background:var(--brand);color:#fff;border-color:var(--brand)}
.btn-primary:hover{filter:brightness(.92);color:#fff}
.btn-navy{background:var(--navy);color:#fff;border-color:var(--navy)}
.btn-navy:hover{background:var(--navy-2);color:#fff}
.btn-outline{background:transparent;color:var(--navy);border-color:var(--line-strong)}
.btn-outline:hover{border-color:var(--navy);color:var(--navy)}
.btn-lg{padding:13px 28px;font-size:.98rem}

/* Hero */
.hero{padding:90px 0 80px;background:linear-gradient(180deg,var(--bg-cream) 0%,#fff 80%);border-bottom:1px solid var(--line);position:relative}
.hero::after{content:'';position:absolute;left:0;right:0;bottom:-1px;height:6px;background:linear-gradient(90deg,var(--navy) 0,var(--navy) 38%,var(--brand) 38%,var(--brand) 62%,var(--gold) 62%,var(--gold) 100%);opacity:.85}
.hero .grid{display:grid;grid-template-columns:1.2fr .9fr;gap:60px;align-items:center}
.hero .eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:.78rem;color:var(--brand);font-weight:700;letter-spacing:.16em;text-transform:uppercase;margin-bottom:22px;padding-bottom:8px;border-bottom:2px solid var(--brand);align-self:flex-start;width:fit-content}
.hero h1{font-size:clamp(2.4rem,5vw,3.6rem);line-height:1.15;margin-bottom:22px;color:var(--navy);max-width:18ch;letter-spacing:-.005em}
.hero h1 .accent{color:var(--brand);font-style:italic}
.hero .bn-tag{font-size:1.05rem;color:var(--navy-soft);margin-bottom:22px;max-width:520px;font-weight:500}
.hero .lead{font-size:1.1rem;color:var(--ink-soft);margin-bottom:32px;max-width:560px;line-height:1.7}
.hero .ctas{display:flex;flex-wrap:wrap;gap:14px;align-items:center}
.hero .crest{background:#fff;border:1px solid var(--line);padding:32px;border-radius:2px;box-shadow:0 16px 40px -20px rgba(12,36,71,.18);text-align:center;position:relative}
.hero .crest::before,.hero .crest::after{content:'';position:absolute;left:24px;right:24px;height:1px;background:var(--line)}
.hero .crest::before{top:14px}.hero .crest::after{bottom:14px}
.hero .crest .seal{width:80px;height:80px;border-radius:50%;background:var(--navy);color:#fff;display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-weight:700;font-size:1.8rem;margin:8px auto 16px;border:3px double var(--gold)}
.hero .crest h4{font-family:'Playfair Display',serif;font-size:1.2rem;color:var(--navy);margin-bottom:6px}
.hero .crest .est{font-size:.78rem;letter-spacing:.18em;text-transform:uppercase;color:var(--muted);margin-bottom:18px}
.hero .crest dl{margin:0;display:grid;grid-template-columns:1fr 1fr;gap:10px;text-align:left;border-top:1px solid var(--line);padding-top:16px}
.hero .crest dt{font-size:.74rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);font-weight:600}
.hero .crest dd{margin:0;font-family:'Playfair Display',serif;font-weight:600;color:var(--navy);font-size:1.1rem}

/* Section primitives */
.section{padding:90px 0;border-bottom:1px solid var(--line)}
.section.alt{background:var(--bg-soft)}
.section .head{margin-bottom:54px;max-width:680px;text-align:center;margin-left:auto;margin-right:auto}
.section .eyebrow{font-size:.78rem;font-weight:700;letter-spacing:.16em;color:var(--brand);text-transform:uppercase;margin-bottom:14px;display:inline-block}
.section .eyebrow::before,.section .eyebrow::after{content:'';display:inline-block;width:24px;height:1px;background:var(--line-strong);vertical-align:middle;margin:0 12px}
.section h2{font-size:clamp(1.9rem,3.6vw,2.6rem);line-height:1.2;margin-bottom:14px;color:var(--navy)}
.section .lead-p{font-size:1.05rem;color:var(--ink-soft);max-width:580px;margin:0 auto;line-height:1.7}

/* Features — formal columns with rule lines */
.feat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0;border-top:1px solid var(--line);border-left:1px solid var(--line)}
.feat-grid .item{padding:36px 32px;border-right:1px solid var(--line);border-bottom:1px solid var(--line);background:#fff;position:relative}
.feat-grid .item .num{font-family:'Playfair Display',serif;font-style:italic;font-size:1.6rem;color:var(--brand);margin-bottom:14px;display:block;font-weight:600}
.feat-grid .item h5{font-family:'Playfair Display',serif;font-size:1.15rem;margin-bottom:10px;color:var(--navy)}
.feat-grid .item p{font-size:.95rem;color:var(--ink-soft);line-height:1.65}

/* Pricing — free callout */
.price-block{background:#fff;border:1px solid var(--line);padding:48px;display:grid;grid-template-columns:1.1fr 1fr;gap:60px;align-items:center;border-top:6px solid var(--brand)}
.price-block .badge{display:inline-block;font-size:.78rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--brand);border:1px solid var(--brand);padding:5px 12px;margin-bottom:22px}
.price-block h2{font-size:clamp(2rem,4vw,2.8rem);line-height:1.15;margin-bottom:18px;color:var(--navy)}
.price-block .big-price{font-family:'Playfair Display',serif;font-size:clamp(4.5rem,11vw,7rem);font-weight:600;letter-spacing:-.02em;line-height:1;color:var(--navy);margin-bottom:14px}
.price-block .big-price .unit{font-size:1rem;font-weight:500;color:var(--muted);margin-left:8px;display:inline-block;vertical-align:middle;font-family:'Source Sans 3',sans-serif}
.price-block .strike{color:var(--muted);font-size:1rem;text-decoration:line-through;margin-bottom:30px;display:block}
.price-block .perks{list-style:none;padding:0;margin:0 0 24px;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px 24px}
.price-block .perks li{display:flex;gap:10px;align-items:flex-start;color:var(--ink-soft);font-size:.95rem}
.price-block .perks li svg{color:var(--brand);flex-shrink:0;margin-top:3px}
.price-block .future-note{margin-top:24px;padding:18px 20px;background:var(--bg-cream);border-left:3px solid var(--brand);font-size:.92rem;color:var(--ink-soft);line-height:1.6}
.price-block .future-note strong{color:var(--navy)}

/* Pricing — tier cards */
.price-cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px}
.price-card{background:#fff;border:1px solid var(--line);padding:36px 28px;display:flex;flex-direction:column;border-top:4px solid var(--navy);transition:box-shadow .2s,transform .2s}
.price-card:hover{box-shadow:0 16px 40px -20px rgba(12,36,71,.18);transform:translateY(-2px)}
.price-card.featured{border-top-color:var(--brand)}
.price-card .badge-pop{display:inline-block;background:var(--brand);color:#fff;padding:4px 14px;font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;margin-bottom:14px;align-self:flex-start}
.price-card .plan-name{font-family:'Playfair Display',serif;font-weight:600;font-size:1.25rem;color:var(--navy);margin-bottom:14px}
.price-card .price{font-family:'Playfair Display',serif;font-size:2.4rem;font-weight:600;letter-spacing:-.01em;margin-bottom:4px;color:var(--navy)}
.price-card .price small{font-size:.92rem;color:var(--muted);font-weight:400;font-family:'Source Sans 3',sans-serif}
.price-card .per{color:var(--muted);font-size:.85rem;margin-bottom:18px}
.price-card .feat-list{list-style:none;padding:0;margin:0 0 24px;flex:1;border-top:1px solid var(--line);padding-top:18px}
.price-card .feat-list li{display:flex;gap:8px;align-items:flex-start;padding:6px 0;color:var(--ink-soft);font-size:.93rem}
.price-card .feat-list li::before{content:'\2713';color:var(--brand);font-weight:700;flex-shrink:0}
.price-card .trial-note{font-size:.85rem;color:var(--brand);font-weight:600;margin-bottom:14px}

/* Testimonials */
.t-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:32px}
.t-grid .t{padding:36px 32px;background:#fff;border:1px solid var(--line);position:relative}
.t-grid .t::before{content:'';position:absolute;top:0;left:0;width:48px;height:3px;background:var(--brand)}
.t-grid .t .mark{font-family:'Playfair Display',serif;font-size:3rem;line-height:1;color:var(--brand);margin-bottom:-6px;display:block}
.t-grid .t .q{font-family:'Playfair Display',serif;font-style:italic;font-size:1.1rem;line-height:1.55;color:var(--navy);margin-bottom:22px;font-weight:500}
.t-grid .t .by{font-size:.9rem;color:var(--ink-soft);display:flex;align-items:center;gap:12px;border-top:1px solid var(--line);padding-top:16px}
.t-grid .t .by .av{width:38px;height:38px;border-radius:50%;background:var(--navy);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;font-family:'Playfair Display',serif}
.t-grid .t .by a{color:var(--brand);font-weight:600}

/* Final CTA */
.cta-strip{padding:90px 0;text-align:center;background:var(--navy);color:#e9eef7;position:relative}
.cta-strip::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:linear-gradient(90deg,var(--brand) 0,var(--brand) 50%,var(--gold) 50%,var(--gold) 100%)}
.cta-strip h2{font-size:clamp(2rem,4.4vw,3rem);line-height:1.15;margin-bottom:18px;max-width:20ch;margin-left:auto;margin-right:auto;color:#fff}
.cta-strip p{color:#bcc7dc;font-size:1.05rem;margin-bottom:30px;max-width:540px;margin-left:auto;margin-right:auto;line-height:1.7}

/* Footer */
footer{padding:56px 0 32px;background:#0a1f3d;color:#aab6cc}
footer a{color:#cbd6e6}
footer a:hover{color:#fff}
footer .row{display:grid;grid-template-columns:2fr 1fr 1fr;gap:48px;margin-bottom:32px}
footer .col h6{font-family:'Playfair Display',serif;font-size:.95rem;font-weight:600;color:#fff;margin-bottom:14px;letter-spacing:.01em}
footer .col ul{list-style:none;padding:0;margin:0}
footer .col ul li{padding:5px 0}
footer .col ul li a{font-size:.93rem}
footer .brand{font-family:'Playfair Display',serif;font-weight:700;font-size:1.1rem;color:#fff;margin-bottom:10px;display:inline-block}
footer .brand .dot{color:var(--brand)}
footer .blurb{color:#aab6cc;font-size:.92rem;max-width:340px;line-height:1.6}
footer .bot{display:flex;flex-wrap:wrap;justify-content:space-between;border-top:1px solid #1c3360;padding-top:24px;font-size:.85rem;color:#8e9ab2;gap:8px}

@media(prefers-reduced-motion:no-preference){
  .reveal{opacity:0;transform:translateY(10px);animation:rev .9s ease forwards}
  .reveal.d1{animation-delay:.05s}.reveal.d2{animation-delay:.18s}
  @keyframes rev{to{opacity:1;transform:none}}
}
@media(max-width:900px){
  .hero .grid{grid-template-columns:1fr;gap:48px}
  .feat-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:767px){
  .utility .left span:not(:first-child){display:none}
  .hero{padding:60px 0 56px}
  .section{padding:64px 0}
  .nav .links a:not(.btn){display:none}
  .price-block{grid-template-columns:1fr;gap:32px;padding:32px 24px}
  .feat-grid{grid-template-columns:1fr}
  footer .row{grid-template-columns:1fr;gap:32px}
  .price-cards-grid{grid-template-columns:1fr}
}
</style>
</head>
<body data-variant="e">

<div class="utility">
  <div class="container">
    <div class="row">
      <div class="left">
        <span><i></i>Established for Bangladeshi institutions</span>
        <span class="sep">·</span>
        <span>Bengali &amp; English UI</span>
        <span class="sep">·</span>
        <span>Government &amp; private schools</span>
      </div>
      <div class="right">
        <a href="<?=htmlspecialchars($login)?>">Institution login</a>
      </div>
    </div>
  </div>
</div>

<nav class="nav">
  <div class="container">
    <div class="row">
      <a class="brand" href="<?=base_url()?>">SmartSchool<span class="dot">.bd</span></a>
      <div class="links">
        <?php if ($show_feat): ?><a href="#features">Features</a><?php endif; ?>
        <?php if ($show_price): ?><a href="#pricing">Pricing</a><?php endif; ?>
        <?php if ($show_schools): ?><a href="#schools">Institutions</a><?php endif; ?>
        <a href="<?=htmlspecialchars($login)?>">Login</a>
        <a class="btn btn-primary" href="<?=htmlspecialchars($signup)?>">Register your school</a>
      </div>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <div class="grid">
      <div class="reveal d1">
        <?php if ($eyeb): ?><div class="eyebrow"><?=htmlspecialchars($eyeb)?></div><?php endif; ?>
        <h1><?=htmlspecialchars($h1)?></h1>
        <?php if ($h1_bn): ?><p class="bn bn-tag"><?=htmlspecialchars($h1_bn)?></p><?php endif; ?>
        <?php if ($lead): ?><p class="lead"><?=htmlspecialchars($lead)?></p><?php endif; ?>
        <div class="ctas">
          <a class="btn btn-primary btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?></a>
          <a class="btn btn-outline btn-lg" href="#features"><?=htmlspecialchars($cta2)?></a>
        </div>
      </div>
      <div class="reveal d2">
        <div class="crest">
          <div class="seal">S</div>
          <h4>SmartSchool.bd</h4>
          <div class="est">Trusted institutional platform</div>
          <dl>
            <dt>Schools</dt><dd>2 live</dd>
            <dt>Languages</dt><dd>BN · EN</dd>
            <dt>Setup</dt><dd>5 min</dd>
            <dt>Cost today</dt><dd>৳0</dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if ($show_feat): ?>
<section class="section" id="features">
  <div class="container">
    <div class="head reveal d1">
      <div class="eyebrow">Capabilities</div>
      <h2>A complete administrative platform.</h2>
      <p class="lead-p">Built for the rigour of formal Bangladeshi institutions — from admission registers to audited accounts.</p>
    </div>
    <div class="feat-grid">
      <div class="item"><span class="num">i.</span><h5>Admissions &amp; students</h5><p>Online admission form, documents, ID generation, class &amp; section assignment.</p></div>
      <div class="item"><span class="num">ii.</span><h5>Attendance &amp; examinations</h5><p>Daily attendance, examination schedule, marks entry, gradebook, official report cards.</p></div>
      <div class="item"><span class="num">iii.</span><h5>Fees &amp; accounting</h5><p>Fee invoices, online &amp; offline collection, dues, full double-entry institutional accounting.</p></div>
      <div class="item"><span class="num">iv.</span><h5>Public school website</h5><p>Notices, prospectus, photo gallery, events, faculty pages — all without writing code.</p></div>
      <div class="item"><span class="num">v.</span><h5>Parent &amp; student portals</h5><p>Parents review attendance, results, fees and notices. Students access homework and marks.</p></div>
      <div class="item"><span class="num">vi.</span><h5>Multi-tenant &amp; isolated</h5><p>Each institution's data is fully separated with strict access controls. No cross-tenant access.</p></div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($show_price && $pmode !== 'hidden'): ?>
<section class="section alt" id="pricing">
  <div class="container">
    <?php if ($pmode === 'tiers' && !empty($packages)): ?>
    <div class="head reveal d1">
      <div class="eyebrow">Pricing schedule</div>
      <h2><?=htmlspecialchars($ph)?></h2>
      <p class="lead-p">Fees are quoted per institution in BDT. Begin on the free tier; upgrade as your school grows.</p>
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
        <?php if ($featured): ?><span class="badge-pop">Recommended</span><?php endif; ?>
        <div class="plan-name"><?=htmlspecialchars($p->name)?></div>
        <div class="price"><?php if ($price > 0): ?>&#2547;<?=number_format($price)?><small><?=htmlspecialchars($per)?></small><?php else: ?>Free<?php endif; ?></div>
        <?php if ($price > 0): ?><div class="per">per institution <?=htmlspecialchars($per)?></div><?php endif; ?>
        <?php if ((int)$p->trial_days > 0): ?><div class="trial-note"><?=(int)$p->trial_days?>-day free trial</div><?php endif; ?>
        <ul class="feat-list">
          <?php foreach ($highlights as $h): ?><li><?=htmlspecialchars($h)?></li><?php endforeach; ?>
        </ul>
        <a class="btn btn-primary" href="<?=htmlspecialchars($signup)?>?plan=<?=urlencode($p->code)?>"><?= $price > 0 ? 'Choose ' . htmlspecialchars($p->name) : 'Begin free' ?></a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="price-block reveal d1">
      <div>
        <div class="badge">Complimentary tier · Today</div>
        <h2><?=htmlspecialchars($ph)?></h2>
        <div class="big-price">৳0<span class="unit">/ school / month</span></div>
        <span class="strike">Forthcoming tier: ৳2,999 / month</span>
        <div style="display:flex;gap:14px;flex-wrap:wrap">
          <a class="btn btn-primary btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?></a>
          <a class="btn btn-outline btn-lg" href="<?=htmlspecialchars($login)?>">Institution login</a>
        </div>
      </div>
      <div>
        <ul class="perks">
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Unlimited students &amp; staff</li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Every module unlocked</li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Your institutional subdomain</li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Parent &amp; student SMS</li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Full audited accounting</li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Public website CMS</li>
        </ul>
        <?php if (!empty($pfn)): ?>
        <div class="future-note"><strong>A note on future plans.</strong> <?=htmlspecialchars($pfn)?></div>
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
      <div class="eyebrow">Institutions</div>
      <h2>Trusted by live institutions.</h2>
      <p class="lead-p">Two schools currently serving parents and pupils on the platform.</p>
    </div>
    <div class="t-grid">
      <div class="t reveal d1">
        <span class="mark">"</span>
        <p class="q">We moved from paper attendance to SmartSchool.bd in a weekend. Parents now receive an SMS the same morning if their child is absent — and we paid nothing.</p>
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
    <h2>Register your institution today.</h2>
    <p>Set up in 5 minutes. Full module access. Bengali &amp; English. No credit card required.</p>
    <a class="btn btn-primary btn-lg" href="<?=htmlspecialchars($signup)?>"><?=htmlspecialchars($cta1)?></a>
  </div>
</section>

<footer>
  <div class="container">
    <div class="row">
      <div class="col">
        <a class="brand" href="<?=base_url()?>">SmartSchool<span class="dot">.bd</span></a>
        <p class="blurb">A formal school management platform built for Bangladesh's institutions. Bengali UI, BDT fees, parent SMS, audited accounting — all included.</p>
      </div>
      <div class="col">
        <h6>Platform</h6>
        <ul>
          <?php if ($show_feat): ?><li><a href="#features">Capabilities</a></li><?php endif; ?>
          <?php if ($show_price): ?><li><a href="#pricing">Pricing schedule</a></li><?php endif; ?>
          <li><a href="<?=htmlspecialchars($signup)?>">Register your school</a></li>
          <li><a href="<?=htmlspecialchars($login)?>">Institution login</a></li>
        </ul>
      </div>
      <div class="col">
        <h6>Live institutions</h6>
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
