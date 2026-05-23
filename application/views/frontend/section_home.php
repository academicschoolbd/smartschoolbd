<?php if (is_superadmin_loggedin() ): ?>
	<?php $this->load->view('frontend/branch_select'); ?>
<?php endif; if (!empty($branch_id)): 

$well_ele = json_decode($wellcome['elements'], true);
if (empty($well_ele)) {
	$well_ele = array('image' => '');
}
$doc_ele = json_decode($teachers['elements'], true);
if (empty($doc_ele)) {
	$doc_ele = array(
		'image' => '',
		'teacher_start' => ''
	);
}
$sta_ele = json_decode($statistics['elements'], true);
if (empty($sta_ele)) {
	$sta_ele = array('image' => '');
}

$elements = json_decode($cta['elements'], true);
if (empty($elements)) {
	$elements = array(
		'mobile_no' => '',
		'button_text' => '',
		'button_url' => '',
	);
}
?>
<div class="row">
	<div class="col-md-3 mb-md">
		<?php include 'sidebar.php'; ?>
	</div>
	<div class="col-md-9">
		<section class="panel">
			<div class="tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#welcome" data-toggle="tab"><?php echo translate('Institute History') . ' ' . translate('Short'); ?></a>
					</li>
					<li>
						<a href="#teachers" data-toggle="tab"><?php echo translate('Principal & Sovapoti'); ?></a>
					</li>

					<li>
						<a href="#options" data-toggle="tab"><?php echo translate('SEO'); ?></a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="welcome">
						<?php echo form_open_multipart('frontend/section/home_wellcome' . get_request_url(), array('class' => 'form-horizontal frm-submit-data')); ?>
							<div class="form-group">
								<label class="col-md-3 control-label"><?php echo translate('Instititute History Tittle'); ?> <span class="required">*</span></label>
								<div class="col-md-7">
									<input type="text" class="form-control" name="wel_title" value="<?php echo set_value('wel_title', $wellcome['title']); ?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label  class="col-md-3 control-label"><?php echo translate('Instititute Short'); ?> <span class="required">*</span></label>
								<div class="col-md-7">
									<textarea class="form-control" name="description" rows="5"><?php echo set_value('description', $wellcome['description']); ?></textarea>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?php echo translate('Institute Photo'); ?> <span class="required">*</span></label>
								<div class="col-md-4">
									<input type="hidden" name="old_photo" value="<?php echo $well_ele['image'] ?>">
									<input type="file" name="photo" class="dropify" data-height="150" data-default-file="<?php echo base_url('uploads/frontend/home_page/' . $well_ele['image']); ?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Show Website</label>
								<div class="col-md-4">
								<div class="material-switch mt-xs">
									<input id="isvisiblewell" name="isvisible" type="checkbox" <?php echo $wellcome['active'] == 1 ? 'checked' : ''; ?>  />
									<label for="isvisiblewell" class="label-primary"></label>
								</div>
								</div>
							</div>
							<footer class="panel-footer mt-lg">
								<div class="row">
									<div class="col-md-2 col-md-offset-3">
										<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
											<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
										</button>
									</div>
								</div>
							</footer>
						<?php echo form_close(); ?>
					</div>
					<div class="tab-pane" id="teachers">
						<?php echo form_open_multipart('frontend/section/home_teachers' . get_request_url(), array('class' => 'form-horizontal frm-submit-data')); ?>

							
							
							
							
							
							
							
							
							
							<div class="form-group">
								<label  class="col-md-3 control-label"><?php echo translate('Principal Short Message'); ?> <span class="required">*</span></label>
								<div class="col-md-7">
									<textarea class="form-control" name="tea_title" rows="5"><?php echo set_value('tea_title', $teachers['title']); ?></textarea>
									<span class="error"></span>
								</div>
							</div>
							
														<div class="form-group">
								<label  class="col-md-3 control-label"><?php echo translate('Principal Long Message'); ?> <span class="required">*</span></label>
								<div class="col-md-7">
									<textarea class="form-control" name="principal_long_message" rows="5"><?php echo set_value('principal_long_message', $teachers['principal_long_message']); ?></textarea>
									<span class="error"></span>
								</div>
							</div>
														
							
							
							
							
							
							
							
							
							
							
							
							<div class="form-group">
								<label  class="col-md-3 control-label"><?php echo translate('Sovapoti Short Message'); ?> <span class="required">*</span></label>
								<div class="col-md-7">
									<textarea class="form-control" name="sovapoti_short_message" rows="5"><?php echo set_value('sovapoti_short_message', $teachers['sovapoti_short_message']); ?></textarea>
									<span class="error"></span>
								</div>
							</div>
							
														<div class="form-group">
								<label  class="col-md-3 control-label"><?php echo translate('Sovapoti Long Message'); ?> <span class="required">*</span></label>
								<div class="col-md-7">
									<textarea class="form-control" name="tea_description" rows="5"><?php echo set_value('tea_description', $teachers['description']); ?></textarea>
									<span class="error"></span>
								</div>
							</div>
							
							
							
							
							
							
							
							
							
							
							

							<div class="form-group">
								<label class="col-md-3 control-label">Show Website</label>
								<div class="col-md-4">
								<div class="material-switch mt-xs">
									<input id="isvisibletea" name="isvisible" type="checkbox" <?php echo $teachers['active'] == 1 ? 'checked' : ''; ?>  />
									<label for="isvisibletea" class="label-primary"></label>
								</div>
								</div>
							</div>
							<footer class="panel-footer mt-lg">
								<div class="row">
									<div class="col-md-2 col-md-offset-3">
										<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
											<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
										</button>
									</div>
								</div>
							</footer>
						<?php echo form_close(); ?>
					</div>

					
					
					
					
					
					
					
					
					
					
					
					
					
					

					<div class="tab-pane" id="options">
						<?php echo form_open('frontend/section/home_options' . get_request_url(), array('class' => 'form-horizontal frm-submit')); ?>
							<div class="form-group">
								<label class="col-md-3 control-label"><?php echo translate('page') . " " .  translate('Meta_tittle'); ?> <span class="required">*</span></label>
								<div class="col-md-8">
									<input type="text" class="form-control" name="page_title" value="<?php echo set_value('page_title', $home_seo['page_title']); ?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?php echo translate('meta') . " " . translate('keyword'); ?></label>
								<div class="col-md-8">
									<input type="text" class="form-control" name="meta_keyword" value="<?php echo set_value('meta_keyword', $home_seo['meta_keyword']); ?>" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?php echo translate('meta') . " " . translate('description'); ?></label>
								<div class="col-md-8">
									<input type="text" class="form-control" name="meta_description" value="<?php echo set_value('meta_description', $home_seo['meta_description']); ?>" />
								</div>
							</div>
							<footer class="panel-footer mt-lg">
								<div class="row">
									<div class="col-md-2 col-md-offset-3">
										<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
											<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
										</button>
									</div>
								</div>
							</footer>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>
<?php endif; ?>

<script type="text/javascript">
    $(".complex-colorpicker").asColorPicker({
		readonly: false,
		lang: 'en',
		mode: 'complex',
		color: {
			reduceAlpha: true,
			zeroAlphaAsTransparent: false
		},
    });
</script>