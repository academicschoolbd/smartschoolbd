<div class="archive-page">
			
				<div class="row">              
                        <div class="col-lg-12 col-md-12">
                            <div class="video-p-title">
                               নোটিশ 
                                <div class="icon-class">
                                    <img src="<?php echo base_url('assets/front/assets/images/icon-image.png'); ?>" alt="">
                                </div>
                            
                            </div>
                        </div>
                    </div>
					
                <div class="container">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                      <tr>
                                   <th scope="col"> Date </th>
                                  <th scope="col"> Title </th>
                                  <th scope="col"> View </th>
								  <th scope="col"> Download </th>
                                  
                                 </tr>
                                </thead>
                                <tbody>
						<?php
        $url_alias = $cms_setting['url_alias'];
        if (!empty($results)) {
            foreach ($results as $key => $value) {
                ?>
                                    
                                 <tr>
                                   <th class="wrpper" scope="row"><?php echo _d($value['created_at']); ?></th>
         
                                    <td class="wrpper"> <?php echo $value['title'] ?>  </td>
                                     <td class="wrpper archive-btn view-btn">
									                                         <a href="<?=base_url("$url_alias/news_view/". $value['alias'])?>"> View <span> <i class="las la-eye"></i></span> 
										                                    </a></td>


<td class="wrpper archive-btn view-btn">
									                                         <a href="<?=base_url('uploads/frontend/news/' . $value['image'] )?>"> Download <span> <i class="las la-download"></i></span> 
										                                    </a></td>


                                 </tr>
                                 
                                 
                                   
								 <?php } ?>
                
                                </tbody>
                            </table>
                    </div>

                    
                
						</div>
						
						
											 
    <div class="pagination-bx mt-2">
        <?php
            if (isset($links)) {
                echo $links;
            }
        ?>
    </div>
 <?php } else { ?>
    <div class="col-md-12">
        <div class="alert alert-info">No news found.</div>
    </div>
 <?php } ?>


                </div>