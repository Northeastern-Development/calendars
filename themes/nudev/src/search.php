<?php
if(isset($_GET['post_type'])) {
    $type = $_GET['post_type'];
    if($type == 'classnotes') {
        load_template(TEMPLATEPATH . '/classnotes-search.php');
    // } elseif($type == 'books') {
		}else{
        load_template(TEMPLATEPATH . '/search-results.php');
    }
}

if($type != "classnotes"){
?>




<?php get_header(); ?>

	<main id="category" role="main" aria-label="Content">
		<!-- section -->
		<section>

			<p><?php echo sprintf( __( '%s Search Results for: \'', 'nudev' ), $wp_query->found_posts ); echo get_search_query(); ?>'</p>

			Perform Another Search: <?php get_search_form(); ?>

			<div class="articles">
				<?php get_template_part('loops/loop-search'); ?>
				<?php get_template_part('pagination'); ?>
			</div>

		</section>
		<!-- /section -->
	</main>

<?php get_footer(); } ?>
