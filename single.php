<?php get_header(); ?>
<?php get_sidebar(); ?>
		<div id="blogPosts">
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
			<div class="post">
				<h1 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
<?php if (has_post_thumbnail()) : ?>
				<div class="post-thumb">
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
				</div>
<?php endif; ?>
<?php the_content(''); ?>
<?php comments_template(); ?>
<?php endwhile; ?>
<?php else: ?>
			<h1>Nothing Found</h1>
			<p>Sorry, but you are looking for something that isn't here.</p>
			<p><a href="<?php get_option('home'); ?>">Return to the homepage</a></p>
<?php endif; ?>
		</div>
<?php get_footer(); ?>