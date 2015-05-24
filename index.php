<?php
#Add this to the index.php file
if (function_exists('get_header'))
	get_header();
else
	exit;
?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>