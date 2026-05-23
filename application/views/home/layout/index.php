<?php
/* =============================================================
   Default Template Layout — design matched to https://smartschoolbd.app/
   Tokens drive theming via $cms_setting (primary_color, hover_color,
   menu_color, footer_background_color, copyright_bg_color, etc.).
   Backend functions preserved: header.php (dynamic menu),
   home/index.php (sliders, marquee, welcome, speeches, teachers,
   news, gallery, sidebar widgets), footer.php.
   ============================================================= */

if (!function_exists('thm_shade')) {
	function thm_shade($hex, $percent) {
		$hex = ltrim((string)$hex, '#');
		if (strlen($hex) === 3) {
			$hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
		}
		if (strlen($hex) !== 6 || !ctype_xdigit($hex)) return '#000000';
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));
		$percent = max(-100, min(100, $percent));
		if ($percent < 0) {
			$factor = 1 + ($percent / 100);
			$r = max(0, min(255, (int) round($r * $factor)));
			$g = max(0, min(255, (int) round($g * $factor)));
			$b = max(0, min(255, (int) round($b * $factor)));
		} else {
			$factor = $percent / 100;
			$r = max(0, min(255, (int) round($r + (255 - $r) * $factor)));
			$g = max(0, min(255, (int) round($g + (255 - $g) * $factor)));
			$b = max(0, min(255, (int) round($b + (255 - $b) * $factor)));
		}
		return sprintf('#%02x%02x%02x', $r, $g, $b);
	}
}
if (!function_exists('thm_contrast_text')) {
	function thm_contrast_text($hex, $light = '#ffffff', $dark = '#1a1a2e') {
		$hex = ltrim((string)$hex, '#');
		if (strlen($hex) === 3) {
			$hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
		}
		if (strlen($hex) !== 6 || !ctype_xdigit($hex)) return $dark;
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));
		$luma = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;
		return $luma > 0.6 ? $dark : $light;
	}
}
if (!function_exists('thm_rgba')) {
	function thm_rgba($hex, $alpha = 1.0) {
		$hex = ltrim((string)$hex, '#');
		if (strlen($hex) === 3) {
			$hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
		}
		if (strlen($hex) !== 6 || !ctype_xdigit($hex)) return 'rgba(0,0,0,'.$alpha.')';
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));
		return "rgba($r, $g, $b, $alpha)";
	}
}

$thm_primary  = !empty($cms_setting['primary_color']) ? $cms_setting['primary_color'] : '#1a237e';
$thm_accent   = !empty($cms_setting['hover_color']) ? $cms_setting['hover_color'] : '#f9a825';
$thm_menu_bg  = !empty($cms_setting['menu_color']) ? $cms_setting['menu_color'] : '#ffffff';
$thm_primary_dark  = thm_shade($thm_primary, -35);
$thm_primary_light = thm_shade($thm_primary, 14);
$thm_accent_dark   = thm_shade($thm_accent, -22);
$thm_accent_light  = thm_shade($thm_accent, 18);
$thm_on_primary    = thm_contrast_text($thm_primary);
$thm_on_accent     = thm_contrast_text($thm_accent);
$thm_on_menu       = thm_contrast_text($thm_menu_bg);
$thm_accent_glow   = thm_rgba($thm_accent, 0.10);
$thm_primary_rgba  = thm_rgba($thm_primary, 0.40);
?>
<!doctype html>
<html lang="bn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keyword" content="<?php echo isset($page_data['meta_keyword']) ? $page_data['meta_keyword'] : ''; ?>">
	<meta name="description" content="<?php echo isset($page_data['meta_description']) ? $page_data['meta_description'] : ''; ?>">
	<?php if (!empty($cms_setting['fav_icon'])): ?>
	<link rel="shortcut icon" href="<?php echo base_url('uploads/frontend/images/' . $cms_setting['fav_icon']); ?>">
	<?php endif; ?>
	<title><?php echo isset($page_data['page_title']) ? $page_data['page_title'] : (isset($cms_setting['application_title']) ? $cms_setting['application_title'] : 'Smart School'); ?></title>

	<!-- Google Fonts: Hind Siliguri (Bengali) + Inter (Latin) -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

	<!-- Bootstrap 5.3 -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
	<!-- Font Awesome 6.4 -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
	<!-- AOS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
	<style>
		/* Fallback: never hide content if AOS fails to load / init.
		   AOS itself adds the `aos-init` class once it runs and overrides this. */
		html:not(.aos-active) [data-aos], html.no-js [data-aos] { opacity: 1 !important; transform: none !important; }
		[data-aos] { transition: opacity .6s ease, transform .6s ease; }
	</style>
	<!-- Swiper 10 -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
	<!-- GLightbox -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<style>
/* === DESIGN TOKENS — CMS-editable via $cms_setting === */
:root {
	--primary:        <?php echo $thm_primary; ?>;
	--primary-light:  <?php echo $thm_primary_light; ?>;
	--primary-dark:   <?php echo $thm_primary_dark; ?>;
	--accent:         <?php echo $thm_accent; ?>;
	--accent-light:   <?php echo $thm_accent_light; ?>;
	--accent-dark:    <?php echo $thm_accent_dark; ?>;
	--accent-glow:    <?php echo $thm_accent_glow; ?>;
	--on-primary:     <?php echo $thm_on_primary; ?>;
	--on-accent:      <?php echo $thm_on_accent; ?>;
	--menu-bg:        <?php echo $thm_menu_bg; ?>;
	--on-menu:        <?php echo $thm_on_menu; ?>;
	--text-dark:      <?php echo !empty($cms_setting['text_color']) ? $cms_setting['text_color'] : '#1a1a2e'; ?>;
	--text-muted:     <?php echo !empty($cms_setting['text_secondary_color']) ? $cms_setting['text_secondary_color'] : '#5a6270'; ?>;
	--bg-light:       #f4f6fb;
	--bg-section:     #eef1f8;
	--white:          #ffffff;
	--border:         #dde3f0;
	--footer-bg:      <?php echo !empty($cms_setting['footer_background_color']) ? $cms_setting['footer_background_color'] : $thm_primary_dark; ?>;
	--footer-text:    <?php echo !empty($cms_setting['footer_text_color']) ? $cms_setting['footer_text_color'] : '#ffffff'; ?>;
	--copyright-bg:   <?php echo !empty($cms_setting['copyright_bg_color']) ? $cms_setting['copyright_bg_color'] : thm_shade($thm_primary_dark, -25); ?>;
	--copyright-text: <?php echo !empty($cms_setting['copyright_text_color']) ? $cms_setting['copyright_text_color'] : '#ffffff'; ?>;
	--shadow-sm: 0 2px 8px rgba(0,0,0,0.05);
	--shadow-md: 0 4px 12px rgba(0,0,0,0.08);
	--shadow-lg: 0 10px 30px rgba(0,0,0,0.12);
	--radius-sm: 8px;
	--radius-md: <?php echo !empty($cms_setting['border_radius']) ? $cms_setting['border_radius'] : '12px'; ?>;
	--radius-lg: 18px;
	--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* === RESET / TYPOGRAPHY === */
* { box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
	font-family: 'Hind Siliguri', 'Inter', 'SolaimanLipiNormal', system-ui, -apple-system, sans-serif !important;
	background: var(--bg-light) !important;
	color: var(--text-dark) !important;
	font-size: 15px !important;
	line-height: 1.7 !important;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
	margin: 0;
}
h1, h2, h3, h4, h5, h6 { font-family: 'Hind Siliguri', 'Inter', sans-serif; font-weight: 700; color: var(--text-dark); }
a { color: var(--primary); text-decoration: none; transition: var(--transition); }
a:hover { color: var(--accent-dark); }
img { max-width: 100%; height: auto; }
.container { max-width: 1280px; padding-left: 18px; padding-right: 18px; }

/* === TOP UTILITY BAR === */
.t2-topbar {
	background: linear-gradient(90deg, var(--primary-dark), var(--primary), var(--primary-dark));
	color: var(--on-primary);
	font-size: 13px;
	padding: 7px 0;
	position: relative;
}
.t2-topbar::after {
	content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 2px;
	background: linear-gradient(90deg, var(--accent), var(--accent-light), var(--accent));
}
.t2-topbar a { color: var(--on-primary); transition: var(--transition); }
.t2-topbar a:hover { color: var(--accent); }
.t2-topbar i.text-warning, .t2-topbar .text-warning { color: var(--accent) !important; }
.t2-topbar .eiin-badge {
	display: inline-flex; align-items: center;
	background: var(--accent); color: var(--on-accent);
	font-weight: 700; padding: 3px 12px; border-radius: 4px;
	font-size: 12.5px; letter-spacing: 0.3px;
}

/* === HEADER / BRAND BANNER === */
.t2-header {
	background:
		radial-gradient(circle at 80% 50%, var(--accent-glow) 0%, transparent 60%),
		linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
	color: var(--on-primary);
	padding: 22px 0;
	position: relative; overflow: hidden;
}
.t2-header::before {
	content: ''; position: absolute; top: -50%; right: -10%;
	width: 60%; height: 200%;
	background: radial-gradient(circle, <?php echo thm_rgba($thm_accent, 0.06); ?> 0%, transparent 60%);
	pointer-events: none;
}
.t2-logo-wrap { display: flex; align-items: center; gap: 18px; position: relative; z-index: 2; color: inherit; }
.t2-logo-wrap:hover { color: inherit; }
.t2-logo-img {
	width: 78px; height: 78px; object-fit: contain;
	background: var(--white); border-radius: 50%; padding: 4px;
	border: 3px solid var(--accent);
	box-shadow: 0 0 0 4px <?php echo thm_rgba($thm_accent, 0.18); ?>;
	flex-shrink: 0;
}
.t2-school-name .name-bn {
	font-size: 1.5rem; font-weight: 700; margin: 0;
	color: var(--on-primary); line-height: 1.25; letter-spacing: -0.3px;
}
.t2-school-name .name-en {
	font-size: 0.95rem; font-weight: 500;
	color: <?php echo $thm_on_primary === '#ffffff' ? thm_rgba('#ffffff', 0.85) : thm_rgba('#000000', 0.75); ?>;
	font-family: 'Inter', sans-serif; letter-spacing: 0.2px; margin-top: 2px;
}
.t2-school-name .info-row {
	display: flex; flex-wrap: wrap; gap: 18px; margin-top: 8px; font-size: 13px;
	color: <?php echo $thm_on_primary === '#ffffff' ? thm_rgba('#ffffff', 0.80) : thm_rgba('#000000', 0.70); ?>;
}
.t2-school-name .info-row span { display: inline-flex; align-items: center; gap: 6px; }
.t2-school-name .info-row i { color: var(--accent); }

/* === STICKY NAVBAR + DROPDOWNS === */
.t2-navbar {
	background: var(--menu-bg);
	box-shadow: var(--shadow-sm);
	position: sticky; top: 0; z-index: 1000;
	border-bottom: 3px solid var(--accent);
}
.t2-nav-inner { display: flex; align-items: stretch; position: relative; }
.t2-nav-home {
	background: linear-gradient(135deg, var(--accent), var(--accent-light));
	color: var(--on-accent) !important;
	padding: 0 20px; display: flex; align-items: center;
	font-weight: 700; font-size: 15px;
	transition: var(--transition); text-decoration: none;
}
.t2-nav-home:hover { background: linear-gradient(135deg, var(--accent-dark), var(--accent)); color: var(--on-accent) !important; }
.t2-nav-list { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; }
.t2-nav-list > li { position: relative; }
.t2-nav-list > li > a {
	display: flex; align-items: center; gap: 4px;
	padding: 16px 14px; font-size: 14px; font-weight: 600;
	color: var(--on-menu); white-space: nowrap;
	border-bottom: 3px solid transparent; transition: var(--transition);
}
.t2-nav-list > li > a:hover, .t2-nav-list > li.active > a {
	color: var(--primary); border-bottom-color: var(--accent);
}
.t2-nav-list > li > a .fa-chevron-down { font-size: 10px; opacity: 0.6; transition: var(--transition); }
.t2-nav-list > li:hover > a .fa-chevron-down { transform: rotate(180deg); }
.t2-dropdown {
	display: none; position: absolute; top: 100%; left: 0;
	background: var(--white); min-width: 240px;
	border-radius: 0 0 var(--radius-md) var(--radius-md);
	box-shadow: var(--shadow-lg); z-index: 9999;
	border-top: 3px solid var(--accent);
	animation: dropDown 0.2s ease;
	padding: 0; margin: 0; list-style: none;
}
@keyframes dropDown {
	from { opacity: 0; transform: translateY(-8px); }
	to { opacity: 1; transform: translateY(0); }
}
.t2-nav-list > li:hover > .t2-dropdown { display: block; }
.t2-dropdown li { list-style: none; }
.t2-dropdown li a {
	display: flex; align-items: center; gap: 8px;
	padding: 10px 18px; font-size: 13.5px; font-weight: 500;
	color: var(--text-dark);
	border-bottom: 1px solid var(--border);
	transition: var(--transition);
}
.t2-dropdown li:last-child a { border-bottom: none; }
.t2-dropdown li a:hover { background: var(--bg-section); color: var(--primary); padding-left: 26px; }
.t2-dropdown li a::before {
	content: ''; width: 6px; height: 6px; border-radius: 50%;
	background: var(--accent); flex-shrink: 0;
}
.t2-nav-toggle {
	display: none; background: var(--primary); color: var(--on-primary);
	border: none; padding: 10px 14px; border-radius: var(--radius-sm);
	font-size: 18px; cursor: pointer; margin-left: auto; align-items: center;
}

/* === MARQUEE BAR === */
.t2-marquee-bar { background: var(--white); border-bottom: 1px solid var(--border); padding: 9px 0; }
.t2-marquee-wrap { display: flex; align-items: center; gap: 14px; }
.t2-marquee-label {
	background: linear-gradient(135deg, var(--accent), var(--accent-light));
	color: var(--on-accent); font-weight: 700; font-size: 13px;
	padding: 4px 14px; border-radius: 20px; flex-shrink: 0;
	display: inline-flex; align-items: center; gap: 4px;
}
.t2-marquee-content {
	display: inline-flex; gap: 60px;
	animation: marquee 35s linear infinite;
	white-space: nowrap; color: var(--text-dark); font-size: 14px;
}
.t2-marquee-content a { color: var(--text-dark); font-weight: 500; }
.t2-marquee-content a:hover { color: var(--primary); }
@keyframes marquee { 0%{transform:translateX(0);} 100%{transform:translateX(-50%);} }

/* === HERO SLIDER === */
.t2-hero { position: relative; margin-bottom: 0; }
.t2-hero-swiper { width: 100%; max-height: 520px; overflow: hidden; border-bottom: 4px solid var(--accent); }
.t2-hero-swiper .swiper-slide { position: relative; background: var(--primary-dark); }
.t2-hero-swiper .swiper-slide img { width: 100%; height: 520px; object-fit: cover; display: block; }
.t2-hero-caption {
	position: absolute; left: 0; right: 0; bottom: 0;
	padding: 24px 32px 28px;
	background: linear-gradient(0deg, <?php echo thm_rgba($thm_primary_dark, 0.85); ?> 0%, transparent 100%);
	color: var(--on-primary);
}
.t2-hero-caption h2 { color: var(--on-primary); font-size: 1.8rem; font-weight: 700; margin: 0; max-width: 800px; }
.t2-hero-swiper .swiper-button-prev, .t2-hero-swiper .swiper-button-next {
	background: var(--accent); color: var(--on-accent);
	width: 44px; height: 44px; border-radius: 50%;
	box-shadow: var(--shadow-md);
}
.t2-hero-swiper .swiper-button-prev::after, .t2-hero-swiper .swiper-button-next::after { font-size: 16px; font-weight: 800; }
.t2-hero-swiper .swiper-pagination-bullet { background: var(--white); opacity: 0.6; width: 10px; height: 10px; }
.t2-hero-swiper .swiper-pagination-bullet-active { background: var(--accent); opacity: 1; width: 26px; border-radius: 5px; }

/* === STATS STRIP === */
.t2-stats {
	background: linear-gradient(135deg, var(--primary-dark), var(--primary), var(--primary-light));
	color: var(--on-primary); padding: 36px 0; position: relative; overflow: hidden;
}
.t2-stats::after {
	content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 4px;
	background: linear-gradient(90deg, var(--accent), var(--accent-light), var(--accent));
}
.t2-stat-item { padding: 8px 20px; position: relative; }
.t2-stat-divider { border-left: 1px solid <?php echo thm_rgba('#ffffff', 0.15); ?>; }
.t2-stat-item .stat-num {
	font-family: 'Inter', sans-serif; font-size: 2.4rem; font-weight: 800;
	color: var(--accent); line-height: 1; margin-bottom: 6px; letter-spacing: -1px;
}
.t2-stat-item .stat-label {
	font-size: 14px; font-weight: 500;
	color: <?php echo $thm_on_primary === '#ffffff' ? thm_rgba('#ffffff', 0.85) : thm_rgba('#000000', 0.75); ?>;
	display: flex; align-items: center; justify-content: center; gap: 4px;
}
.t2-stat-item .stat-label i { color: var(--accent); }

/* === MAIN WRAP + CARDS === */
.t2-main-wrap { padding: 36px 0 0; background: var(--bg-light); }
.t2-card { background: var(--white); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); overflow: hidden; transition: var(--transition); }
.t2-card:hover { box-shadow: var(--shadow-md); }
.t2-card-header {
	background: linear-gradient(135deg, var(--primary), var(--primary-light));
	color: var(--on-primary); padding: 14px 20px; font-weight: 700; font-size: 15px;
	display: flex; align-items: center; gap: 10px;
	border-bottom: 3px solid var(--accent);
}
.t2-card-header i { color: var(--accent); font-size: 16px; }
.t2-card-header a { color: var(--accent); font-weight: 600; }
.t2-card-body { padding: 22px; }

.t2-quick-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.t2-quick-item {
	display: flex; flex-direction: column; align-items: center; gap: 8px;
	padding: 16px 8px; background: var(--bg-section);
	border-radius: var(--radius-sm); color: var(--text-dark);
	font-weight: 600; font-size: 13px; text-align: center;
	transition: var(--transition); text-decoration: none;
	border: 1px solid var(--border);
}
.t2-quick-item:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); color: var(--primary); border-color: var(--accent); }
.t2-quick-item .icon {
	width: 54px; height: 54px; border-radius: 50%;
	display: flex; align-items: center; justify-content: center;
	font-size: 22px;
	background: linear-gradient(135deg, var(--primary), var(--primary-dark));
	color: var(--on-primary);
}
.t2-quick-item:hover .icon { background: linear-gradient(135deg, var(--accent), var(--accent-light)); color: var(--on-accent); }

.t2-notice-list { list-style: none; margin: 0; padding: 0; }
.t2-notice-item { display: flex; gap: 14px; padding: 14px 0; border-bottom: 1px dashed var(--border); align-items: stretch; }
.t2-notice-item:last-child { border-bottom: none; padding-bottom: 0; }
.t2-notice-item:first-child { padding-top: 0; }
.t2-notice-date {
	background: linear-gradient(135deg, var(--primary), var(--primary-light));
	color: var(--on-primary); border-radius: var(--radius-sm);
	padding: 8px 12px; min-width: 64px; text-align: center; flex-shrink: 0;
	display: flex; flex-direction: column; justify-content: center;
	border-top: 3px solid var(--accent);
}
.t2-notice-date .day { font-size: 1.4rem; font-weight: 800; line-height: 1; font-family: 'Inter', sans-serif; }
.t2-notice-info { flex: 1; display: flex; align-items: center; }
.t2-notice-info a { color: var(--text-dark); font-weight: 600; font-size: 14px; line-height: 1.5; display: block; }
.t2-notice-info a:hover { color: var(--primary); }

.t2-msg-grid-item {
	text-align: center; background: var(--white);
	border-radius: var(--radius-md); padding: 22px 16px;
	box-shadow: var(--shadow-sm); border: 1px solid var(--border);
	border-top: 4px solid var(--accent);
	transition: var(--transition); height: 100%;
}
.t2-msg-grid-item:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
.t2-msg-grid-photo {
	width: 110px; height: 110px; object-fit: cover;
	border-radius: 50%; border: 3px solid var(--accent);
	margin: 0 auto 14px; display: block;
	box-shadow: 0 4px 12px <?php echo $thm_primary_rgba; ?>;
}
.t2-msg-grid-name { font-weight: 700; font-size: 15px; color: var(--primary); margin-bottom: 2px; }
.t2-msg-grid-desig { font-size: 12.5px; color: var(--text-muted); margin-bottom: 12px; font-weight: 500; }
.t2-msg-grid-text {
	font-size: 13.5px; color: var(--text-muted);
	text-align: justify; line-height: 1.7;
	display: -webkit-box; -webkit-line-clamp: 6; -webkit-box-orient: vertical; overflow: hidden;
}
.t2-msg-grid-more {
	display: inline-block; margin-top: 14px;
	padding: 6px 16px; border: 1px solid var(--border);
	border-radius: var(--radius-sm); color: var(--primary);
	font-size: 13px; font-weight: 600;
	transition: var(--transition);
}
.t2-msg-grid-more:hover { background: var(--primary); color: var(--on-primary); border-color: var(--primary); }

.t2-service-box {
	background: var(--white); border-radius: var(--radius-md);
	overflow: hidden; box-shadow: var(--shadow-sm);
	border: 1px solid var(--border); transition: var(--transition); height: 100%;
}
.t2-service-box:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
.t2-service-box-head {
	background: linear-gradient(135deg, var(--primary), var(--primary-light));
	color: var(--on-primary); padding: 12px 16px;
	font-weight: 700; font-size: 14px;
	display: flex; align-items: center; gap: 8px;
}
.t2-service-box-head i { color: var(--accent); }
.t2-service-box-links { padding: 12px 14px; }
.t2-service-box-links a {
	display: flex; align-items: center; gap: 8px;
	padding: 7px 0; font-size: 13.5px; color: var(--text-dark);
	border-bottom: 1px dashed var(--border); transition: var(--transition);
}
.t2-service-box-links a:last-child { border-bottom: none; }
.t2-service-box-links a:hover { color: var(--primary); padding-left: 6px; }
.t2-service-box-links a::before {
	content: '\f054'; font-family: 'Font Awesome 6 Free';
	font-weight: 900; font-size: 10px; color: var(--accent);
}

.t2-extra-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
.t2-extra-card {
	background: var(--white); border-radius: var(--radius-md);
	padding: 16px; box-shadow: var(--shadow-sm);
	border: 1px solid var(--border); border-left: 4px solid var(--accent);
	transition: var(--transition);
}
.t2-extra-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
.t2-extra-card .icon {
	width: 44px; height: 44px;
	background: linear-gradient(135deg, var(--primary), var(--primary-dark));
	color: var(--on-primary); border-radius: 50%;
	display: flex; align-items: center; justify-content: center;
	font-size: 18px; margin-bottom: 10px;
}
.t2-extra-card h6 { font-size: 14px; font-weight: 700; color: var(--primary); margin: 0 0 4px; }
.t2-extra-card p { font-size: 13px; color: var(--text-muted); margin: 0; line-height: 1.5; }

.t2-gallery-box {
	display: block; border-radius: var(--radius-sm);
	overflow: hidden; position: relative;
	box-shadow: var(--shadow-sm); aspect-ratio: 4 / 3;
	background: var(--bg-section); transition: var(--transition);
}
.t2-gallery-box:hover { box-shadow: var(--shadow-md); transform: scale(1.02); }
.t2-gallery-box img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.t2-gallery-box:hover img { transform: scale(1.08); }
.t2-gallery-box::after {
	content: '\f00e'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
	position: absolute; top: 50%; left: 50%;
	transform: translate(-50%, -50%) scale(0);
	color: var(--white); background: var(--accent);
	width: 44px; height: 44px; border-radius: 50%;
	display: flex; align-items: center; justify-content: center;
	transition: var(--transition); font-size: 16px;
}
.t2-gallery-box:hover::after { transform: translate(-50%, -50%) scale(1); }

.t2-teacher-card {
	background: var(--white); border-radius: var(--radius-md);
	overflow: hidden; box-shadow: var(--shadow-sm);
	border: 1px solid var(--border); transition: var(--transition);
	text-align: center; height: 100%;
}
.t2-teacher-card:hover { box-shadow: var(--shadow-md); transform: translateY(-4px); }
.t2-teacher-photo {
	background: linear-gradient(135deg, var(--primary), var(--primary-dark));
	padding: 24px 12px 18px; position: relative;
}
.t2-teacher-photo::after { content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 3px; background: var(--accent); }
.t2-teacher-photo img {
	width: 130px; height: 130px; object-fit: cover;
	border-radius: 50%; border: 3px solid var(--accent);
	box-shadow: 0 4px 12px <?php echo thm_rgba('#000000', 0.20); ?>;
}
.t2-teacher-info { padding: 16px 14px; }
.t2-teacher-info h5 { font-size: 15px; font-weight: 700; color: var(--primary); margin: 0 0 4px; }
.t2-teacher-info p { font-size: 12.5px; color: var(--text-muted); margin: 0; }

/* === SIDEBAR WIDGETS === */
.t2-sidebar-widget {
	background: var(--white); border-radius: var(--radius-md);
	box-shadow: var(--shadow-sm); overflow: hidden;
	margin-bottom: 22px; transition: var(--transition);
}
.t2-sidebar-widget:hover { box-shadow: var(--shadow-md); }
.t2-widget-head {
	background: linear-gradient(135deg, var(--primary), var(--primary-light));
	color: var(--on-primary); padding: 12px 18px;
	font-weight: 700; font-size: 14px;
	display: flex; align-items: center; gap: 8px;
	border-bottom: 3px solid var(--accent);
}
.t2-widget-head i { color: var(--accent); }
.t2-widget-body { padding: 16px; }

.t2-msg-photo {
	width: 100px; height: 110px; object-fit: cover;
	border-radius: var(--radius-sm); border: 2px solid var(--accent);
	display: block; margin: 0 auto 12px;
}
.t2-msg-name { text-align: center; font-weight: 700; font-size: 14px; color: var(--primary); margin-bottom: 2px; }
.t2-msg-desig { text-align: center; font-size: 12px; color: var(--text-muted); margin-bottom: 10px; }
.t2-msg-text {
	font-size: 13px; color: var(--text-muted);
	text-align: justify; line-height: 1.7;
	display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical; overflow: hidden;
}
.t2-msg-more {
	display: block; text-align: center;
	margin-top: 10px; color: var(--primary);
	font-size: 13px; font-weight: 600; padding: 6px;
	border: 1px solid var(--border); border-radius: var(--radius-sm);
	transition: var(--transition);
}
.t2-msg-more:hover { background: var(--primary); color: var(--on-primary); }

.t2-hotline-items { display: flex; flex-direction: column; gap: 8px; }
.t2-hotline-item {
	display: flex; align-items: center; gap: 10px;
	background: var(--bg-section); border-radius: var(--radius-sm);
	padding: 9px 12px; border-left: 3px solid var(--primary);
	font-size: 13px; font-weight: 600; color: var(--text-dark);
	transition: var(--transition);
}
.t2-hotline-item i { color: var(--primary); width: 16px; }
.t2-hotline-item:hover { border-left-color: var(--accent); background: var(--white); box-shadow: var(--shadow-sm); color: var(--primary); }

.t2-section-title {
	display: inline-block; font-size: 1.4rem; font-weight: 700;
	color: var(--primary); padding-bottom: 8px;
	border-bottom: 3px solid var(--accent); margin-bottom: 22px;
}

/* === FOOTER === */
.t2-footer {
	background: linear-gradient(135deg, var(--primary-dark) 0%, var(--footer-bg) 40%, var(--primary) 100%);
	color: <?php echo thm_rgba('#ffffff', 0.85); ?>;
	padding-top: 60px; margin-top: 50px;
	position: relative; overflow: hidden;
}
.t2-footer::before {
	content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
	background: linear-gradient(90deg, var(--accent), var(--accent-light), var(--accent));
}
.t2-footer::after {
	content: ''; position: absolute; bottom: 60px; right: -100px;
	width: 300px; height: 300px; border-radius: 50%;
	background: radial-gradient(circle, <?php echo thm_rgba($thm_accent, 0.05); ?> 0%, transparent 70%);
	pointer-events: none;
}
.t2-footer h5 {
	color: var(--accent); font-size: 1rem; font-weight: 700;
	margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
	text-transform: uppercase; letter-spacing: 0.5px;
}
.t2-footer h5::after { content: ''; flex: 1; height: 1px; background: <?php echo thm_rgba($thm_accent, 0.3); ?>; }
.t2-footer p, .t2-footer li, .t2-footer a {
	color: <?php echo thm_rgba('#ffffff', 0.75); ?>;
	font-size: 13.5px; line-height: 1.8;
}
.t2-footer a:hover { color: var(--accent); }
.t2-footer ul { list-style: none; padding: 0; margin: 0; }
.t2-footer ul li {
	padding: 5px 0; border-bottom: 1px solid <?php echo thm_rgba('#ffffff', 0.06); ?>;
	display: flex; align-items: flex-start; gap: 8px;
}
.t2-footer ul li::before {
	content: '\f054'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
	font-size: 10px; color: var(--accent); margin-top: 5px; flex-shrink: 0;
}
.t2-footer-info p { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; }
.t2-footer-info p i { color: var(--accent); margin-top: 3px; width: 16px; flex-shrink: 0; }
.t2-footer-brand { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
.t2-footer-brand img {
	width: 60px; height: 60px; object-fit: contain;
	background: var(--white); border-radius: 50%; padding: 4px;
	border: 2px solid var(--accent);
}
.t2-footer-brand .brand-name { font-size: 1rem; font-weight: 700; color: var(--white); line-height: 1.3; }
.t2-footer-brand .brand-name small { color: <?php echo thm_rgba('#ffffff', 0.65); ?>; font-size: 12px; font-weight: 400; display: block; }

.t2-social-row { display: flex; gap: 8px; margin-top: 14px; }
.t2-social-row a {
	width: 36px; height: 36px; border-radius: 50%;
	background: <?php echo thm_rgba('#ffffff', 0.08); ?>;
	color: var(--white);
	display: flex; align-items: center; justify-content: center;
	font-size: 14px; transition: var(--transition);
}
.t2-social-row a:hover { background: var(--accent); color: var(--on-accent); transform: translateY(-3px); }

.t2-founded-box {
	background: <?php echo thm_rgba('#ffffff', 0.05); ?>;
	border: 1px solid <?php echo thm_rgba($thm_accent, 0.25); ?>;
	border-radius: var(--radius-sm); padding: 14px 16px; margin-top: 12px;
	display: flex; align-items: center; gap: 14px;
}
.t2-founded-box .yr {
	font-family: 'Inter', sans-serif; font-size: 1.7rem; font-weight: 800;
	color: var(--accent); line-height: 1;
}
.t2-founded-box .label {
	font-size: 12.5px; color: <?php echo thm_rgba('#ffffff', 0.85); ?>;
	font-weight: 500; line-height: 1.4;
}
.t2-footer-bottom {
	background: var(--copyright-bg);
	border-top: 1px solid <?php echo thm_rgba('#ffffff', 0.1); ?>;
	padding: 16px 0; margin-top: 40px; text-align: center;
}
.t2-footer-bottom p { font-size: 13px; color: var(--copyright-text); margin: 0; }
.t2-footer-bottom a { color: var(--accent); }

#backToTop {
	position: fixed; bottom: 30px; right: 30px;
	width: 44px; height: 44px;
	background: linear-gradient(135deg, var(--primary), var(--accent));
	color: var(--white); border: none; border-radius: 50%;
	font-size: 18px; cursor: pointer;
	box-shadow: 0 4px 15px <?php echo $thm_primary_rgba; ?>;
	z-index: 9999;
	display: flex; align-items: center; justify-content: center;
	transition: var(--transition);
	opacity: 0; transform: translateY(20px); pointer-events: none;
}
#backToTop.show { opacity: 1; transform: translateY(0); pointer-events: all; }
#backToTop:hover { transform: translateY(-4px); box-shadow: 0 8px 25px <?php echo $thm_primary_rgba; ?>; }

/* === RESPONSIVE === */
@media (max-width: 1199px) { .t2-quick-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 991px) {
	.t2-nav-toggle { display: flex; }
	.t2-nav-list {
		display: none; position: absolute; top: 100%; left: 0; right: 0;
		background: var(--menu-bg); flex-direction: column;
		box-shadow: var(--shadow-lg); max-height: 70vh; overflow-y: auto;
		border-top: 1px solid var(--border); z-index: 9998;
	}
	.t2-nav-list.show { display: flex; }
	.t2-nav-list > li { width: 100%; }
	.t2-nav-list > li > a { width: 100%; padding: 14px 18px; border-bottom: 1px solid var(--border); }
	.t2-dropdown {
		position: static; display: none; box-shadow: none;
		border-top: none; border-left: 3px solid var(--accent);
		border-radius: 0; margin: 0; padding: 0; background: var(--bg-section);
	}
	.t2-nav-list > li.open > .t2-dropdown { display: block; }
	.t2-school-name .name-bn { font-size: 1.2rem; }
	.t2-school-name .name-en { font-size: 0.85rem; }
	.t2-hero-swiper { max-height: 360px; }
	.t2-hero-swiper .swiper-slide img { height: 360px; }
	.t2-hero-caption { padding: 16px 20px; }
	.t2-hero-caption h2 { font-size: 1.3rem; }
}
@media (max-width: 767px) {
	.t2-quick-grid { grid-template-columns: repeat(2, 1fr); }
	.t2-extra-grid { grid-template-columns: 1fr; }
	.t2-stat-divider { border-left: none; border-top: 1px solid <?php echo thm_rgba('#ffffff', 0.15); ?>; padding-top: 12px; }
	.t2-stat-item .stat-num { font-size: 1.8rem; }
	.t2-logo-img { width: 60px; height: 60px; }
	.t2-school-name .name-bn { font-size: 1.05rem; }
	.t2-school-name .info-row { font-size: 12px; gap: 10px; }
	.t2-section-title { font-size: 1.15rem; }
	.t2-hero-swiper { max-height: 240px; }
	.t2-hero-swiper .swiper-slide img { height: 240px; }
	.t2-card-body { padding: 16px; }
	#backToTop { bottom: 18px; right: 18px; width: 38px; height: 38px; font-size: 15px; }
}
</style>
</head>

<body>

<?php $this->load->view('home/layout/header'); ?>

<main id="main-content">
	<?php echo $main_contents; ?>
</main>

<?php $this->load->view('home/layout/footer'); ?>

<button id="backToTop" aria-label="Back to top"><i class="fa fa-arrow-up"></i></button>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<script>
if (document.querySelector('.t2-hero-swiper')) {
	new Swiper('.t2-hero-swiper', {
		loop: true,
		autoplay: { delay: 5000, disableOnInteraction: false },
		pagination: { el: '.t2-hero-swiper .swiper-pagination', clickable: true },
		navigation: { nextEl: '.t2-hero-swiper .swiper-button-next', prevEl: '.t2-hero-swiper .swiper-button-prev' },
		effect: 'fade',
		fadeEffect: { crossFade: true }
	});
}
if (window.AOS) { AOS.init({ once: true, duration: 700, offset: 60 }); }
if (window.GLightbox) { GLightbox({ selector: '.t2-gallery-box' }); }

document.addEventListener('DOMContentLoaded', function () {
	var toggle = document.getElementById('navToggle');
	var list   = document.getElementById('mainNav');
	if (toggle && list) {
		toggle.addEventListener('click', function () { list.classList.toggle('show'); });
	}
	document.querySelectorAll('.t2-nav-list > li > a.has-drop').forEach(function (a) {
		a.addEventListener('click', function (e) {
			if (window.innerWidth <= 991) {
				e.preventDefault();
				a.parentElement.classList.toggle('open');
			}
		});
	});
	var btn = document.getElementById('backToTop');
	if (btn) {
		window.addEventListener('scroll', function () {
			if (window.scrollY > 300) btn.classList.add('show'); else btn.classList.remove('show');
		});
		btn.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
	}
});
</script>

<?php
if (!empty($cms_setting['google_analytics'])) {
	echo $cms_setting['google_analytics'];
}
?>
<script type="text/javascript">
	var base_url = "<?php echo base_url(); ?>";
	<?php if (function_exists('csrf_jquery_token')): ?>
	var csrfData = <?php echo json_encode(csrf_jquery_token()); ?>;
	if (window.jQuery) { jQuery(function ($) { $.ajaxSetup({ data: csrfData }); }); }
	<?php endif; ?>
</script>

</body>
</html>
