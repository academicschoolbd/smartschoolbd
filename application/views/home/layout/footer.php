<?php
$application_title  = !empty($cms_setting['application_title']) ? $cms_setting['application_title'] : '';
$school_name_en     = !empty($cms_setting['application_title_en']) ? $cms_setting['application_title_en'] : '';
$established_year   = !empty($cms_setting['establish_year']) ? $cms_setting['establish_year']
                       : (!empty($cms_setting['founded_year']) ? $cms_setting['founded_year'] : '');
$footer_short_about = !empty($cms_setting['footer_about']) ? strip_tags($cms_setting['footer_about'])
                       : (!empty($cms_setting['short_description']) ? strip_tags($cms_setting['short_description']) : '');
$url_alias_f        = !empty($cms_setting['url_alias']) ? $cms_setting['url_alias'] : 'frontend';
?>
<!-- =============================================================
     FOOTER (4-column reference-site layout)
============================================================== -->
<footer class="t2-footer">
    <div class="container">
        <div class="row g-4 pb-4">

            <!-- Column 1: Brand + about + socials -->
            <div class="col-lg-4 col-md-6">
                <div class="t2-footer-brand">
                    <?php if (!empty($cms_setting['main_logo'])): ?>
                    <img src="<?php echo base_url('uploads/frontend/images/' . $cms_setting['main_logo']); ?>"
                         alt="<?php echo htmlspecialchars($application_title); ?>">
                    <?php endif; ?>
                    <div class="brand-name">
                        <?php echo $application_title; ?>
                        <?php if (!empty($school_name_en)): ?>
                        <small><?php echo htmlspecialchars($school_name_en); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($footer_short_about)): ?>
                <p style="text-align: justify;"><?php echo $footer_short_about; ?></p>
                <?php endif; ?>

                <?php if (!empty($established_year)): ?>
                <div class="t2-founded-box">
                    <div class="yr"><?php echo $established_year; ?></div>
                    <div class="label">প্রতিষ্ঠাকাল<br><span style="font-size:11px; opacity:.7;">Established</span></div>
                </div>
                <?php endif; ?>

                <div class="t2-social-row">
                    <?php if (!empty($cms_setting['facebook_page_url'])): ?>
                    <a href="https://www.facebook.com/<?php echo $cms_setting['facebook_page_url']; ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($cms_setting['youtube_url'])): ?>
                    <a href="<?php echo $cms_setting['youtube_url']; ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($cms_setting['twitter_url'])): ?>
                    <a href="<?php echo $cms_setting['twitter_url']; ?>" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($cms_setting['instagram_url'])): ?>
                    <a href="<?php echo $cms_setting['instagram_url']; ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($cms_setting['linkedin_url'])): ?>
                    <a href="<?php echo $cms_setting['linkedin_url']; ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h5><i class="fa fa-link"></i> দ্রুত লিংক</h5>
                <ul>
                    <li><a href="<?php echo base_url($url_alias_f . '/about'); ?>">প্রতিষ্ঠানের ইতিহাস</a></li>
                    <li><a href="<?php echo base_url($url_alias_f . '/sovapoti'); ?>">সভাপতির বানী</a></li>
                    <li><a href="<?php echo base_url($url_alias_f . '/principal'); ?>">প্রধান শিক্ষকের বানী</a></li>
                    <li><a href="<?php echo base_url($url_alias_f . '/contact'); ?>">যোগাযোগ</a></li>
                </ul>
            </div>

            <!-- Column 3: Important Pages -->
            <div class="col-lg-3 col-md-6">
                <h5><i class="fa fa-star"></i> গুরুত্বপূর্ণ পেজ</h5>
                <ul>
                    <li><a href="<?php echo base_url($url_alias_f . '/exam_results'); ?>">পরীক্ষার ফলাফল</a></li>
                    <li><a href="<?php echo base_url($url_alias_f . '/news'); ?>">নোটিশ</a></li>
                    <li><a href="<?php echo base_url($url_alias_f . '/gallery'); ?>">ফটো গ্যালারী</a></li>
                    <li><a href="<?php echo base_url($url_alias_f . '/video'); ?>">ভিডিও গ্যালারী</a></li>
                    <li><a href="<?php echo base_url($url_alias_f . '/teachers'); ?>">শিক্ষকমণ্ডলী</a></li>
                </ul>
            </div>

            <!-- Column 4: Contact -->
            <div class="col-lg-3 col-md-6">
                <h5><i class="fa fa-map-marker-alt"></i> যোগাযোগ</h5>
                <div class="t2-footer-info">
                    <?php if (!empty($cms_setting['address'])): ?>
                    <p><i class="fa fa-map-marker-alt"></i> <span><?php echo strip_tags($cms_setting['address']); ?></span></p>
                    <?php endif; ?>
                    <?php if (!empty($cms_setting['mobile_no'])): ?>
                    <p><i class="fa fa-phone"></i> <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $cms_setting['mobile_no']); ?>"><?php echo $cms_setting['mobile_no']; ?></a></p>
                    <?php endif; ?>
                    <?php if (!empty($cms_setting['email'])): ?>
                    <p><i class="fa fa-envelope"></i> <a href="mailto:<?php echo $cms_setting['email']; ?>"><?php echo $cms_setting['email']; ?></a></p>
                    <?php endif; ?>
                    <?php if (!empty($cms_setting['eienn_code'])): ?>
                    <p><i class="fa fa-id-badge"></i> <span>EIIN: <?php echo $cms_setting['eienn_code']; ?></span></p>
                    <?php endif; ?>
                    <?php if (!empty($cms_setting['college_code'])): ?>
                    <p><i class="fa fa-hashtag"></i> <span>Code: <?php echo $cms_setting['college_code']; ?></span></p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Bottom copyright bar -->
    <div class="t2-footer-bottom">
        <div class="container">
            <p>
                <?php
                if (!empty($cms_setting['copyright_text'])) {
                    echo $cms_setting['copyright_text'];
                } else {
                    echo '&copy; ' . date('Y') . ' ' . htmlspecialchars($application_title) . '. All rights reserved.';
                }
                ?>
            </p>
        </div>
    </div>
</footer>

<!-- Legacy JS bundle (CSRF / sweetalert / etc.) -->
<script src="<?php echo base_url('assets/vendor/sweetalert/sweetalert.min.js'); ?>"></script>

<?php
$alertclass = "";
if ($this->session->flashdata('alert-message-success')) {
    $alertclass = "success";
} else if ($this->session->flashdata('alert-message-error')) {
    $alertclass = "error";
} else if ($this->session->flashdata('alert-message-info')) {
    $alertclass = "info";
}
if ($alertclass != ''):
    $alert_message = $this->session->flashdata('alert-message-' . $alertclass);
?>
<script type="text/javascript">
    if (typeof swal !== 'undefined') {
        swal({
            toast: true,
            position: 'top-end',
            type: '<?php echo $alertclass; ?>',
            title: '<?php echo addslashes($alert_message); ?>',
            confirmButtonClass: 'btn btn-1',
            buttonsStyling: false,
            timer: 6000
        });
    }
</script>
<?php endif; ?>
