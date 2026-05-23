<!--================================
							Video-Page-start
                    =================================-->        
        <div class="video-page">
            <div class="container">
                <div class="row">              
                    <div class="col-lg-12 col-md-12">
                        <div class="video-p-title">
                            ভিডিও গ্যালারী
                            <div class="icon-class">
                                <img src="<?php echo base_url('assets/front/assets/images/icon-image.png'); ?>" alt="">
                            </div>
                        
                        </div>
                    </div>
                </div>

                <div class="row">
                    
			    	



                                <?php 
            $faq_list = $this->db->where('branch_id', $branchID)->get('front_cms_faq_list')->result_array();
            foreach ($faq_list as $key => $value) {
            ?>	

									<div class="elitesDesign-3 elitesDesign-m2">
                         <div class="video-page-wrpp">
                             <figure class="video-page-thumbnails">
                                                                     <img src="https://img.youtube.com/vi/<?php echo $value['description']; ?>/mqdefault.jpg" />
												                                 <a class="video-page-icon popup" href="https://www.youtube.com/watch?v=<?php echo $value['description']; ?>"> <i class="las la-play"></i> </a>
                                 <h5 class="video-caption">
                                     <a href="https://www.youtube.com/watch?v=<?php echo $value['description']; ?>"><?php echo $value['title'] ?></a>
                                 </h5>
                             </figure>
                         </div>
                     </div>
					 					
							<?php } ?>				
										
										
										
										
										
					 					  
                     
				
 
                  
                    </div>

                   <div class="col-lg-12 col-md-12"></div>
						</div>
              
            </div>