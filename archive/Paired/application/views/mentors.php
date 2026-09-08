<section class="pt-6 bg-grey">
    <div class="container">

        <div class="text-left mx-md-auto mb-5 mb-md-5 mb-lg-6">
            <h4 class="custom-font font-weight-light"><?php echo trans('mentors') ?></h4>
        </div>

        <div class="row">
            <div class="col-md-12 mb-4">
                <form method="GET" enctype="multipart/form-data" class="mentor_search_form" action="<?php echo base_url('home/mentor_search') ?>" role="form">
                    <div class="row">
                        <div class="col-md-2 mb-xs-2">
                            <div class="form-group has-search">
                                <span class="bi bi-search form-control-feedback"></span>
                                <input type="text" class="form-control search_name" placeholder="Search" name="mentor_search_name">
                            </div>
                        </div>

                        <div class="col-md-2 mb-xs-2">
                            <div class="form-group">
                                <select name="category" class="form-control sort_front custom-select search_category">
                                    <option value=""> Industry</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option <?php if(isset($_GET['category']) && $_GET['category'] == $category->id){echo "selected";} ?> value="<?php echo html_escape($category->id)?>"> <?php echo html_escape($category->name)?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!--comment out the skills field in mentors page search form
                        <div class="col-md-2 mb-xs-2">
                            <div class="form-group">

                                 <select name="mentor_search_skill" class="form-control sort_front custom-select" id="search_skills">
                                 <option value=""><?php echo trans('all') ?></option>                
                                </select>
                            </div>
                        </div>
                        

                        <div class="col-md-2 mb-xs-2">
                            <div class="form-group">
                                <select name="mentor_search_experience" class="form-control sort_front custom-select">
                                    <option value=""><?php echo trans('experience') ?></option>
                                    <?php for ($i=1 ; $i <31; $i++ ): ?>
                                        <option <?php if(isset($_POST['mentor_search_experience']) && $_POST['mentor_search_experience'] == $i){echo "selected";} ?> value="<?php echo html_escape($i); ?>"><?php echo html_escape($i); ?> <?php echo trans('year') ?></option>
                                        
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        -->

                        <div class="col-md-2 mb-xs-2">
                            <div class="form-group">
                                <select name="mentor_search_country" class="form-control sort_front custom-select">
                                    <option value=""><?php echo trans('countries') ?></option>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo html_escape($country->id) ?>" <?php if(isset($_POST['mentor_search_country']) && $_POST['mentor_search_country'] == $country->id){echo 'selected';} ?>><?php echo html_escape($country->name) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2 mb-xs-2">
                            <button type="submit" class="btn btn-outline-primary btn-block"><i class="bi bi-search"></i> <?php echo trans('search') ?></button>
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                        </div>
                
                    </div>
                </form>
            </div>
        </div>

        <?php if(!empty($mentors)): ?>
            <div class="row mentor_area">
                <?php include APPPATH.'views/include/mentor_item.php'; ?> 
            </div>
        <?php else: ?>
            <div class="col-12 text-center py-14 fs-16"><?php echo trans('no-data-found') ?></div>
        <?php endif; ?>

        <div class="col-md-12 text-center mt-4">
            <?php echo $this->pagination->create_links(); ?>
        </div>

    </div>
</section>