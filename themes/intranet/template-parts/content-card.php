<div id="card" class="flex flex-col border border slate-500 shadow-md rounded-xl">
    <div id="card-thumbnail" class="hover:shadow-md rounded-t-xl">
        <div class="overflow-hidden">
            <a class="" href="<?php the_permalink();?>">
            <?php the_post_thumbnail('', array('class' => 'aspect-video object-cover hover:scale-110 duration-700')); ?>
            </a>
        </div>
    </div>
    <div id="card-content" class="flex flex-col gap-1 h-full mx-2">

        <div id="card-post-title" class="min-h-24">
            <a class="hover:underline" href="<?php the_permalink();?>">
		    <?php the_title('<h1 class="text-2xl text-balance line-clamp-3">','</h1>') ?></div>
        </a>

        <?php if ( get_main_site_id() === get_current_blog_id() ) : ?>
        <div id="card-post-site" class="">
            <a class="hover:underline" href="<?php bloginfo("url"); ?>">
			    <?php bloginfo("name"); ?>
            </a>
        </div>
        <?php endif; ?>

        <div id="card-post-content" class="grow">
            <div class="line-clamp-6"><?php the_excerpt(); ?></div>
        </div>
        <div id="card-post-footer" class="my-2">
            <span class="mr-1 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                <?php the_category('</span><span class="mr-1 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">'); ?>
            </span>
            <?php if ( has_tag()) : ?>
            <span class="mr-1 inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-red-500/10">
                <?php the_tags('','</span><span class="mr-1 inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-red-500/10">',''); ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
</div>