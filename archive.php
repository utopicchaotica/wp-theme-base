<?php get_header(); ?>
				<div id="blogPosts">
<?php if (have_posts()) : ?>
							<h1 class="title">if (is_category()) :
	printf(__('Category Archives: %s', 'text-domain'), single_cat_title('', false));
elseif (is_tag()) :
	printf(__('Tag Archives: %s', 'text-domain'), single_tag_title('',false));
elseif (is_author()) :
	_e('All posts by author', 'text-domain');
elseif (is_day()) :
	printf(__('Daily Archives: %s', 'text-domain'), get_the_date());
elseif (is_month()) :
	printf(__('Monthly Archives: %s', 'text-domain'), get_the_date(_x('F Y', 'monthly archives date format', 'text-domain')));
elseif (is_year()) :
	printf(__('Yearly Archives: %s', 'text-domain'), get_the_date(_x('Y', 'yearly archives date format', 'text-domain')));
elseif (is_search()) :
	printf(__('Search Results: %s','text-domain'),the_search_query());
elseif (is_blog()) :
	_e('Blog','text-domain');
else :
	_e( 'Archives', 'text-domain' );
endif;
							</h1>
<?php the_post(); ?>
<?php endif; ?>
				</div><!-- end of blogPosts //-->
<?php get_sidebar(); ?>

<?php get_footer(); ?>