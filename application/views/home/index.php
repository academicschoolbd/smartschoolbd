<?php
$url_alias = !empty($cms_setting['url_alias']) ? $cms_setting['url_alias'] : 'frontend';
?>

<!-- =============================================================
     HERO SLIDER (Swiper 10)
============================================================== -->
<?php if (!empty($sliders)): ?>
<section class="t2-hero">
    <div class="swiper t2-hero-swiper">
        <div class="swiper-wrapper">
            <?php foreach ($sliders as $key => $value) {
                $elements = json_decode($value['elements'], true);
            ?>
            <div class="swiper-slide">
                <img src="<?php echo base_url('uploads/frontend/slider/' . $elements['image']); ?>" alt="<?php echo htmlspecialchars($value['title']); ?>">
                <?php if (!empty($value['title'])): ?>
                <div class="t2-hero-caption">
                    <h2><?php echo $value['title']; ?></h2>
                </div>
                <?php endif; ?>
            </div>
            <?php } ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>
<?php endif; ?>

<!-- =============================================================
     MARQUEE BAR (emergency notice)
============================================================== -->
<?php if (!empty($cms_setting['emergency_notice'])): ?>
<div class="t2-marquee-bar">
    <div class="container">
        <div class="t2-marquee-wrap">
            <span class="t2-marquee-label"><i class="fa fa-bullhorn"></i> জরুরী ঘোষণা</span>
            <div style="flex:1; overflow:hidden;">
                <marquee class="t2-marquee-content" direction="left" scrollamount="5" onmouseover="this.stop()" onmouseout="this.start()">
                    <a href="#"><i class="fa fa-exclamation-circle"></i> <?php echo $cms_setting['emergency_notice']; ?></a>
                </marquee>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- =============================================================
     STATS STRIP (Students / Teachers / Classes / Sessions)
============================================================== -->
<?php
$statsBranch = isset($branchID) ? $branchID : null;
$statsRow = [];
if ($statsBranch && $this->db->table_exists('students')) {
    $statsRow['students'] = (int) $this->db->where('branch_id', $statsBranch)->count_all_results('students');
}
if ($statsBranch && $this->db->table_exists('staff')) {
    $statsRow['teachers'] = (int) $this->db->where('branch_id', $statsBranch)->count_all_results('staff');
}
if ($statsBranch && $this->db->table_exists('classes')) {
    $statsRow['classes'] = (int) $this->db->where('branch_id', $statsBranch)->count_all_results('classes');
}
if ($statsBranch && $this->db->table_exists('sessions')) {
    $statsRow['sessions'] = (int) $this->db->where('branch_id', $statsBranch)->count_all_results('sessions');
}
if (!empty($statsRow)):
?>
<section class="t2-stats">
    <div class="container">
        <div class="row text-center g-0">
            <?php if (isset($statsRow['students'])): ?>
            <div class="col-md-3 col-6 t2-stat-item">
                <div class="stat-num"><?php echo $statsRow['students']; ?>+</div>
                <div class="stat-label"><i class="fa fa-user-graduate"></i> শিক্ষার্থী</div>
            </div>
            <?php endif; ?>
            <?php if (isset($statsRow['teachers'])): ?>
            <div class="col-md-3 col-6 t2-stat-item t2-stat-divider">
                <div class="stat-num"><?php echo $statsRow['teachers']; ?>+</div>
                <div class="stat-label"><i class="fa fa-chalkboard-teacher"></i> শিক্ষক ও স্টাফ</div>
            </div>
            <?php endif; ?>
            <?php if (isset($statsRow['classes'])): ?>
            <div class="col-md-3 col-6 t2-stat-item t2-stat-divider">
                <div class="stat-num"><?php echo $statsRow['classes']; ?>+</div>
                <div class="stat-label"><i class="fa fa-school"></i> শ্রেণি</div>
            </div>
            <?php endif; ?>
            <?php if (isset($statsRow['sessions'])): ?>
            <div class="col-md-3 col-6 t2-stat-item t2-stat-divider">
                <div class="stat-num"><?php echo $statsRow['sessions']; ?>+</div>
                <div class="stat-label"><i class="fa fa-calendar-alt"></i> শিক্ষাবর্ষ</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =============================================================
     MAIN WRAP (left = welcome / speeches / corner widgets   •   right = sidebar)
============================================================== -->
<section class="t2-main-wrap">
    <div class="container">

        <!-- Quick menu grid (4 icon cards) -->
        <?php
        $quickMenu = [
            [ 'icon' => 'fa fa-bullhorn',       'label' => 'নোটিশ',           'href' => base_url($url_alias . '/news') ],
            [ 'icon' => 'fa fa-file-alt',       'label' => 'পরীক্ষার ফলাফল', 'href' => base_url($url_alias . '/exam_results') ],
            [ 'icon' => 'fa fa-images',         'label' => 'ফটো গ্যালারী',    'href' => base_url($url_alias . '/gallery') ],
            [ 'icon' => 'fa fa-graduation-cap', 'label' => 'ভর্তি',           'href' => (isset($cms_setting['online_admission']) && $cms_setting['online_admission'] == 1) ? base_url($url_alias . '/admission') : base_url($url_alias . '/admission_information') ],
        ];
        ?>
        <div class="t2-quick-grid mb-4" data-aos="fade-up">
            <?php foreach ($quickMenu as $qm): ?>
                <a href="<?php echo $qm['href']; ?>" class="t2-quick-item">
                    <span class="icon"><i class="<?php echo $qm['icon']; ?>"></i></span>
                    <span><?php echo $qm['label']; ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="row g-4">
            <!-- =========================================== LEFT COLUMN -->
            <div class="col-lg-8 col-md-12">

                <!-- WELCOME / ABOUT -->
                <?php if (!empty($wellcome)) {
                    $w_elements = json_decode($wellcome['elements'], true);
                ?>
                <div class="t2-card mb-4" data-aos="fade-up">
                    <div class="t2-card-header">
                        <i class="fa fa-address-card"></i> <?php echo $wellcome['title']; ?>
                    </div>
                    <div class="t2-card-body">
                        <div class="row g-3 align-items-center">
                            <?php if (!empty($w_elements['image'])): ?>
                            <div class="col-md-5">
                                <img src="<?php echo base_url('uploads/frontend/home_page/' . $w_elements['image']); ?>"
                                     alt="<?php echo htmlspecialchars($wellcome['title']); ?>"
                                     style="width:100%; border-radius: var(--radius-sm); box-shadow: var(--shadow-sm);">
                            </div>
                            <div class="col-md-7">
                            <?php else: ?>
                            <div class="col-12">
                            <?php endif; ?>
                                <p style="text-align:justify; color: var(--text-muted);">
                                    <?php echo $wellcome['description']; ?>.....
                                </p>
                                <a href="<?php echo base_url($url_alias . '/about'); ?>"
                                   style="display:inline-block; margin-top:10px; padding:8px 18px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: var(--on-primary); border-radius: var(--radius-sm); font-weight:600; font-size:13px;">
                                    বিস্তারিত <i class="fa fa-angle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <!-- SPEECHES (Sovapoti / Principal) -->
                <?php if (!empty($teachers)) {
                    $t_elements = json_decode($teachers['elements'], true);
                ?>
                <h3 class="t2-section-title" data-aos="fade-up"><i class="fa fa-comment-dots"></i> বার্তা</h3>
                <div class="row g-3 mb-4" data-aos="fade-up">
                    <div class="col-md-6">
                        <div class="t2-msg-grid-item">
                            <?php if (!empty($cms_setting['sovapoti_images'])): ?>
                            <img class="t2-msg-grid-photo"
                                 src="<?php echo base_url('uploads/frontend/images/' . $cms_setting['sovapoti_images']); ?>"
                                 alt="সভাপতি">
                            <?php endif; ?>
                            <div class="t2-msg-grid-name">সভাপতি</div>
                            <div class="t2-msg-grid-desig">পরিচালনা পরিষদ</div>
                            <div class="t2-msg-grid-text"><?php echo strip_tags($teachers['title']); ?></div>
                            <a class="t2-msg-grid-more" href="<?php echo base_url($url_alias . '/sovapoti'); ?>">বিস্তারিত <i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="t2-msg-grid-item">
                            <?php if (!empty($cms_setting['principal_images'])): ?>
                            <img class="t2-msg-grid-photo"
                                 src="<?php echo base_url('uploads/frontend/images/' . $cms_setting['principal_images']); ?>"
                                 alt="প্রিন্সিপাল">
                            <?php endif; ?>
                            <div class="t2-msg-grid-name">প্রধান শিক্ষক</div>
                            <div class="t2-msg-grid-desig">প্রিন্সিপাল</div>
                            <div class="t2-msg-grid-text"><?php echo strip_tags($teachers['sovapoti_short_message']); ?></div>
                            <a class="t2-msg-grid-more" href="<?php echo base_url($url_alias . '/principal'); ?>">বিস্তারিত <i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <!-- FOUR INFO BOXES (Student/Teacher/Download/Academic corners) -->
                <h3 class="t2-section-title" data-aos="fade-up"><i class="fa fa-th-large"></i> তথ্য কেন্দ্র</h3>
                <div class="row g-3 mb-4" data-aos="fade-up">
                    <div class="col-md-6">
                        <div class="t2-service-box">
                            <div class="t2-service-box-head"><i class="fa fa-user-graduate"></i> শিক্ষার্থী ও অভিভাবকদের কর্ণার</div>
                            <div class="t2-service-box-links">
                                <a href="<?php echo base_url('authentication'); ?>">শিক্ষার্থী লগইন</a>
                                <a href="<?php echo base_url('authentication'); ?>">অভিভাবক লগইন</a>
                                <a href="<?php echo base_url($url_alias . '/exam_results'); ?>">অনলাইন রেজাল্ট</a>
                                <a href="<?php echo base_url($url_alias . '/admission'); ?>">অনলাইন ভর্তি</a>
                                <a href="<?php echo base_url($url_alias . '/admit_card'); ?>">এডমিট কার্ড</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="t2-service-box">
                            <div class="t2-service-box-head"><i class="fa fa-chalkboard-teacher"></i> শিক্ষক ও স্টাফদের কর্ণার</div>
                            <div class="t2-service-box-links">
                                <a href="<?php echo base_url('authentication'); ?>">শিক্ষক লগইন</a>
                                <a href="<?php echo base_url('authentication'); ?>">স্টাফ লগইন</a>
                                <a href="<?php echo base_url($url_alias . '/teachers'); ?>">শিক্ষক মণ্ডলী</a>
                                <a href="<?php echo base_url($url_alias . '/teachers'); ?>">স্টাফদের তালিকা</a>
                                <a href="<?php echo base_url('authentication'); ?>">লাইব্রেরিয়ান লগইন</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="t2-service-box">
                            <div class="t2-service-box-head"><i class="fa fa-download"></i> সকল ডাউনলোড</div>
                            <div class="t2-service-box-links">
                                <a href="<?php echo base_url($url_alias . '/exam_results'); ?>">মার্কশীট</a>
                                <a href="<?php echo base_url($url_alias . '/certificates'); ?>">সার্টিফিকেট</a>
                                <a href="<?php echo base_url('authentication'); ?>">পরীক্ষার রুটিন</a>
                                <a href="<?php echo base_url('authentication'); ?>">ভর্তি ফরম</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="t2-service-box">
                            <div class="t2-service-box-head"><i class="fa fa-book"></i> একাডেমিক তথ্য</div>
                            <div class="t2-service-box-links">
                                <a href="<?php echo base_url($url_alias . '/exam_results'); ?>">পরীক্ষার ফলাফল</a>
                                <a href="<?php echo base_url($url_alias . '/news'); ?>">নোটিশ</a>
                                <a href="<?php echo base_url($url_alias . '/gallery'); ?>">ফটো গ্যালারী</a>
                                <a href="<?php echo base_url($url_alias . '/video'); ?>">ভিডিও গ্যালারী</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /col-lg-8 -->

            <!-- =========================================== RIGHT SIDEBAR -->
            <div class="col-lg-4 col-md-12">

                <!-- Notice board widget -->
                <?php
                $news_list = $this->home_model->getLatestNews($branchID);
                if (!empty($news_list)):
                ?>
                <div class="t2-sidebar-widget" data-aos="fade-up">
                    <div class="t2-widget-head"><i class="fa fa-bullhorn"></i> নোটিশ বোর্ড
                        <a href="<?php echo base_url($url_alias . '/news'); ?>" style="margin-left:auto; color:var(--accent); font-size:12px;">সব দেখুন <i class="fa fa-angle-right"></i></a>
                    </div>
                    <div class="t2-widget-body" style="max-height:340px; overflow:hidden; position:relative;">
                        <marquee direction="up" scrollamount="2" height="320" onmouseover="this.stop()" onmouseout="this.start()" style="margin:0;">
                            <ul class="t2-notice-list">
                                <?php foreach ($news_list as $key => $value): ?>
                                <li class="t2-notice-item">
                                    <div class="t2-notice-date">
                                        <div class="day"><?php echo str_pad($key + 1, 2, '0', STR_PAD_LEFT); ?></div>
                                    </div>
                                    <div class="t2-notice-info">
                                        <a href="<?php echo base_url($url_alias . '/news_view/' . $value->alias); ?>"><?php echo $value->title; ?></a>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </marquee>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Hotline / Quick contact -->
                <div class="t2-sidebar-widget" data-aos="fade-up">
                    <div class="t2-widget-head"><i class="fa fa-headset"></i> জরুরি যোগাযোগ</div>
                    <div class="t2-widget-body">
                        <div class="t2-hotline-items">
                            <?php if (!empty($cms_setting['mobile_no'])): ?>
                            <a class="t2-hotline-item" href="tel:<?php echo preg_replace('/[^0-9+]/', '', $cms_setting['mobile_no']); ?>">
                                <i class="fa fa-phone"></i> <?php echo $cms_setting['mobile_no']; ?>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($cms_setting['email'])): ?>
                            <a class="t2-hotline-item" href="mailto:<?php echo $cms_setting['email']; ?>">
                                <i class="fa fa-envelope"></i> <?php echo $cms_setting['email']; ?>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($cms_setting['eienn_code'])): ?>
                            <div class="t2-hotline-item">
                                <i class="fa fa-id-badge"></i> EIIN: <?php echo $cms_setting['eienn_code']; ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($cms_setting['college_code'])): ?>
                            <div class="t2-hotline-item">
                                <i class="fa fa-hashtag"></i> Code: <?php echo $cms_setting['college_code']; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Important / Official links -->
                <div class="t2-sidebar-widget" data-aos="fade-up">
                    <div class="t2-widget-head"><i class="fa fa-link"></i> গুরুত্বপূর্ণ লিংক</div>
                    <div class="t2-widget-body">
                        <div class="t2-service-box-links" style="padding:0;">
                            <a href="https://www.dshe.gov.bd/" target="_blank" rel="noopener">মাধ্যমিক ও উচ্চ শিক্ষা অধিদপ্তর</a>
                            <a href="https://www.moedu.gov.bd/" target="_blank" rel="noopener">শিক্ষা মন্ত্রণালয়</a>
                            <a href="http://www.banbeis.gov.bd/" target="_blank" rel="noopener">ব্যানবেইস</a>
                            <a href="http://www.educationboard.gov.bd/" target="_blank" rel="noopener">শিক্ষা বোর্ড</a>
                            <a href="https://www.nctb.gov.bd/" target="_blank" rel="noopener">এনসিটিবি</a>
                        </div>
                    </div>
                </div>

                <!-- Facebook page -->
                <?php if (!empty($cms_setting['facebook_page_url'])): ?>
                <div class="t2-sidebar-widget" data-aos="fade-up">
                    <div class="t2-widget-head"><i class="fab fa-facebook-f"></i> ফেসবুকে আমরা</div>
                    <div class="t2-widget-body" style="padding:8px;">
                        <iframe
                            src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2F<?php echo $cms_setting['facebook_page_url']; ?>&tabs=timeline&width=320&height=300&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=true"
                            width="100%" height="300"
                            style="border:none; overflow:hidden; display:block;"
                            scrolling="no" frameborder="0" allowfullscreen="true"
                            allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- /col-lg-4 -->
        </div>

    </div>
</section>

<!-- =============================================================
     TEACHERS SECTION
============================================================== -->
<?php if (!empty($teachers)) {
    $t_elements = json_decode($teachers['elements'], true);
    $doctor_list = $this->home_model->get_teacher_list($t_elements['teacher_start'], $branchID);
    if (!empty($doctor_list)):
?>
<section class="t2-main-wrap" style="padding-top:14px;">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="t2-section-title" data-aos="fade-up"><i class="fa fa-chalkboard-teacher"></i> আমাদের শিক্ষকমণ্ডলী</h3>
        </div>
        <div class="row g-3" data-aos="fade-up">
            <?php foreach ($doctor_list as $row): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="t2-teacher-card">
                    <div class="t2-teacher-photo">
                        <img src="<?php echo get_image_url('staff', $row['photo']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    </div>
                    <div class="t2-teacher-info">
                        <h5><?php echo $row['name']; ?></h5>
                        <p><?php echo $row['designation_name']; ?></p>
                        <div style="display:flex; justify-content:center; gap:8px; margin-top:8px;">
                            <?php if (!empty($row['facebook_url'])): ?>
                            <a href="<?php echo $row['facebook_url']; ?>" target="_blank" rel="noopener" style="width:30px; height:30px; border-radius:50%; background: var(--bg-section); display:inline-flex; align-items:center; justify-content:center; color: var(--primary);"><i class="fab fa-facebook-f"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($row['twitter_url'])): ?>
                            <a href="<?php echo $row['twitter_url']; ?>" target="_blank" rel="noopener" style="width:30px; height:30px; border-radius:50%; background: var(--bg-section); display:inline-flex; align-items:center; justify-content:center; color: var(--primary);"><i class="fab fa-twitter"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($row['linkedin_url'])): ?>
                            <a href="<?php echo $row['linkedin_url']; ?>" target="_blank" rel="noopener" style="width:30px; height:30px; border-radius:50%; background: var(--bg-section); display:inline-flex; align-items:center; justify-content:center; color: var(--primary);"><i class="fab fa-linkedin-in"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; } ?>

<!-- =============================================================
     VIDEO GALLERY
============================================================== -->
<?php
$faq_list = [];
if (!empty($branchID) && $this->db->table_exists('front_cms_faq_list')) {
    $faq_list = $this->db->where('branch_id', $branchID)->get('front_cms_faq_list')->result_array();
}
if (!empty($faq_list)):
?>
<section class="t2-main-wrap" style="padding-top:14px;">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="t2-section-title" data-aos="fade-up"><i class="fa fa-video"></i> ভিডিও গ্যালারী</h3>
        </div>
        <div class="row g-3" data-aos="fade-up">
            <?php foreach ($faq_list as $key => $value): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="https://www.youtube.com/watch?v=<?php echo $value['description']; ?>" class="t2-gallery-box" data-glightbox data-type="video">
                    <img src="https://img.youtube.com/vi/<?php echo $value['description']; ?>/mqdefault.jpg" alt="<?php echo htmlspecialchars($value['title']); ?>">
                </a>
                <div style="text-align:center; margin-top:8px; font-size:13px; font-weight:600; color: var(--text-dark);">
                    <?php echo $value['title']; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =============================================================
     NOTICE / NEWS SECTION (cards)
============================================================== -->
<?php
$news_cards = $this->home_model->getLatestNews($branchID);
if (!empty($news_cards)):
?>
<section class="t2-main-wrap" style="padding-top:14px; padding-bottom:30px;">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="t2-section-title" data-aos="fade-up"><i class="fa fa-newspaper"></i> সর্বশেষ নোটিশ</h3>
        </div>
        <div class="row g-3" data-aos="fade-up">
            <?php foreach (array_slice($news_cards, 0, 8) as $key => $value): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="t2-card h-100">
                    <div style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); padding:34px 16px; text-align:center; position:relative;">
                        <i class="fa fa-bullhorn" style="color: var(--accent); font-size:42px;"></i>
                        <div style="color: var(--on-primary); font-weight:700; margin-top:8px;">নোটিশ</div>
                        <div style="position:absolute; left:0; right:0; bottom:0; height:3px; background: var(--accent);"></div>
                    </div>
                    <div class="t2-card-body" style="padding:14px 16px;">
                        <h6 style="font-size:14px; font-weight:600; color: var(--text-dark); line-height:1.5; min-height:42px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                            <a href="<?php echo base_url($url_alias . '/news_view/' . $value->alias); ?>" style="color: var(--text-dark);">
                                <?php echo $value->title; ?>
                            </a>
                        </h6>
                        <a href="<?php echo base_url($url_alias . '/news_view/' . $value->alias); ?>"
                           style="display:inline-block; margin-top:10px; padding:5px 14px; background: var(--bg-section); border:1px solid var(--border); border-radius: var(--radius-sm); color: var(--primary); font-size:12.5px; font-weight:600;">
                            বিস্তারিত <i class="fa fa-angle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =============================================================
     PHOTO GALLERY (latest gallery images, if available)
============================================================== -->
<?php
$gallery_list = [];
if (!empty($branchID) && $this->db->table_exists('front_cms_gallery')) {
    $gallery_list = $this->db->where('branch_id', $branchID)->order_by('id', 'desc')->limit(8)->get('front_cms_gallery')->result_array();
}
if (!empty($gallery_list)):
?>
<section class="t2-main-wrap" style="padding-top:14px; padding-bottom:30px;">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="t2-section-title" data-aos="fade-up"><i class="fa fa-images"></i> ফটো গ্যালারী</h3>
        </div>
        <div class="row g-2" data-aos="fade-up">
            <?php foreach ($gallery_list as $g):
                $g_elements = !empty($g['elements']) ? json_decode($g['elements'], true) : [];
                $g_img = !empty($g_elements['image']) ? $g_elements['image'] : (!empty($g['image']) ? $g['image'] : '');
                if (empty($g_img)) continue;
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="<?php echo base_url('uploads/frontend/gallery/' . $g_img); ?>" class="t2-gallery-box">
                    <img src="<?php echo base_url('uploads/frontend/gallery/' . $g_img); ?>" alt="<?php echo htmlspecialchars($g['title']); ?>">
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
