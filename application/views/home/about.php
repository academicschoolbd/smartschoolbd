<?php $service = $this->db->get_where('front_cms_services', array('branch_id' => $branchID))->row_array(); ?>
<!--================================
							Create Page-css start
                    =================================-->        
        <div class="create-page">
            <div class="container">
                <div class="col-lg-12 col-md-12">
                   <div class="page-title">
                        <?php echo $page_data['title']; ?>                     <div class="icon-image">
                           <img src="<?php echo base_url('assets/front/assets/images/icon-image.png'); ?>" alt="">
                       </div> 
                    </div>
                    
                     
                    <div class="page-content">
									
                                            <img src="<?php echo base_url('uploads/frontend/about/' . $page_data['about_image']); ?>" alt="" style="float: left; width:40%; margin-right:20px"> 
								
                        <p><?php echo $page_data['content']; ?></p>
                    </div>
                    
                </div>
            </div>
            
        </div>
        
					<!--================================
							Create-Page-End
                    =================================-->