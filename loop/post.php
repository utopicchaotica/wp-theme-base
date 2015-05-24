<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h1 class="page-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
<?php if (has_post_thumbnail() && is_single()) : ?>
			<div class="post-thumb"><?php the_post_thumbnail(); ?></div>
<?php endif; ?>
			<div class="post-content">
<?php if (is_single()) : ?>
				<div class="meta">
					<span class="author"><div class="dashicons-admin-users dashicons"></div>&nbsp;<?php the_author(); ?></span>
					<div class="dashicons dashicons-calendar"></div>&nbsp;<time class="time" datetime="<?php the_time('Y-m-d')?>"><?php the_time('F jS, Y'); ?></time>
					<div class="dashicons dashicons-tag"></div>&nbsp;<span class="category"><?php the_category(', '); ?></span>
				</div>
<?php endif; ?>
	<?php the_content(''); ?>
				<div class="clear"></div>
			</div>
		</article>
<?php endwhile; ?>
<?php else: ?>
			<h1>Nothing Found</h1>
			<p>Sorry, but you are looking for something that isn't here.</p>

			<?php get_search_form(); ?>

			<p><a href="<?php get_option('home'); ?>">Return to the homepage</a></p>
<?php endif; ?>