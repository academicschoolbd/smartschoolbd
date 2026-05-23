 <!--================================
                        Archive-Page-Start
                ===============================-->

            <div class="archive-page">
			
				<div class="row">              
                        <div class="col-lg-12 col-md-12">
                            <div class="video-p-title">
                              ফটো গ্যালারী
                                <div class="icon-class">
                                    <img src="<?php echo base_url('assets/front/assets/images/icon-image.png'); ?>" alt="">
                                </div>
                            
                            </div>
                        </div>
                    </div>
                    

                    
					
                <div class="container">
            <div class="row">
                				 	

				
				
				
        <?php 
        $school = $this->uri->segment(1);
        foreach ($galleryList as $row) { ?>
				<div class="elitesDesign-3 elitesDesign-m2">
                    <div class="photo-gallery-wrpp">
                        <div class="photo-image">
                                                        
                            <a href="<?php echo $this->gallery_model->get_image_url($row['thumb_image']); ?>">
                            <img class="lazyload" src="<?php echo $this->gallery_model->get_image_url($row['thumb_image']); ?>" data-src="<?php echo $this->gallery_model->get_image_url($row['thumb_image']); ?>">	
                            </a> 
                            	                        </div>
                        <div class="photo-title">
                            <a href="<?php echo $this->gallery_model->get_image_url($row['thumb_image']); ?>" style="color:white"><?php echo $row['title'] ?></a>
                        </div>
                    </div>
                </div>                             
                	   <?php } ?>			
                								
				    
                
				  
  
                
            </div>
			
       </div>
						
					
                    



                </div>
      
                <!--================================
                        Archive-Page-End
                ===============================-->