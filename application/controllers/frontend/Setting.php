<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('frontend_model');
    }

    public function index()
    {
        // check access permission
        if (!get_permission('frontend_setting', 'is_view')) {
            access_denied();
        }
        $branchID = $this->frontend_model->getBranchID();
        if ($_POST) {
            $branch_id = $this->input->post('branch_id');
            redirect(base_url('frontend/setting?branch_id=' . $branch_id));
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/dropify/css/dropify.min.css',
                'vendor/jquery-asColorPicker-master/css/asColorPicker.css',
            ),
            'js' => array(
                'vendor/dropify/js/dropify.min.js',
                'vendor/jquery-asColorPicker-master/libs/jquery-asColor.js',
                'vendor/jquery-asColorPicker-master/libs/jquery-asGradient.js',
                'vendor/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js',
            ),
        );
        $this->data['branch_id'] = $branchID;
        $this->data['setting'] = $this->frontend_model->get('front_cms_setting', array('branch_id' => $branchID), true);
        $this->data['title'] = translate('frontend');
        $this->data['sub_page'] = 'frontend/setting';
        $this->data['main_menu'] = 'frontend';
        $this->load->view('layout/index', $this->data);
    }


    public function save()
    {
        if (!get_permission('frontend_setting', 'is_add')) {
            ajax_access_denied();
        }
        if ($_POST) {
            $branchID = $this->frontend_model->getBranchID();
            $this->form_validation->set_rules('application_title', 'Cms Title', 'trim|required');
            $this->form_validation->set_rules('url_alias', 'Cms Url Alias', 'trim|required|callback_unique_url');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            $this->form_validation->set_rules('mobile_no', 'Mobile No', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('copyright_text', 'Copyright Text', 'trim|required');
            // theme options
            $this->form_validation->set_rules('primary_color', 'Primary Color', 'trim|required');
            $this->form_validation->set_rules('menu_color', 'Menu Color', 'trim|required');
            $this->form_validation->set_rules('btn_hover', 'Button Hover Color', 'trim|required');
            $this->form_validation->set_rules('footer_bg_color', 'Footer Background Color ', 'trim|required');
            $this->form_validation->set_rules('copyright_bg_color', 'Copyright BG Color', 'trim|required');
            $this->form_validation->set_rules('border_radius', 'Border Radius', 'trim|required');
            if ($this->form_validation->run() == true) {
                $cms_setting = array(
                    'branch_id' => $branchID,
                    'application_title' => $this->input->post('application_title'),
                    'url_alias' =>  strtolower(preg_replace('/[^A-Za-z0-9]/', '_', $this->input->post('url_alias'))),
                    'cms_active' => $this->input->post('cms_frontend_status'),
                    'primary_color' => $this->input->post('primary_color'),
                    'menu_color' => $this->input->post('menu_color'),
                    'hover_color' => $this->input->post('btn_hover'),
                    'text_color' => $this->input->post('text_color'),
                    'text_secondary_color' => $this->input->post('secondary_text_color'),
                    'footer_background_color' => $this->input->post('footer_bg_color'),
                    'footer_text_color' => $this->input->post('footer_text_color'),
                    'copyright_bg_color' => $this->input->post('copyright_bg_color'),
                    'copyright_text_color' => $this->input->post('copyright_text_color'),
                    'border_radius' => $this->input->post('border_radius'),

                    'online_admission' => $this->input->post('online_admission'),
                    'captcha_status' => $this->input->post('captcha_status'),
                    'recaptcha_site_key' => $this->input->post('recaptcha_site_key'),
                    'recaptcha_secret_key' => $this->input->post('recaptcha_secret_key'),
					'emergency_notice' => $this->input->post('emergency_notice'),
                    'address' => $this->input->post('address'),
                    'mobile_no' => $this->input->post('mobile_no'),
                    'email' => $this->input->post('email'),
                    'copyright_text' => $this->input->post('copyright_text'),
					'eienn_code' => $this->input->post('eienn_code'),
					'facebook_page_url' => $this->input->post('facebook_page_url'),
					'college_code' => $this->input->post('college_code'),
					'terms' => $this->input->post('terms'),
					'privacy' => $this->input->post('privacy'),
                    'google_analytics' => $this->input->post('google_analytics', false),
                );
				
				
				// Main Logo
                if (isset($_FILES["main_logo"]) && !empty($_FILES["main_logo"]['name'])) {
                    $imageNmae = $_FILES['main_logo']['name'];
                    $extension = pathinfo($imageNmae, PATHINFO_EXTENSION);
                    $newLogoName = "main_logo$branchID." . $extension;
                    $image_path = './uploads/frontend/images/' . $newLogoName;
                    if (move_uploaded_file($_FILES['main_logo']['tmp_name'], $image_path)) {
                        $cms_setting['main_logo'] = $newLogoName;
                    }
                }
				
				
				
				// Principal Images
                if (isset($_FILES["principal_images"]) && !empty($_FILES["principal_images"]['name'])) {
                    $imageNmae = $_FILES['principal_images']['name'];
                    $extension = pathinfo($imageNmae, PATHINFO_EXTENSION);
                    $newLogoName = "principal_images$branchID." . $extension;
                    $image_path = './uploads/frontend/images/' . $newLogoName;
                    if (move_uploaded_file($_FILES['principal_images']['tmp_name'], $image_path)) {
                        $cms_setting['principal_images'] = $newLogoName;
                    }
                }
				
			// Sovapoti Images
                if (isset($_FILES["sovapoti_images"]) && !empty($_FILES["sovapoti_images"]['name'])) {
                    $imageNmae = $_FILES['sovapoti_images']['name'];
                    $extension = pathinfo($imageNmae, PATHINFO_EXTENSION);
                    $newLogoName = "sovapoti_images$branchID." . $extension;
                    $image_path = './uploads/frontend/images/' . $newLogoName;
                    if (move_uploaded_file($_FILES['sovapoti_images']['tmp_name'], $image_path)) {
                        $cms_setting['sovapoti_images'] = $newLogoName;
                    }
                }
				
				
				
				
				
				
				
				
				// Mobile Logo
                if (isset($_FILES["mobile_logo"]) && !empty($_FILES["mobile_logo"]['name'])) {
                    $imageNmae = $_FILES['mobile_logo']['name'];
                    $extension = pathinfo($imageNmae, PATHINFO_EXTENSION);
                    $newLogoName = "mobile_logo$branchID." . $extension;
                    $image_path = './uploads/frontend/images/' . $newLogoName;
                    if (move_uploaded_file($_FILES['mobile_logo']['tmp_name'], $image_path)) {
                        $cms_setting['mobile_logo'] = $newLogoName;
                    }
                }
				

                // upload fav icon
                if (isset($_FILES["fav_icon"]) && !empty($_FILES["fav_icon"]['name'])) {
                    $imageNmae = $_FILES['fav_icon']['name'];
                    $extension = pathinfo($imageNmae, PATHINFO_EXTENSION);
                    $newLogoName = "fav_icon$branchID." . $extension;
                    $image_path = './uploads/frontend/images/' . $newLogoName;
                    if (move_uploaded_file($_FILES['fav_icon']['tmp_name'], $image_path)) {
                        $cms_setting['fav_icon'] = $newLogoName;
                    }
                }

                // update all information in the database
                $this->db->where(array('branch_id' => $branchID));
                $get = $this->db->get('front_cms_setting');
                if ($get->num_rows() > 0) {
                    $this->db->where('id', $get->row()->id);
                    $this->db->update('front_cms_setting', $cms_setting);
                } else {
                    $this->db->insert('front_cms_setting', $cms_setting);
                }

                set_alert('success', translate('information_has_been_saved_successfully'));
                $array = array('status' => 'success');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
        }
    }


    public function unique_url($alias)
    {
        $branchID = $this->frontend_model->getBranchID();
        $this->db->where_not_in('branch_id', $branchID);
        $this->db->where('url_alias', $alias);
        $query = $this->db->get('front_cms_setting');
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message("unique_url", translate('already_taken'));
            return false;
        } else {
            return true;
        }
    }
}
