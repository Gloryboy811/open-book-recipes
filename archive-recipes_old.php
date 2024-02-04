<?php
/* Template Name: Archive Page Custom */
get_header(); ?>
<link href="<?php echo recipebook_url; ?>/styles/style-archive.css?<?php echo (rand()); ?>" rel="stylesheet" type="text/css" />
<header class="archive-header has-text-align-center header-footer-group">

			<div class="archive-header-inner section-inner medium">

									<h1 class="archive-title"><span class="color-accent">Recipes</span></h1>
				
				
			</div><!-- .archive-header-inner -->

		</header>
<div class="container">

  
      <main id="main" class="rb-recipe-list" role="main">
        
        <?php while ( have_posts() ) : the_post(); // standard WordPress loop. ?>
   
          <?php include( 'templates/recipes-list-item.php'); // loading our custom file. ?>

        <?php endwhile; // end of the loop. ?>

      </main><!-- #main -->
  

</div><!-- .container -->

<?php get_footer(); ?>