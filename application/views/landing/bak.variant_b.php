<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| ULTIMATE SAAS LANDING SYSTEM ARCHITECTURE Engine v4.5
|--------------------------------------------------------------------------
*/
$signup = $signup_url ?? base_url('signup');
$login  = $login_url  ?? base_url('authentication');
$s      = $s ?? (object)[];

$brand        = $s->brand_color ?? '#059669'; 
$h1           = $s->hero_h1     ?? 'The Next-Gen Operating System for Schools.';
$h1_bn        = $s->hero_bn     ?? 'আধুনিক বেন্টো কার্ড মডিউল ও ইন্টারেক্টিভ ইকোসিস্টেমে আপনার সম্পূর্ণ প্রতিষ্ঠান';
$lead         = $s->hero_lead   ?? 'Deploy a fully dynamic institution website, unlock premium native apps, automate automated accounting ledgers, and manage transcripts flawlessly.';
$eyeb         = $s->hero_eyebrow ?? 'SmartSchool Enterprise v4.5 Stable';
$cta_primary  = $s->cta_primary_label   ?? 'ইনস্ট্যান্ট ফ্রি অ্যাকাউন্ট তৈরি করুন';
$cta_sec      = $s->cta_secondary_label ?? 'লাইভ মডিউল ডেমো দেখুন';
$ph           = $s->pricing_headline    ?? 'One Fully Unlocked Core. Continuous Zero License Fees.';
$pmode        = $s->pricing_mode        ?? 'free';
$pfn          = $s->pricing_future_note ?? 'Registered domestic educational institutions will experience zero subscription costs for baseline infrastructure portals.';

$show_feat    = !isset($s->show_features)     || (int)$s->show_features === 1;
$show_price   = !isset($s->show_pricing)      || (int)$s->show_pricing === 1;
$show_test    = !isset($s->show_testimonials) || (int)$s->show_testimonials === 1;
$show_schools = !isset($s->show_schools)      || (int)$s->show_schools === 1;

$packages       = (isset($packages) && is_array($packages)) ? $packages : [];
$feature_labels = (isset($feature_labels) && is_array($feature_labels)) ? $feature_labels : [];
?>
<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($page_title ?? 'SmartSchool.bd | আল্ট্রা-মডার্ন ইন্টারেক্টিভ স্কুল ইআরপি সিস্টেম') ?></title>
    <meta name="description" content="বেন্টো কার্ড ইন্টারেক্টিভ ইন্টারফেস, প্রবেশপত্র মডিউল, রেজাল্ট শিট প্রসেসর এবং অটোমেটেড এসএমএস গেটওয়েসহ সম্পূর্ণ আধুনিক স্কুল সফটওয়্যার।">
    <meta name="theme-color" content="<?= htmlspecialchars($brand) ?>">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand: <?= htmlspecialchars($brand) ?>;
            --brand-rgb: 5, 150, 69;
            --brand-soft: rgba(5, 150, 105, 0.04);
            --brand-glow: rgba(5, 150, 105, 0.12);
            --brand-gradient: linear-gradient(135deg, #059669 0%, #047857 100%);
            --ink: #0b0f19;
            --ink-soft: #334155;
            --muted: #64748b;
            --line: #f1f5f9;
            --border-card: #e2e8f0;
            --bg: #ffffff;
            --bg-soft: #f8fafc;
            
            /* High-fidelity fluid diffusion system shadows */
            --shadow-fluid-sm: 0 2px 8px -2px rgba(11, 15, 25, 0.04);
            --shadow-fluid-md: 0 16px 40px -12px rgba(11, 15, 25, 0.06);
            --shadow-fluid-lg: 0 40px 80px -20px rgba(11, 15, 25, 0.1);
            
            --radius-card-sm: 12px;
            --radius-card-md: 20px;
            --radius-card-lg: 32px;
            
            /* Standard Motion Variables */
            --transition-smooth: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            --transition-bounce: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        * { box-sizing: border-box; padding: 0; margin: 0; }
        html { scroll-behavior: smooth; font-size: 16px; -webkit-text-size-adjust: 100%; }
        body { 
            font-family: 'Inter', 'Hind Siliguri', system-ui, sans-serif; 
            color: var(--ink); 
            background: var(--bg); 
            line-height: 1.6; 
            -webkit-font-smoothing: antialiased; 
            overflow-x: hidden;
        }
        
        .bn { font-family: 'Hind Siliguri', sans-serif; }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 24px; position: relative; z-index: 2; }
        
        a { color: var(--ink); text-decoration: none; transition: var(--transition-smooth); }
        a:hover { color: var(--brand); }
        
        h1, h2, h3, h4, h5, h6 { font-weight: 800; letter-spacing: -0.04em; color: var(--ink); line-height: 1.15; }
        p { color: var(--ink-soft); font-size: 1rem; }
        
        /* Ambient Floating Particle Layer Background */
        .ambient-glow-layer {
            position: absolute; width: 600px; height: 600px;
            background: radial-gradient(circle, var(--brand-glow) 0%, transparent 70%);
            top: -150px; right: -100px; z-index: 1; pointer-events: none;
            animation: floatingGlow 12px infinite alternate ease-in-out;
        }

        @keyframes floatingGlow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-30px, 40px) scale(1.1); }
        }

        .text-gradient {
            background: var(--brand-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Responsive Fluid Navigation Hub */
        .header-nav { 
            position: sticky; top: 0; 
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(24px); 
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(241, 245, 249, 0.8); 
            z-index: 1000; 
            transition: var(--transition-smooth);
        }
        .header-nav .row { display: flex; align-items: center; justify-content: space-between; padding: 16px 0; }
        .header-nav .brand { font-weight: 800; font-size: 1.4rem; letter-spacing: -0.04em; display: flex; align-items: center; gap: 8px; }
        .header-nav .brand svg { color: var(--brand); transition: transform 0.4s ease; }
        .header-nav .brand:hover svg { transform: rotate(15deg) scale(1.1); }
        .header-nav .links { display: flex; gap: 32px; align-items: center; font-size: 0.95rem; font-weight: 600; }
        .header-nav .links a:not(.btn) { color: var(--ink-soft); position: relative; padding: 4px 0; }
        .header-nav .links a:not(.btn)::after { content: ''; position: absolute; bottom: 0; left: 0; width: 0; height: 2px; background: var(--brand-gradient); transition: var(--transition-smooth); }
        .header-nav .links a:not(.btn):hover::after { width: 100%; }
        
        /* Modernized Interactive Buttons */
        .btn { 
            display: inline-flex; align-items: center; justify-content: center; gap: 8px; 
            border: 1px solid transparent; border-radius: var(--radius-card-sm); 
            padding: 14px 28px; font-weight: 600; font-size: 0.95rem; 
            cursor: pointer; transition: var(--transition-bounce); position: relative; overflow: hidden;
        }
        .btn::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent); transition: 0.5s; }
        .btn:hover::before { left: 100%; }
        
        .btn-primary { background: var(--ink); color: #fff; box-shadow: var(--shadow-fluid-sm); }
        .btn-primary:hover { background: #010204; transform: translateY(-3px) scale(1.02); box-shadow: var(--shadow-fluid-md); }
        .btn-brand { background: var(--brand-gradient); color: #fff; box-shadow: 0 8px 24px rgba(5, 150, 105, 0.15); }
        .btn-brand:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 12px 32px rgba(5, 150, 105, 0.3); color: #fff; }
        .btn-outline { background: transparent; color: var(--ink); border: 1px solid var(--border-card); }
        .btn-outline:hover { background: var(--bg-soft); border-color: var(--muted); transform: translateY(-2px); }
        .btn-lg { padding: 18px 36px; font-size: 1.05rem; border-radius: var(--radius-card-sm); }

        /* Immersive Hero Architecture */
        .hero { position: relative; padding: 120px 0 90px; overflow: hidden; }
        .hero .badge-capsule { display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--brand); font-weight: 700; background: var(--brand-soft); border: 1px solid rgba(5, 150, 105, 0.15); padding: 8px 16px; border-radius: 99px; margin-bottom: 28px; letter-spacing: 0.02em; animation: pulseBadge 2s infinite; }
        @keyframes pulseBadge { 0% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.2); } 70% { box-shadow: 0 0 0 10px rgba(5, 150, 105, 0); } 100% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0); } }
        
        .hero h1 { font-size: clamp(3rem, 6.2vw, 4.6rem); line-height: 1.1; margin-bottom: 24px; max-width: 20ch; letter-spacing: -0.05em; }
        .hero .bn-tag { font-size: clamp(1.4rem, 2.6vw, 1.85rem); color: var(--ink-soft); margin-bottom: 28px; max-width: 850px; font-weight: 600; line-height: 1.4; letter-spacing: -0.01em; }
        .hero .lead { font-size: 1.2rem; color: var(--muted); margin-bottom: 44px; max-width: 720px; line-height: 1.65; }
        .hero .ctas { display: flex; flex-wrap: wrap; gap: 16px; align-items: center; }
        
        .hero .stats-matrix { margin-top: 80px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 40px; padding-top: 40px; border-top: 1px solid var(--line); }
        .hero .stat-node { transition: var(--transition-smooth); }
        .hero .stat-node:hover { transform: translateY(-4px); }
        .hero .stat-node .val { font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em; color: var(--ink); line-height: 1; }
        .hero .stat-node .lbl { font-size: 0.85rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 8px; font-weight: 600; }

        /* Structural Container Sections */
        .section { padding: 120px 0; position: relative; }
        .section.alt { background: var(--bg-soft); }
        .section .section-head { margin-bottom: 80px; max-width: 800px; }
        .section .section-head .accent-tag { font-size: 0.85rem; font-weight: 700; letter-spacing: 0.1em; color: var(--brand); text-transform: uppercase; margin-bottom: 16px; display: block; }
        .section h2 { font-size: clamp(2.4rem, 4.5vw, 3.2rem); line-height: 1.15; letter-spacing: -0.04em; margin-bottom: 20px; }
        .section .section-lead { font-size: 1.2rem; color: var(--muted); line-height: 1.6; }

        /* Premium Bento-style Interactive Deliverables Framework */
        .bento-container { display: grid; grid-template-columns: repeat(12, 1fr); gap: 28px; }
        .bento-node { 
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius-card-lg); 
            padding: 48px; transition: var(--transition-smooth);
            display: flex; flex-direction: column; justify-content: space-between;
            position: relative; overflow: hidden; box-shadow: var(--shadow-fluid-sm);
        }
        .bento-node:hover { transform: translateY(-6px); box-shadow: var(--shadow-fluid-lg); border-color: var(--border-card); }
        .bento-node .icon-box { width: 56px; height: 56px; border-radius: 16px; background: var(--bg-soft); color: var(--ink); display: flex; align-items: center; justify-content: center; margin-bottom: 32px; border: 1px solid var(--line); transition: var(--transition-bounce); }
        .bento-node:hover .icon-box { background: var(--brand-soft); color: var(--brand); border-color: transparent; transform: scale(1.1) rotate(-8deg); }
        .bento-node h4 { font-size: 1.5rem; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.02em; }
        .bento-node .bn-title { font-size: 1.2rem; color: var(--brand); font-weight: 600; margin-bottom: 20px; }
        .bento-node p { font-size: 1rem; color: var(--ink-soft); line-height: 1.65; }
        .bento-node .floating-badge { position: absolute; top: 32px; right: 32px; background: var(--ink); color: #fff; font-size: 0.72rem; font-weight: 700; padding: 6px 14px; border-radius: 99px; text-transform: uppercase; letter-spacing: 0.06em; }
        
        /* Animated Component Micro-Previews inside Bento Cards */
        .bento-node .interactive-preview-ui { margin-top: 32px; background: var(--bg-soft); border-radius: var(--radius-card-sm); padding: 20px; border: 1px solid var(--line); position: relative; overflow: hidden; }
        .ui-pulse-line { height: 8px; background: #cbd5e1; border-radius: 4px; margin-bottom: 12px; position: relative; overflow: hidden; }
        .ui-pulse-line::after { content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent); animation: linearPulse 1.8s infinite; }
        @keyframes linearPulse { 0% { left: -100%; } 100% { left: 100%; } }

        .gc-7 { grid-column: span 7; }
        .gc-5 { grid-column: span 5; }
        .gc-4 { grid-column: span 4; }
        .gc-8 { grid-column: span 8; }
        .gc-12 { grid-column: span 12; }

        /* Fluid Responsive Features List Cards */
        .adaptive-cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 32px; }
        .fluid-card { 
            background: #fff; padding: 48px 40px; border-radius: var(--radius-card-md); 
            border: 1px solid var(--line); box-shadow: var(--shadow-fluid-sm);
            transition: var(--transition-smooth);
        }
        .fluid-card:hover { border-color: var(--brand); box-shadow: var(--shadow-fluid-md); transform: translateY(-4px); }
        .fluid-card .card-index { font-size: 0.8rem; font-weight: 700; color: var(--brand); letter-spacing: 0.08em; display: block; margin-bottom: 20px; }
        .fluid-card h5 { font-size: 1.35rem; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.02em; }
        .fluid-card .bn-desc { font-size: 1.1rem; color: var(--muted); font-weight: 600; margin-bottom: 16px; }
        .fluid-card p { font-size: 0.98rem; color: var(--ink-soft); line-height: 1.6; }

        /* Minimal Luxury Testimonial Grid Stack */
        .testimonials-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 32px; }
        .premium-review-card {
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius-card-md);
            padding: 48px; display: flex; flex-direction: column; gap: 28px; box-shadow: var(--shadow-fluid-sm);
            transition: var(--transition-smooth);
        }
        .premium-review-card:hover { box-shadow: var(--shadow-fluid-md); border-color: var(--border-card); transform: scale(1.01); }
        .premium-review-card .stars-row { color: #f59e0b; font-size: 1rem; display: flex; gap: 2px; }
        .premium-review-card .quote-body { font-size: 1.15rem; color: var(--ink); line-height: 1.7; font-weight: 500; letter-spacing: -0.01em; }
        .premium-review-card .quote-body.bn { font-size: 1.25rem; line-height: 1.65; }
        .premium-review-card .profile-node { display: flex; align-items: center; gap: 16px; margin-top: auto; padding-top: 24px; border-top: 1px solid var(--line); }
        .premium-review-card .avatar-ui { width: 44px; height: 44px; border-radius: 50%; background: var(--ink); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; transition: var(--transition-bounce); }
        .premium-review-card:hover .avatar-ui { transform: scale(1.1); background: var(--brand-gradient); }
        .premium-review-card .meta-txt h6 { font-size: 1.05rem; font-weight: 700; margin-bottom: 2px; }
        .premium-review-card .meta-txt a { font-size: 0.88rem; color: var(--brand); font-weight: 600; }

        /* Flat Monolithic Unlocked Pricing Box Layout */
        .pricing-monolith { background: var(--ink); color: #fff; border-radius: var(--radius-card-lg); padding: 80px 72px; position: relative; overflow: hidden; box-shadow: var(--shadow-fluid-lg); }
        .pricing-monolith h2 { color: #fff; font-size: clamp(2.2rem, 4.5vw, 3rem); margin-bottom: 20px; }
        .pricing-monolith p { color: #94a3b8; font-size: 1.15rem; }
        .monolith-split { display: grid; grid-template-columns: 1.1fr 1fr; gap: 64px; margin-top: 56px; align-items: center; }
        .monolith-rates .unlocked-badge { display: inline-block; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--brand); background: rgba(5, 150, 105, 0.15); padding: 6px 14px; border-radius: 6px; margin-bottom: 24px; border: 1px solid rgba(5, 150, 105, 0.2); }
        .monolith-rates .rate-num { font-size: clamp(5rem, 11vw, 7.5rem); font-weight: 800; letter-spacing: -0.06em; line-height: 1; color: #fff; margin-bottom: 8px; }
        .monolith-rates .rate-num small { font-size: 1.1rem; font-weight: 500; color: #64748b; margin-left: 8px; }
        .monolith-rates .strike-rate { color: #475569; font-size: 1.15rem; text-decoration: line-through; margin-bottom: 32px; display: block; }
        .monolith-perks { list-style: none; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px 32px; }
        .monolith-perks li { display: flex; gap: 12px; align-items: flex-start; color: #cbd5e1; font-size: 1.05rem; }
        .monolith-perks li svg { color: var(--brand); flex-shrink: 0; margin-top: 4px; transition: transform 0.3s ease; }
        .pricing-monolith:hover .monolith-perks li svg { transform: scale(1.15); }
        .monolith-banner-note { margin-top: 48px; padding: 24px; background: #0f172a; border-radius: var(--radius-card-sm); font-size: 0.98rem; color: #94a3b8; border: 1px solid #1e293b; }

        /* Dynamic Alternative Package System Matrix (Tier Mode Framework) */
        .tier-framework-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px; }
        .matrix-tier-card { background: #fff; border: 1px solid var(--line); border-radius: var(--radius-card-lg); padding: 56px 48px; display: flex; flex-direction: column; transition: var(--transition-smooth); position: relative; box-shadow: var(--shadow-fluid-sm); }
        .matrix-tier-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-fluid-lg); border-color: var(--border-card); }
        .matrix-tier-card.featured { border: 2px solid var(--brand); }
        .matrix-tier-card .pop-pill { position: absolute; top: -14px; left: 48px; background: var(--brand-gradient); color: #fff; padding: 6px 18px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .matrix-tier-card .tier-title { font-size: 1.45rem; font-weight: 800; margin-bottom: 16px; }
        .matrix-tier-card .tier-value { font-size: 3rem; font-weight: 800; letter-spacing: -0.04em; margin-bottom: 8px; }
        .matrix-tier-card .tier-value small { font-size: 1.05rem; color: var(--muted); font-weight: 400; }
        .matrix-tier-card .tier-list { list-style: none; display: flex; flex-direction: column; gap: 16px; margin: 36px 0; flex: 1; }
        .matrix-tier-card .tier-list li { display: flex; gap: 12px; font-size: 1rem; color: var(--ink-soft); }
        .matrix-tier-card .tier-list li::before { content: '\2713'; color: var(--brand); font-weight: 700; }

        /* Conversion Activation Closure Panel */
        .activation-panel { padding: 140px 0; text-align: center; background: #f8fafc; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); position: relative; overflow: hidden; }
        .activation-panel h2 { font-size: clamp(2.4rem, 5vw, 3.5rem); line-height: 1.15; letter-spacing: -0.04em; margin-bottom: 24px; max-width: 22ch; margin-left: auto; margin-right: auto; }
        .activation-panel p { color: var(--muted); font-size: 1.2rem; margin-bottom: 44px; max-width: 680px; margin-left: auto; margin-right: auto; }

        /* Corporate Structural Footer */
        footer { padding: 120px 0 40px; background: #fff; position: relative; z-index: 5; }
        footer .main-row { display: grid; grid-template-columns: 2.5fr 1fr 1.2fr; gap: 64px; margin-bottom: 80px; }
        footer .col h6 { font-size: 0.85rem; font-weight: 700; letter-spacing: 0.08em; color: var(--ink); text-transform: uppercase; margin-bottom: 28px; }
        footer .col ul { list-style: none; display: flex; flex-direction: column; gap: 14px; }
        footer .col ul li a { color: var(--muted); font-size: 0.98rem; }
        footer .col ul li a:hover { color: var(--brand); padding-left: 4px; }
        footer .brand-signature { font-weight: 800; font-size: 1.45rem; letter-spacing: -0.03em; color: var(--ink); margin-bottom: 18px; display: inline-flex; align-items: center; gap: 8px; }
        footer .brand-signature svg { color: var(--brand); }
        footer .blurb-txt { color: var(--muted); font-size: 0.98rem; max-width: 400px; line-height: 1.65; margin-top: 12px; }
        footer .base-bar { display: flex; flex-wrap: wrap; justify-content: space-between; border-top: 1px solid var(--line); padding-top: 40px; font-size: 0.9rem; color: var(--muted); gap: 16px; }

        /* Production Precision Intersection Keyframes Orchestrator */
        @media (prefers-reduced-motion: no-preference) {
            .reveal { opacity: 0; transform: translateY(24px); animation: nodeFadeIn 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
            .reveal.d1 { animation-delay: 0.12s; }
            .reveal.d2 { animation-delay: 0.28s; }
            @keyframes nodeFadeIn { to { opacity: 1; transform: none; } }
        }

        /* Screen Scale Adapters */
        @media (max-width: 1024px) {
            .gc-7, .gc-5, .gc-4, .gc-8 { grid-column: span 12; }
            footer .main-row { grid-template-columns: 1fr; gap: 56px; }
            .monolith-split { grid-template-columns: 1fr; gap: 56px; }
        }
        @media (max-width: 768px) {
            .header-nav .links a:not(.btn) { display: none; }
            .hero { padding: 90px 0 50px; }
            .section { padding: 80px 0; }
            .pricing-monolith { padding: 48px 24px; }
            .bento-node { padding: 36px 24px; }
        }
    </style>
</head>
<body>

<div class="ambient-glow-layer" aria-hidden="true"></div>

<header class="header-nav">
    <div class="container">
        <div class="row">
            <a class="brand" href="<?= base_url() ?>" aria-label="SmartSchool Operational Hub Portal">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                SmartSchool<span style="color:var(--brand)">.bd</span>
            </a>
            <nav class="links" aria-label="Primary Platform Hub Links">
                <?php if ($show_feat): ?><a href="#deliverables">প্যাকেজ বিবরণী</a><?php endif; ?>
                <?php if ($show_feat): ?><a href="#modules">ফিচার প্যাক</a><?php endif; ?>
                <?php if ($show_schools): ?><a href="#reviews">রিভিউ সমূহ</a><?php endif; ?>
                <?php if ($show_price): ?><a href="#pricing">প্রাইসিং</a><?php endif; ?>
                <a href="<?= htmlspecialchars($login) ?>">লগইন</a>
                <a class="btn btn-primary bn" style="padding: 10px 20px;" href="<?= htmlspecialchars($signup) ?>">ফ্রি ট্রায়াল শুরু করুন</a>
            </nav>
        </div>
    </div>
</header>

<main>
    <section class="hero" id="platform-entry-node">
        <div class="container">
            <div class="reveal d1">
                <span class="badge-capsule"><span style="width:6px; height:6px; background:var(--brand); border-radius:50%"></span> <?= htmlspecialchars($eyeb) ?></span>
                <h1><span class="text-gradient"><?= htmlspecialchars($h1) ?></span></h1>
                <?php if ($h1_bn): ?>
                    <p class="bn bn-tag"><?= htmlspecialchars($h1_bn) ?></p>
                <?php endif; ?>
                <?php if ($lead): ?>
                    <p class="lead"><?= htmlspecialchars($lead) ?></p>
                <?php endif; ?>
                
                <div class="ctas">
                    <a class="btn btn-brand btn-lg bn" href="<?= htmlspecialchars($signup) ?>"><?= htmlspecialchars($cta_primary) ?> &rarr;</a>
                    <a class="btn btn-outline btn-lg bn" href="#deliverables"><?= htmlspecialchars($cta_sec) ?></a>
                </div>
                
                <div class="stats-matrix">
                    <div class="stat-node"><div class="val">১,২০০+</div><div class="lbl bn">সক্রিয় ইনস্টিটিউশন</div></div>
                    <div class="stat-node"><div class="val">&#2547;০</div><div class="lbl bn">আজীবন কোনো ফি নেই</div></div>
                    <div class="stat-node"><div class="val">৫ মিনিট</div><div class="lbl bn">ইনস্ট্যান্ট সেটআপ টাইম</div></div>
                    <div class="stat-node"><div class="val">Dual UI</div><div class="lbl bn">সম্পূর্ণ বাংলা ও ইংরেজি</div></div>
                </div>
            </div>
            
            <div class="showcase-wrapper reveal d2" style="margin-top: 64px; background: var(--bg-soft); border: 1px solid var(--line); border-radius: var(--radius-card-lg); padding: 24px; box-shadow: var(--shadow-fluid-md);">
                <div class="showcase-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--line);">
                    <div class="showcase-dots" style="display: flex; gap: 8px;">
                        <div class="showcase-dot active" style="width: 12px; height: 12px; border-radius: 50%; background: var(--brand);"></div>
                        <div class="showcase-dot" style="width: 12px; height: 12px; border-radius: 50%; background: #cbd5e1;"></div>
                        <div class="showcase-dot" style="width: 12px; height: 12px; border-radius: 50%; background: #cbd5e1;"></div>
                    </div>
                    <div class="bn" style="font-size:0.85rem; font-weight:700; color:var(--brand)">SmartSchool™ Core Management Core</div>
                </div>
                <div class="showcase-body" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                    <div class="showcase-card" style="background: #ffffff; border: 1px solid var(--line); border-radius: var(--radius-card-sm); padding: 24px; box-shadow: var(--shadow-fluid-sm); display: flex; flex-direction: column; justify-content: space-between; transition: var(--transition-smooth);">
                        <div>
                            <div class="bn" style="font-size: 0.8rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">মোট শিক্ষার্থী (Total Students)</div>
                            <div style="font-size: 2rem; font-weight: 800; color: var(--ink); line-height: 1;">১,৪৫০ <span style="font-size: 0.85rem; font-weight: 600; color: var(--brand); background: var(--brand-soft); padding: 2px 8px; border-radius: 4px; margin-left: 4px;">সক্রিয়</span></div>
                        </div>
                        <div style="margin-top: 20px;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; color: var(--ink-soft);">
                                <span class="bn">আজকের ডিজিটাল হাজিরা</span>
                                <span>৯৪.২%</span>
                            </div>
                            <div style="height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                                <div style="width: 94.2%; height: 100%; background: var(--brand-gradient); border-radius: 3px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="showcase-card" style="background: #ffffff; border: 1px solid var(--line); border-radius: var(--radius-card-sm); padding: 24px; box-shadow: var(--shadow-fluid-sm); display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="bn" style="font-size: 0.8rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">চলতি মাসের ফি কালেকশন</div>
                            <div style="font-size: 2rem; font-weight: 800; color: var(--ink); line-height: 1;">&#2547;৩,৮২,০০০</div>
                        </div>
                        <div style="margin-top: 20px; display: flex; align-items: center; gap: 8px; background: var(--brand-soft); padding: 10px; border-radius: 8px; border: 1px solid rgba(5, 150, 105, 0.1);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color: var(--brand);"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                            <span class="bn" style="font-size: 0.8rem; font-weight: 700; color: var(--brand);">১২.৫% কালেকশন বৃদ্ধি</span>
                        </div>
                    </div>
                    <div class="showcase-card" style="background: #ffffff; border: 1px solid var(--line); border-radius: var(--radius-card-sm); padding: 24px; box-shadow: var(--shadow-fluid-sm); display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="bn" style="font-size: 0.8rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">অটোমেটেড নোটিফিকেশন সিস্টেম</div>
                            <div class="bn" style="font-size: 1.15rem; font-weight: 700; color: var(--ink); margin-bottom: 4px;">এসএমএস গেটওয়ে রেডি</div>
                        </div>
                        <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 8px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8rem;">
                                <span class="bn" style="color: var(--muted);">আজকের প্রেরিত নোটিশ</span>
                                <span style="font-weight: 700; color: var(--ink);">১,৩২০ টি</span>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8rem;">
                                <span class="bn" style="color: var(--muted);">ডেলিভারি সাকসেস রেট</span>
                                <span style="font-weight: 700; color: var(--brand);">৯৯.৮%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section alt" id="deliverables">
        <div class="container">
            <div class="section-head">
                <span class="accent-tag">Deliverables Architecture</span>
                <h2>আমাদের সিস্টেম থেকে আপনি কী কী পাবেন?</h2>
                <p class="section-lead">আপনার শিক্ষাপ্রতিষ্ঠানকে সম্পূর্ণ ডিজিটাল করার জন্য প্রয়োজনীয় প্রতিটি উপাদান একটি মাত্র কমপ্লিট প্যাকেজে ডেভেলপ করা হয়েছে।</p>
            </div>
            
            <div class="bento-container">
                <div class="bento-node gc-7">
                    <span class="floating-badge">Instant Subdomain</span>
                    <div>
                        <div class="icon-box">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </div>
                        <h4>1 Full Ready Website</h4>
                        <div class="bn-title bn">১টি সম্পূর্ণ রেডি ডায়নামিক ওয়েবসাইট</div>
                        <p>কোডিং নলেজ ছাড়াই প্রতিষ্ঠানের নিজস্ব অনলাইন নোটিশ বোর্ড, শিক্ষক পরিচিতি মডিউল, ইমেজ গ্যালারি এবং ভর্তি ফরম সম্বলিত প্রফেশনাল ও রেসপনসিভ ওয়েবসাইট যা অটো-লাইভ হবে।</p>
                    </div>
                    <div class="interactive-preview-ui" aria-hidden="true">
                        <div class="ui-pulse-line" style="width: 40%; background: var(--brand)"></div>
                        <div class="ui-pulse-line"></div>
                        <div class="ui-pulse-line" style="width: 60%"></div>
                    </div>
                </div>

                <div class="bento-node gc-5">
                    <span class="floating-badge">App Framework</span>
                    <div>
                        <div class="icon-box">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                        </div>
                        <h4>1 Dedicated Android App</h4>
                        <div class="bn-title bn">১টি ডেডিকেটেড মোবাইল অ্যাপ্লিকেশন পোর্টাল</div>
                        <p>অভিভাবক ও শিক্ষার্থীদের জন্য তৈরি বিশেষায়িত অ্যাপ পোর্টাল। এর মাধ্যমে দৈনিক পুশ নোটিফিকেশন, রিয়েল-টাইম ডিজিটাল হাজিরা এবং ফলাফল সরাসরিスマートফোনে পাওয়া যাবে।</p>
                    </div>
                </div>

                <div class="bento-node gc-4">
                    <div>
                        <div class="icon-box">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                        </div>
                        <h4>All Premium Features Unlocked</h4>
                        <div class="bn-title bn">সব প্রিমিয়াম ফিচার আনলকড</div>
                        <p>কোনো মডিউল লক করা নেই বা আপসেল কন্ডিশন নেই। প্রথম দিন থেকেই আপনি ডাটাবেজের সম্পূর্ণ অ্যাক্সেস সহ সব মডিউল কোনো এক্সট্রা চার্জ ছাড়াই ব্যবহার করতে পারবেন。</p>
                    </div>
                </div>

                <div class="bento-node gc-4">
                    <div>
                        <div class="icon-box">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <h4>100% Fully Customizable</h4>
                        <div class="bn-title bn">সম্পূর্ণ কাস্টমাইজযোগ্য ড্যাশবোর্ড</div>
                        <p>প্রতিষ্ঠানের নিজস্ব থিম কালার, মনোগ্রাম, অফিশিয়াল লোগো এবং ক্লাস শিফট রুলস বিন্যাস অনুযায়ী সম্পূর্ণ প্যানেল ও ওয়েবসাইট নিজের মতো সাজিয়ে নেয়ার স্বাধীনতা।</p>
                    </div>
                </div>

                <div class="bento-node gc-4">
                    <div>
                        <div class="icon-box">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                        </div>
                        <h4>Custom Features Included</h4>
                        <div class="bn-title bn">অন-ডিমান্ড কাস্টম ফিচার</div>
                        <p>আপনার প্রতিষ্ঠানের যদি বিশেষ কোনো মডিউল, কাস্টম রিপোর্টিং খতিয়ান ফরমেট বা এক্সেল রিপোর্টের প্রয়োজন হয়, আমাদের ডেভলপার টিম রিকোয়ারমেন্ট অনুযায়ী তা যুক্ত করে দেবে।</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="modules">
        <div class="container">
            <div class="section-head">
                <span class="accent-tag">Core Modules Pack</span>
                <h2>সফ্টওয়্যারের মূল টেকনিক্যাল মডিউলসমূহ</h2>
                <p class="section-lead">প্রতিষ্ঠানের প্রতিদিনের জটিল হিসাব ও রেকর্ড কিপিং প্রসেসকে নিখুঁতভাবে অটোমেট করার বিশেষায়িত আর্কিটেকচার।</p>
            </div>
            
            <div class="adaptive-cards-grid">
                <div class="fluid-card">
                    <span class="card-index">01 / AUTOMATION</span>
                    <h5>Admit Card Generator</h5>
                    <div class="bn-desc bn">ডিজিটাল প্রবেশপত্র ইঞ্জিন</div>
                    <p>শিক্ষার্থীদের বেতন ক্লিয়ারেন্স ও অ্যাকাউন্টস রিপোর্টের ওপর ভিত্তি করে এক ক্লিকে ছবি ও রোল নম্বর সহ পরীক্ষার এডমিট কার্ড স্বয়ংক্রিয়ভাবে জেনারেট ও বাল্ক প্রিন্ট করার আধুনিক মডিউল।</p>
                </div>

                <div class="fluid-card">
                    <span class="card-index">02 / CORE ENGINE</span>
                    <h5>Advanced Result System</h5>
                    <div class="bn-desc bn">স্বয়ংক্রিয় ফলাফল ও ট্রান্সক্রিপ্ট প্রসেসর</div>
                    <p>শ্রেণিভিত্তিক জিপিএ ও গ্রেড পয়েন্ট অটো-ক্যালকুলেশন মেকানিজম। টেবুলেশন শিট প্রস্তুতকরণ এবং কাস্টমাইজড নম্বরসহ প্রফেশনাল একাডেমিক প্রোগ্রেস রিপোর্ট কার্ড ডাউনলোডের ব্যবস্থা।</p>
                </div>

                <div class="fluid-card">
                    <span class="card-index">03 / DIRECTORY</span>
                    <h5>Student Management</h5>
                    <div class="bn-desc bn">স্মার্ট স্টুডেন্ট প্রোফাইল ডাটাবেজ</div>
                    <p>ডিজিটাল ভর্তি ফাইল ট্র্যাকিং, ইউনিক আইডি কার্ড জেনারেশন, সেশন ভিত্তিক অটোমেটেড সেকশন প্রমোশন এবং অভিভাবক কন্টাক্ট ডিরেক্টরির ওয়ান-স্টপ ডেটা সেন্টার।</p>
                </div>

                <div class="fluid-card">
                    <span class="card-index">04 / MANAGEMENT</span>
                    <h5>Teacher & Staff Portals</h5>
                    <div class="bn-desc bn">শিক্ষক ও স্টাফ কন্ট্রোল প্যানেল</div>
                    <p>শিক্ষকদের প্রোফাইল ডাটাবেজ, ক্লাস রুটিন অ্যাসাইনমেন্ট এবং স্যালারি লেজার ট্র্যাকিং। শিক্ষকরা সরাসরি প্যানেল লগইন করে ক্লাসের উপস্থিতি ও পরীক্ষার মার্কস ইনপুট দিতে পারবেন।</p>
                </div>

                <div class="fluid-card">
                    <span class="card-index">05 / INTEGRATION</span>
                    <h5>Automated Attendance & SMS</h5>
                    <div class="bn-desc bn">স্মার্ট হাজিরা ও ইনস্ট্যান্ট এসএমএস গেটওয়ে</div>
                    <p>শিক্ষার্থী অনুপস্থিত থাকলে বা বেতন বকেয়া থাকলে স্বয়ংক্রিয়ভাবে অভিভাবকদের মোবাইল ফোনে ইনস্ট্যান্ট এলার্ট নোটিফিকেশন বাংলা অথবা ইংরেজি টেক্সটে চলে যাওয়ার নির্ভরযোগ্য গেটওয়ে সুবিধা।</p>
                </div>

                <div class="fluid-card">
                    <span class="card-index">06 / FINANCIALS</span>
                    <h5>Fee Ledger & Accounts</h5>
                    <div class="bn-desc bn">ফি কালেকশন এবং অ্যাকাউন্টস বিবরণী খতিয়ান</div>
                    <p>মাসিক বেতন, সেশন ফি ও কাস্টম ইনভয়েস ট্র্যাকিং। ডিজিটাল পেমেন্ট গেটওয়ে বা অফলাইন ক্যাশ রিসিট পোস্টিং সহ সম্পূর্ণ ডাবল-এন্ট্রি প্রাতিষ্ঠানিক খতিয়ান ও আয়ের হিসাব বিবরণী।</p>
                </div>
            </div>
        </div>
    </section>

    <?php if ($show_schools && $show_test): ?>
    <section class="section alt" id="reviews">
        <div class="container">
            <div class="section-head" style="text-align: center; margin-inline: auto;">
                <span class="accent-tag">Institutional Proof</span>
                <h2>লাইভ রিভিউ ও বাস্তব অভিজ্ঞতা</h2>
                <p class="section-lead">সাফল্যের সাথে আমাদের ইআরসি ইকোসিস্টেম ব্যবহার করে ডিজিটাল কার্যক্রম পরিচালনা করছে দেশের স্বনামধন্য বিভিন্ন প্রতিষ্ঠান।</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="premium-review-card">
                    <div class="stars-row">★★★★★</div>
                    <p class="quote-body bn">"স্টুডেন্টদের পরীক্ষার এডমিট কার্ড এবং মার্কশিট জেনারেট করার জটিল প্রসেসটি এখন এক ক্লিকে হয়ে যায়। বিশেষ করে কাস্টম ফিচারের অন-ডিমান্ড সুবিধা আমাদের প্রশাসনিক কাজকে ৯০% সহজ করে দিয়েছে।"</p>
                    <div class="profile-node">
                        <div class="avatar-ui">A</div>
                        <div class="meta-txt">
                            <h6 class="bn">অ্যাকাডেমিক স্কুল অ্যাডমিন প্যানেল</h6>
                            <a href="https://academicschool.net" target="_blank" rel="noopener">academicschool.net</a>
                        </div>
                    </div>
                </div>

                <div class="premium-review-card">
                    <div class="stars-row">★★★★★</div>
                    <p class="quote-body bn">"রেডি ওয়েবসাইট ও অ্যান্ড্রয়েড অ্যাপের কম্বিনেশন আমাদের প্রতিষ্ঠানের ডিজিটাল ব্র্যান্ডিং সম্পূর্ণ বদলে দিয়েছে। শিক্ষকরা খুব সহজেই মোবাইল থেকেই হাজিরা ও পরীক্ষার মার্কস এন্ট্রি দিতে পারেন এবং অভিভাবকরাও রিয়েল-টাইম আপডেট পান।"</p>
                    <div class="profile-node">
                        <div class="avatar-ui" style="background:var(--brand)">N</div>
                        <div class="meta-txt">
                            <h6 class="bn">এনজিপিএস একাডেমি ম্যানেজমেন্ট কোর্ড</h6>
                            <a href="https://ngps.smartschool.bd" target="_blank" rel="noopener">ngps.smartschool.bd</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($show_price && $pmode !== 'hidden'): ?>
    <section class="section" id="pricing">
        <div class="container">
            <?php if ($pmode === 'tiers' && !empty($packages)): ?>
                <div class="section-head">
                    <span class="accent-tag">Transparent Cost</span>
                    <h2><?= htmlspecialchars($ph) ?></h2>
                    <p class="section-lead">কোনো সেটআপ ফি বা হিডেন চার্জ নেই। আপনার প্রতিষ্ঠানের স্কেল অনুযায়ী প্ল্যান সিলেক্ট করুন।</p>
                </div>
                <div class="tier-framework-grid">
                    <?php foreach ($packages as $p):
                        $featured = (strtolower($p->code) === 'starter');
                        $price = (float)$p->price_bdt;
                        $per = $p->billing_period === 'yearly' ? '/বছর' : ($p->billing_period === 'lifetime' ? ' এককালীন' : '/মাস');
                        $feats = is_array($p->features) ? $p->features : [];
                        $highlights = array_map(function($f) use ($feature_labels) {
                            return $feature_labels[$f] ?? str_replace('_', ' ', $f);
                        }, array_slice($feats, 0, 8));
                    ?>
                    <div class="matrix-tier-card <?= $featured ? 'featured' : '' ?>">
                        <?php if ($featured): ?><span class="pop-pill">সর্বোত্তম চয়েস</span><?php endif; ?>
                        <h3 class="tier-title"><?= htmlspecialchars($p->name) ?></h3>
                        <div class="tier-value">
                            <?php if ($price > 0): ?>&#2547;<?= number_format($price) ?><small><?= htmlspecialchars($per) ?></small><?php else: ?>ফ্রি<?php endif; ?>
                        </div>
                        <ul class="tier-list" aria-label="<?= htmlspecialchars($p->name) ?> Features List">
                            <?php foreach ($highlights as $h): ?><li><?= htmlspecialchars($h) ?></li><?php endforeach; ?>
                        </ul>
                        <a class="btn btn-primary" style="text-align:center;" href="<?= htmlspecialchars($signup) ?>?plan=<?= urlencode($p->code) ?>">
                            <?= $price > 0 ? 'প্যাকেজটি একটিভ করুন' : 'ফ্রি অ্যাকাউন্ট তৈরি করুন' ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="pricing-monolith">
                    <div class="monolith-split">
                        <div class="monolith-rates">
                            <span class="unlocked-badge">Full Deployment Access</span>
                            <h2>সব মডিউল আনলকড, আজীবন ফ্রি</h2>
                            <div class="rate-num">&#2547;০<small>/ প্রতিষ্ঠান / মাস</small></div>
                            <span class="strike-rate">স্ট্যান্ডার্ড ইআরপি রেট: &#2547;২,৯৯৯ / মাস</span>
                            <div style="display:flex; gap:16px; flex-wrap:wrap; margin-top: 32px;">
                                <a class="btn btn-brand bn" href="<?= htmlspecialchars($signup) ?>"><?= htmlspecialchars($cta_primary) ?></a>
                                <a class="btn btn-outline bn" style="color:#fff; border-color:#334155" href="<?= htmlspecialchars($login) ?>">ড্যাশবোর্ড লগইন</a>
                            </div>
                        </div>
                        <div>
                            <ul class="monolith-perks" aria-label="স্মার্টস্কুল কোর সুবিধা">
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> আনলিমিটেড স্টুডেন্ট প্রোফাইল ডেটা</li>
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> ১টি ডায়নামিক রেডি ওয়েবসাইট</li>
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> ডেডিকেটেড অ্যান্ড্রয়েড অ্যাপ গেটওয়ে</li>
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> এডমিট কার্ড ও রেজাল্ট জেনারেটর</li>
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> স্বয়ংক্রিয় বাল্ক এসএমএস নোটিফিকেশন</li>
                                <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> সম্পূর্ণ নিরাপদ ডেটা আইসোলেশন ক্লাউড</li>
                            </ul>
                            <?php if (!empty($pfn)): ?>
                                <div class="monolith-banner-note bn"><strong>আপডেট পলিসি নোট:</strong> <?= htmlspecialchars($pfn) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="activation-panel">
        <div class="container">
            <h2 class="bn">আপনার শিক্ষাপ্রতিষ্ঠানকে সম্পূর্ণ ডিজিটাল ও পেপারলেস করুন</h2>
            <p class="bn">ইনস্ট্যান্ট সাবডোমেইন, ডেডিকেটেড অ্যান্ড্রয়েড অ্যাপ্লিকেশন এবং অটোমেটেড হিসাব বিবরণী খতিয়ান সহ সম্পূর্ণ ইআরপি ইকোসিস্টেম লাইভ করুন মাত্র ৫ মিনিটে।</p>
            <a class="btn btn-primary btn-lg bn" style="background:var(--brand); color:#fff" href="<?= htmlspecialchars($signup) ?>">অ্যাকাউন্ট তৈরি করুন (সম্পূর্ণ ফ্রি) &rarr;</a>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <div class="main-row">
            <div class="col">
                <a class="brand-signature" href="<?= base_url() ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    SmartSchool<span style="color:var(--brand)">.bd</span>
                </a>
                <p class="blurb-txt bn">বাংলাদেশের স্কুল, কলেজ ও মাদ্রাসাসমূহের প্রশাসনিক জটিলতা দূর করে সম্পূর্ণ অটোমেশন এবং ক্লাউড-বেসড অপারেশন নিশ্চিত করতে ডেভেলপ করা একটি আধুনিক ম্যানেজমেন্ট সফটওয়্যার প্ল্যাটফর্ম।</p>
            </div>
            <div class="col">
                <h6>সিস্টেম ডিরেক্টরি</h6>
                <ul>
                    <li><a href="#deliverables">প্যাকেজ বিবরণী মডিউল</a></li>
                    <li><a href="#modules">ফিচার প্যাক মডিউল</a></li>
                    <li><a href="#reviews">ব্যবহারকারীদের রিভিউ</a></li>
                    <li><a href="<?= htmlspecialchars($signup) ?>">নতুন রেজিস্ট্রেশন পোর্টাল</a></li>
                    <li><a href="<?= htmlspecialchars($login) ?>">অ্যাডমিন প্যানেল লগইন</a></li>
                </ul>
            </div>
            <div class="col">
                <h6>লাইভ ইনস্টিটিউট নেটওয়ার্ক</h6>
                <ul>
                    <li><a href="https://academicschool.net" target="_blank" rel="noopener">Academic School ওয়েবসাইট</a></li>
                    <li><a href="https://ngps.smartschool.bd" target="_blank" rel="noopener">NGPS Academy লাইভ পোর্টাল</a></li>
                    <li><a href="mailto:al.exbru69789@gmail.com">কোর টেকনিক্যাল ইঞ্জিনিয়ারিং সাপোর্ট</a></li>
                </ul>
            </div>
        </div>
        <div class="base-bar">
            <div>&copy; <?= date('Y') ?> SmartSchool.bd. সর্বস্বত্ব সংরক্ষিত। বাংলাদেশে নির্মিত।</div>
            <div style="display: flex; gap: 24px;">
                <a href="<?= base_url('home/privacy') ?>">প্রাইভেসি পলিসি (Privacy)</a>
                <a href="<?= base_url('home/terms') ?>">ব্যবহারের শর্তাবলী (Terms)</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>