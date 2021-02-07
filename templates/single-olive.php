<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>
<link href="<?php echo recipebook_url; ?>/styles/style-olive.css?<?php echo (rand()); ?>" rel="stylesheet" type="text/css" />
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
        
			<?php
			while ( have_posts() ) :
                the_post();
                
                the_content();
                
                ?>
                <div class="rb-ol-wrapper">
                    <?php  
                        $title_back = '';
                        $title_height = 600;

                        $timesJson = get_post_meta( $post->ID, '_rb_recipe_times', true );
                        $times = json_decode( $timesJson.'' );

                        $detailsJson = get_post_meta( $post->ID, '_rb_recipe_details', true );
                        $details = json_decode( $detailsJson.'' );
                        
                        if ( has_post_thumbnail() ) {
                            $title_back = get_the_post_thumbnail_url();
                        } 
                    ?>
                    <div class="rb-ol-header-card" style="background-image: url('<?php echo $title_back; ?>');height:<?php echo $title_height; ?>px">  
                        <a href="<?php echo get_the_post_thumbnail_url(); ?>" target="_blank">View full Image</a>
                    </div>
                    <div class="rb-ol-main">
                        <div class="rb-ol-images">
                            <ul class="rb-ol-img-list">
                                <?php 
                                    $gallery_pics = get_post_meta($post->ID, 'gallery_images', true);
                                    if (is_array($gallery_pics)) {
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
                        <div class="rb-ol-content">
                            <h1 class="rb-ol-title">
                                <?php the_title(); ?>
                                <div class="rb-ol-terms">
                                    <?php 
                                        $mealTypes = get_the_terms($post->ID, 'mealtime');
                                        if ($mealTypes) {
                                            foreach($mealTypes as $i => $mt) {
                                                echo '<a target="_blank" href="'.get_term_link($mt).'">'.$mt->name.'</a>';
                                            }
                                        }
                                    ?>
                                     <?php 
                                        $cuisines = get_the_terms($post->ID, 'cuisine');
                                        if ($cuisines) {
                                            if ($mealTypes) { echo '/ '; }
                                            foreach($cuisines as $i => $c) {
                                                if ($i > 0) { echo ', ';}
                                                echo '<a target="_blank" href="'.get_term_link($c).'">'.$c->name.'</a>';
                                            }
                                        }
                                    ?>
                                </div>
                            </h1>
                            <div class="rb-ol-spacer"></div>
                            <ul class="rb-ol-table">
                                <?php if($details->serves) {  
                                ?><li>SERVES <?php echo $details->serves; ?>  
                                </li><?php 
                                } 
                                if ($times->prepHrs || $times->prepMins) { 
                                ?><li>
                                    PREP <?php 
                                            if ($times->prepHrs) 
                                                echo $times->prepHrs.' HOURS';
                                            
                                            if ($times->prepMins) 
                                                echo ($times->prepHrs ? ' &amp; ' : '').$times->prepMins.' MINUTES';
                                        ?>  
                                </li><?php 
                                }
                                if ($times->cookHrs || $times->cookMins) { 
                                ?><li>
                                    COOK <?php 
                                            if ($times->cookHrs) 
                                                echo $times->cookHrs.' HOURS';
                                            
                                            if ($times->cookMins) 
                                                echo ($times->cookHrs ? ' &amp; ' : '').$times->cookMins.' MINUTES';
                                        ?></li><?php 
                                    } if ($times->totalHrs || $times->totalMins) { 
                                ?><li>
                                    TOTAL <?php
                                            if ($times->totalHrs) 
                                                echo $times->totalHrs.' HOURS';
                                            
                                            if ($times->totalMins) 
                                                echo ($times->totalHrs ? ' &amp; ' : '').$times->totalMins.' MINUTES';
                                        ?></li>
                                <?php }  if($details->difficulty) { 
                                    ?><li>
                                        DIFFICULTY 
                                        <?php echo strtoupper($details->difficulty); ?>
                                    </li><?php } ?>
                            </ul>
                            <div class="rb-ol-ingredients">
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
                                    <?php if ($multi) { ?>
                                    <h4 class="rb-ol-comp-title"><?php echo $component->title; ?></h4>
                                    <?php } ?>
                                    <ul class="rb-ol-ing-list">
                                    <?php 
                                        foreach ($component->ingredients as $objInfo) 
                                        { 
                                        ?>
                                        <li>
                                            <?php echo toFrac($objInfo->amount).''.$objInfo->denom ?> <?php echo $objInfo->title ?>
                                            <i><?php echo $objInfo->extra ?></i>
                                        </li>
                                        <?php 
                                        } ?>
                                    </ul>
                                
                                    <?php

                                }        
                                ?>
                            </div>
                            <div class="rb-ol-spacer"></div>
                            <div class="rb-ol-steps">
                                
                            <?php 
                                $steps = get_post_meta( $post->ID, '_rb_recipe_steps', false );
                                $notes = get_post_meta( $post->ID, '_rb_recipe_notes', true );
                                // $componentsJson = get_post_meta( $post->ID, '_rb_recipe_components', false );
                            ?>
                           
                            <?php 
                                foreach($steps as $index => $step) {
                                    echo '<p>'.$step.'</p> ';
                                }
                            ?>
                            
                            </div>
                            <?php if ($notes) { ?>
                                
                                <div class="rb-ol-spacer"></div>
                                    <p class="rb-ol-notes"><i><?php echo $notes; ?></i></p>
                            <?php }  ?>
                            <div class="rb-ol-meta">
                            <?php 
                                $detailsNutTitles = get_post_meta( $post->ID, '_rb_nut_titles', true );
                                $detailsNutAmounts = get_post_meta( $post->ID, '_rb_nut_amounts', true );
                            ?>
                                <table class="rb-ol-nutrients">
                                    <thead>
                                        <tr id="rb_nut_title"><?php
                                        if(is_array($detailsNutTitles)) {
                                            foreach($detailsNutTitles as $key => $value) {
                                                echo '<th>'.$value.'</th>';
                                            }
                                        }
                                    ?></tr>
                                    </thead>
                                    <tbody>
                                        <tr id="rb_nut_remove"><?php
                                        if(is_array($detailsNutTitles)) {
                                            foreach($detailsNutAmounts as $key => $value) {
                                                echo '<td>'.$value.'</td>';
                                            }
                                        }
                                    ?></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    var rbMag = (() => {
                        var t = jQuery('.rb-ol-img-list').height();
                        jQuery('.rb-ol-main').css('min-height',t+'px');
                        // if (t < i) {
                            // jQuery('.rb-mag-images').height(t);
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