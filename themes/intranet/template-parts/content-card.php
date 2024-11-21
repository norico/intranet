<div class="card border border slate-500">
    <div class="card-thumbnail">
        <?php the_post_thumbnail('', array('class' => 'aspect-video object-cover')); ?>
    </div>
    <div class="m-2">
        <div class="card-title"><?php the_title('<h1 class="text-2xl">','</h1>') ?></div>
        <div class="card-site">From: <?php bloginfo("name"); ?></div>
        <div class="card-content"><?php the_excerpt(); ?></div>
    </div>

</div>