<!--================================
                    Archive-Page-Start
            ===============================-->

            <div class="archive-page">
                <div class="container">
				
				 <div class="row">              
                    <div class="col-lg-12 col-md-12">
                        <div class="video-p-title">
                             শিক্ষক মণ্ডলী 
                            <div class="icon-class">
                                <img src="<?php echo base_url('assets/front/assets/images/icon-image.png'); ?>" alt="">
                            </div>
                        
                        </div>
                    </div>
                </div>
				
				
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                   <th scope="col">Teacher Name</th>
								   <th scope="col">Mobile Number</th>
                                  <th scope="col">Designation </th>
                                   <th scope="col">Image</th>
                                 </tr>
                                </thead>
                                <tbody>



<?php foreach ($doctor_list as $row) { ?>
								 <tr>
                                   <td  class="wrpper" scope="row" width="25%"><?php echo $row['name']; ?></td>
								   <td  class="wrpper" scope="row" width="25%"><?php echo $row['mobileno']; ?></td>
         
                                     <td class="wrpper" width="10%"><?php echo $row['designation_name']; ?></td>
                                     <td class="wrpper" width="15%">
									                                          <div class="image">
                                            <img src="<?php echo get_image_url('staff', $row['photo']); ?>">
                                         </div>
									                                     
						               </td>
                                 </tr>
								 
								  <?php } ?>
								 




                                </tbody>
                            </table>
                    </div>

                    
      


                </div>
            </div>