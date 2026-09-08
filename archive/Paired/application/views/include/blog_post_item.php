<div class="col-md-6 col-lg-4 mb-4 mb-md-5 mb-lg-0 mt-6 lift-xs">
    <article class="card shadow-none h-100 border-0" data-aos="fade-up" data-aos-delay="<?= $b * 100; ?>"> 
        <a href="<?php echo base_url('post/'.$post->slug) ?>">
            <div class="blog-img round-1" style="background-image: url(<?php echo base_url($post->image) ?>);"></div>
        </a>
        <div class="card-body p-0">
            <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
                <p class="text-muted mb-0"><span class="text-muted"><?php echo my_date_show($post->created_at) ?></span></p>
            </div>
            <h3 class="h5 mb-4">
                <a class="text-dark" href="<?php echo base_url('post/'.$post->slug) ?>"><h5><?php echo html_escape($post->title) ?></h5></a>
            </h3>

            <a class="text-muted link-hover" class="mt-5" href="<?php echo base_url('post/'.$post->slug) ?>"> <?php echo trans('read-more') ?> <i class="pl-1 pt-1 bi bi-arrow-right"></i></a>
        </div>
    </article>
</div>