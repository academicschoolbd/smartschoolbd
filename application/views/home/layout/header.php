<?php
$application_title = !empty($cms_setting['application_title']) ? $cms_setting['application_title'] : '';
$school_name_en    = !empty($cms_setting['application_title_en']) ? $cms_setting['application_title_en'] : '';
$mobile_no = !empty($cms_setting['mobile_no']) ? $cms_setting['mobile_no'] : '';
$email     = !empty($cms_setting['email']) ? $cms_setting['email'] : '';
$address   = !empty($cms_setting['address']) ? strip_tags($cms_setting['address']) : '';
$eienn     = !empty($cms_setting['eienn_code']) ? $cms_setting['eienn_code'] : '';
$facebook_url = !empty($cms_setting['facebook_page_url']) ? 'https://www.facebook.com/' . $cms_setting['facebook_page_url'] : '';

$loginURL = base_url('authentication');
if (!empty($cms_setting['url_alias'])) {
    $loginURL = base_url($cms_setting['url_alias'] . '/authentication');
    if (isset($this->app_lib) && method_exists($this->app_lib, 'isExistingAddon')
        && $this->app_lib->isExistingAddon('saas') && $this->db->table_exists('custom_domain')) {
        $getDomain = $this->home_model->getCurrentDomain();
        if (!empty($getDomain)) {
            $loginURL = base_url('authentication');
        }
    }
}
?>

<!-- =============================================================
     TOP UTILITY BAR (EIIN + address LEFT  •  phone/email/FB/login RIGHT)
============================================================== -->
<div class="t2-topbar">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <?php if (!empty($eienn)): ?>
                    <span class="eiin-badge"><i class="fa fa-id-badge me-1"></i> EIIN: <?php echo $eienn; ?></span>
                <?php endif; ?>
                <?php if (!empty($address)): ?>
                    <span><i class="fa fa-map-marker-alt me-1 text-warning"></i> <?php echo $address; ?></span>
                <?php endif; ?>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <?php if (!empty($mobile_no)): ?>
                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $mobile_no); ?>"><i class="fa fa-phone me-1 text-warning"></i> <?php echo $mobile_no; ?></a>
                <?php endif; ?>
                <?php if (!empty($email)): ?>
                    <a href="mailto:<?php echo $email; ?>"><i class="fa fa-envelope me-1 text-warning"></i> <?php echo $email; ?></a>
                <?php endif; ?>
                <?php if (!empty($facebook_url)): ?>
                    <a href="<?php echo $facebook_url; ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <a href="<?php echo $loginURL; ?>"><i class="fa fa-sign-in-alt me-1 text-warning"></i> Login</a>
            </div>
        </div>
    </div>
</div>

<!-- =============================================================
     BRAND BANNER (logo + Bengali name + English name + address/email)
============================================================== -->
<header class="t2-header">
    <div class="container">
        <a href="<?php echo base_url(); ?>" class="t2-logo-wrap">
            <?php if (!empty($cms_setting['main_logo'])): ?>
                <img class="t2-logo-img"
                     src="<?php echo base_url('uploads/frontend/images/' . $cms_setting['main_logo']); ?>"
                     alt="<?php echo htmlspecialchars($application_title); ?>">
            <?php endif; ?>
            <div class="t2-school-name">
                <?php if (!empty($application_title)): ?>
                    <h1 class="name-bn"><?php echo $application_title; ?></h1>
                <?php endif; ?>
                <?php if (!empty($school_name_en)): ?>
                    <div class="name-en"><?php echo htmlspecialchars($school_name_en); ?></div>
                <?php endif; ?>
                <div class="info-row">
                    <?php if (!empty($address)): ?>
                        <span><i class="fa fa-map-marker-alt"></i> <?php echo $address; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($email)): ?>
                        <span><i class="fa fa-envelope"></i> <?php echo $email; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($mobile_no)): ?>
                        <span><i class="fa fa-phone"></i> <?php echo $mobile_no; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </div>
</header>

<!-- =============================================================
     STICKY NAVBAR (gold home pill + main menu)
============================================================== -->
<nav class="t2-navbar">
    <div class="container">
        <div class="t2-nav-inner">
            <a href="<?php echo base_url(); ?>" class="t2-nav-home">
                <i class="fa fa-home me-2"></i> হোম
            </a>
            <ul class="t2-nav-list" id="mainNav">
                <?php
                $school = $this->uri->segment(1);
                $result = $this->home_model->menuList($school);
                $currentURL = base_url(uri_string());
                if (!empty($result) && is_array($result)) {
                    foreach ($result as $row) {
                        $active_menu = '';
                        $has_drop    = '';
                        $op_new_tab  = '';
                        $has_sub     = !empty($row['submenu']) && is_array($row['submenu']);
                        if ($has_sub) {
                            $has_drop = ' has-drop';
                            $arrayURL = array_column($row['submenu'], 'url');
                            if (in_array($currentURL, $arrayURL, true)) {
                                $active_menu = ' active';
                            }
                        }
                        if ($currentURL == $row['url']) {
                            $active_menu = ' active';
                        }
                        if (!empty($row['open_new_tab'])) {
                            $op_new_tab = " target='_blank'";
                        }
                        if (isset($cms_setting['online_admission']) && $cms_setting['online_admission'] == 0
                            && !empty($row['alias']) && $row['alias'] == 'admission') {
                            continue;
                        }
                ?>
                <li class="<?php echo trim($active_menu); ?>">
                    <a href="<?php echo $row['url']; ?>" class="<?php echo trim($has_drop); ?>" <?php echo $op_new_tab; ?>>
                        <?php echo $row['title']; ?>
                        <?php if ($has_sub): ?><i class="fa fa-chevron-down"></i><?php endif; ?>
                    </a>
                    <?php if ($has_sub): ?>
                    <ul class="t2-dropdown">
                        <?php foreach ($row['submenu'] as $row2):
                            $sub_op_new_tab = '';
                            if (!empty($row2['open_new_tab'])) {
                                $sub_op_new_tab = " target='_blank'";
                            }
                        ?>
                        <li><a href="<?php echo $row2['url']; ?>" <?php echo $sub_op_new_tab; ?>><?php echo $row2['title']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>
                <?php
                    }
                }
                ?>

                <?php if (!is_loggedin()): ?>
                <li>
                    <a href="#" class="has-drop">লগইন <i class="fa fa-chevron-down"></i></a>
                    <ul class="t2-dropdown">
                        <li><a href="<?php echo $loginURL; ?>">শিক্ষক লগ-ইন</a></li>
                        <li><a href="<?php echo $loginURL; ?>">স্টাফ লগ-ইন</a></li>
                        <li><a href="<?php echo $loginURL; ?>">শিক্ষার্থী লগ-ইন</a></li>
                        <li><a href="<?php echo $loginURL; ?>">অভিভাবক লগ-ইন</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li>
                    <a href="#" class="has-drop">ড্যাশবোর্ড <i class="fa fa-chevron-down"></i></a>
                    <ul class="t2-dropdown">
                        <li><a href="<?php echo base_url('dashboard'); ?>">আমার ড্যাশবোর্ড</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <button class="t2-nav-toggle" id="navToggle" type="button" aria-label="Toggle menu">
                <i class="fa fa-bars"></i>
            </button>
        </div>
    </div>
</nav>
