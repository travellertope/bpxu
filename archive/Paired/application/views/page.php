<section class="bg-lights p-6 border-1">
    <div class="container">
        <div class="rows d-flex justify-content-center hide-xs">
            <h2 class="pt-2"><?php echo html_escape($page->title) ?></h2>
        </div>
    </div>
</section>

<section class="section bg-grey pt-6">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content-container minh-400">
                    <div class="faq">
                        <div class="accordion-container text-dark">
                            <p><?= $page->details ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>