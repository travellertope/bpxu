<section class="pt-8 bg-grey">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="mb-3">
                    <a href="<?php echo base_url('mentor/'. $mentor->slug) ?>"><i class="bi bi-arrow-left"></i> <?php echo trans('back') ?></a>
                </div>
                <div class="details-img" style="background-image: url(<?php echo base_url($post->image) ?>);"></div>
                <p class="mt-8 fs-20 text-dark">
                    <span><?php echo my_date_show($post->created_at) ?></span>
                </p>
                <h1 class="mb-5 mt-3"><?php echo html_escape($post->title) ?></h1>
                <p><?php echo $post->details ?></p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <article>
                    <?php 

                        $tags = explode(',', $post->tags);

                     ?>

					<?php if (!empty($tags)): ?>
						<div class="my-6 my-md-11">
							<div class="h5"><?php echo trans('tags') ?>:</div>
							<?php foreach ($tags as $tag): ?>
								<a href="#" class="badge badge-light">#<?php echo html_escape($tag) ?></a>
							<?php endforeach ?>
						</div>
					<?php endif ?>
                </article>
            </div>
        </div>
    </div>
</section>




