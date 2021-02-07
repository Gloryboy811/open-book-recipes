<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>
<link href="<?php echo recipebook_url; ?>/styles/style-magazine.css?<?php echo (rand()); ?>" rel="stylesheet" type="text/css" />
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
        
			<?php
			while ( have_posts() ) :
                the_post();
                
                the_content();
                
                ?>


                <div class="rb-mag-wrapper">
                    <div class="rb-mag-text">
                        <h1 class="rb-recipe-title"><?php the_title(); ?></h1>
                        <?php
                            $timesJson = get_post_meta( $post->ID, '_rb_recipe_times', true );
                            $times = json_decode( $timesJson.'' );

                            $detailsJson = get_post_meta( $post->ID, '_rb_recipe_details', true );
                            $details = json_decode( $detailsJson.'' );

                            
                            ?>
                            <ul class="rb-mag-bold-list" id="rb-mag-meta">
                            <?php if($details->serves) { ?>
                                <li>
                                    Serves <?php echo $details->serves; ?>
                                </li>
                            <?php } ?>
                            <?php if ($times) {  ?>
                                <li>
                                    Preperation Time:
                                    <?php 
                                        if ($times->prepHrs) 
                                            echo $times->prepHrs.' <span>hours</span>';
                                        
                                        if ($times->prepMins) 
                                            echo ($times->prepHrs ? ', ' : '').$times->prepMins.' <span>minutes</span>';
                                    ?>           
                                </li>
                                <li>
                                    Cooking Time:
                                    <?php 
                                        if ($times->cookHrs) 
                                            echo $times->cookHrs.' <span>hours</span>';
                                        
                                        if ($times->cookMins) 
                                            echo ($times->cookHrs ? ', ' : '').$times->cookMins.' <span>minutes</span>';
                                    ?>           
                                </li>
                                <li>
                                    Total Time:
                                    <?php 
                                        if ($times->totalHrs) 
                                            echo $times->totalHrs.' <span>hours</span>';
                                        
                                        if ($times->totalMins) 
                                            echo ($times->totalHrs ? ', ' : '').$times->totalMins.' <span>minutes</span>';                                   
                                    ?>           
                                </li>
                            <?php } ?>
                                <?php if($details->difficulty) { ?>
                                    <li>
                                        <span class="rb-mag-hi"><?php echo $details->difficulty; ?></span>
                                    </li>     
                                <?php } ?>
                            </ul>
                            <div class="rb-mag-spacer"></div>
                            <?php
             
                                $componentsJson = get_post_meta( $post->ID, '_rb_recipe_components', false );
                                $components = array();
                                $multi = false;
                                foreach($componentsJson as $index => $compJson) {
                                    $components[] = json_decode( $compJson );
                                    if ($index > 0) $multi = true;
                                }
 
                                foreach($components as $component) {
                                    if ($component == null) {
                                        continue;
                                    } 
                                    ?>

                                    <h4 class="rb-mag-comp-title"><?php echo $component->title; ?></h4>
                                    <ul class="rb-mag-ing-list">
                                    <?php 
                                        foreach ($component->ingredients as $objInfo) 
                                        { 
                                        ?>
                                        <li>
                                            <b><?php echo $objInfo->title ?></b>, <?php echo toFrac($objInfo->amount).' '.$objInfo->denom ?>
                                            <i><?php echo $objInfo->extra ?></i>
                                        </li>
                                        <?php 
                                        } ?>
                                    </ul>
                                
                                    <?php

                                }        
                                ?>
                            <div class="rb-mag-spacer"></div>
                            <?php 
                                $steps = get_post_meta( $post->ID, '_rb_recipe_steps', false );
                                $notes = get_post_meta( $post->ID, '_rb_recipe_notes', true );
                                // $componentsJson = get_post_meta( $post->ID, '_rb_recipe_components', false );
                            ?>
                            <div class="rb-mag-step-list">
                            <?php 
                                foreach($steps as $index => $step) {
                                    echo '<p>'.$step.'</p>';
                                }
                            ?>
                            </div>
                            <p class="rb-mag-tag-list">                           
                            <?php 
                                $mealTypes = get_the_terms($post->ID, 'mealtime');
                                $cuisines = get_the_terms($post->ID, 'cuisine');
                                $mtc = 0;
                                if ($mealTypes) {
                                   
                                    foreach($mealTypes as $mt) {
                                        if ($mtc++ > 0) { echo ', ';}
                                        echo '<a target="_blank" href="'.get_term_link($mt).'">'.$mt->name.'</a>';
                                    }
                                   
                                }
                                if ($cuisines) {                                 
                                    foreach($cuisines as $c) {
                                        if ($mtc++ > 0) { echo ', ';}
                                        echo '<a target="_blank" href="'.get_term_link($c).'">'.$c->name.'</a>';
                                    }
                                }
                            ?>
                            </p>
                            <?php if ($notes) { ?>
                                
                            <div class="rb-mag-spacer"></div>
                                <p class="rb-mag-notes"><i><?php echo $notes; ?></i></p>
                            <?php }  ?>
                            <div class="rb-mag-spacer"></div>
                    </div>
                    <div class="rb-mag-images">
                        <ul class="rb-mag-img-list">
                            <li>
                                <?php  
                                    if ( has_post_thumbnail() ) {
                                        //the_post_thumbnail();
                                        ?>
                                            <div class="rb-magblock-img"><span style="background-image: url('<?php echo get_the_post_thumbnail_url(); ?>')" ></span></div>
                                        <?php
                                    } 
                                ?>
                                <div class="rb-mag-excerpt-block">
                                    <?php 
                                        the_excerpt(); 
                                    ?>
                                </div>
                            </li>
                            <?php 
                                $gallery_pics = get_post_meta($post->ID, 'gallery_images', true);

                                if (is_array($gallery_pics)) {
                                    // $count = count($gallery_pics);
                                    // while(count($gallery_pics) > 0) {                       
                                        if ((count($gallery_pics) > 2) && (count($gallery_pics) % 2 == 1)) {
                                            $image1 = wp_get_attachment_image_src( array_shift($gallery_pics), 'full' ); 
                                            $image2 = wp_get_attachment_image_src( array_shift($gallery_pics), 'full' ); 
                                            $image3 = wp_get_attachment_image_src( array_shift($gallery_pics), 'full' ); 
                                            ?>
                                                <li class="rb-mag-gal-three">
                                                    <div class="rb-magblock-img"><span style="background-image: url('<?php echo $image1[0]; ?>')" ></span></div>
                                                    <div class="rb-magblock-img"><span style="background-image: url('<?php echo $image2[0]; ?>')" ></span></div>
                                                    <div class="rb-magblock-img"><span style="background-image: url('<?php echo $image3[0]; ?>')" ></span></div>
                                                </li>
                                            <?php
                                        }
                                        
                                        while (count($gallery_pics) >= 2) {
                                            $image1 = wp_get_attachment_image_src( array_shift($gallery_pics), 'full' ); 
                                            $image2 = wp_get_attachment_image_src( array_shift($gallery_pics), 'full' ); 
                                            ?>
                                                <li class="rb-mag-gal-two">
                                                    <div class="rb-magblock-img"><span style="background-image: url('<?php echo $image1[0]; ?>')" ></span></div>
                                                    <div class="rb-magblock-img"><span style="background-image: url('<?php echo $image2[0]; ?>')" ></span></div>
                                                </li>
                                            <?php
                                        }
                                        
                                        while (count($gallery_pics)  > 0) {
                                            $image1 = wp_get_attachment_image_src( array_shift($gallery_pics), 'full' ); 
                                            ?>
                                                <li class="rb-mag-gal-one">
                                                <div class="rb-magblock-img"><span style="background-image: url('<?php echo $image1[0]; ?>')" ></span></div>
                                                </li>
                                            <?php
                                        }

                                    // }
                                    

                                foreach($gallery_pics as $key => $pic) {
                                    $image_attributes = wp_get_attachment_image_src( $pic, 'full' ); 
                                    ?>
                                    <li>
                                        <img src="<?php echo $image_attributes[0]; ?>" style="" />
                                    </li>
                                   <?php
                                }
                               } 
                            ?>
                        </ul>
                    </div>
                </div>
                <script>
                    var rbMag = (() => {
                        var t = jQuery('.rb-mag-text').height();
                        var i = jQuery('.rb-mag-images').height();
                        // if (t < i) {
                            jQuery('.rb-mag-images').height(t);
                        // } else {
                            // jQuery('.rb-mag-text').height(i);
                        // }
                    });
                    jQuery(document).ready(rbMag);
                    setTimeout(rbMag, 1000);
                </script>
                <?php 
                if ( comments_open() || get_comments_number() ) {
                    comments_template();
                }
                endwhile;
            ?>
		</div><!-- #content -->
	</div><!-- #primary -->