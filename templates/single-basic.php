<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>
<link href="<?php echo recipebook_url; ?>/styles/style-basic.css?<?php echo (rand()); ?>" rel="stylesheet" type="text/css" />
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
            <div class="rb-wrapper">
			<?php
			while ( have_posts() ) :
                the_post();
                
                the_content();

                $detailsJson = get_post_meta( $post->ID, '_rb_recipe_details', true );
                $details = json_decode( $detailsJson.'' );
                
                ?>
                <div class="rb-title-block">
                    <div class="rb-title-top">
                        <div class="rb-title">
                            <h1 class="rb-recipe-title"><?php the_title(); ?></h1>
                            <div class="rb-excerpt-text">
                                <?php 
                                    the_excerpt(); 
                                ?>
                            </div>
                        </div>
                        <div class="rb-image">
                            <?php  
                                if ( has_post_thumbnail() ) {
                                    $mainImg = get_the_post_thumbnail_url();
                                    ?>
                                    <a href="<?php echo $mainImg; ?>" target="_blank"><div style="background-image: url('<?php echo $mainImg; ?>')"></div></a>
                                    <?php
                                } 
                            ?>
                        </div>
                    </div>
                    <div class="rb-title-footer">
                        <?php if($details) { ?>
                        <ul class="rb-details-list">
                        <?php if($details->cals) { ?>
                            <li>
                                <span>Calories:</span>
                                <?php echo $details->cals; ?>
                            </li>
                        <?php } ?>
                        <?php if($details->serves) { ?>
                            <li>
                                <span>Serves:</span>
                                <?php echo $details->serves; ?>
                            </li>
                        <?php } ?>
                        <?php if($details->difficulty) { ?>
                            <li>
                                <span>Difficulty:</span>
                                <?php echo $details->difficulty; ?>
                            </li>     
                        <?php } ?>
                        
                        <?php 
                            $mealTypes = get_the_terms($post->ID, 'mealtime');
                            if ($mealTypes) {
                                ?><li>
                                <span>Meal type:</span> <?php
                                foreach($mealTypes as $i => $mt) {
                                    if ($i > 0) { echo ', ';}
                                    echo '<a target="_blank" href="'.get_term_link($mt).'">'.$mt->name.'</a>';
                                }
                                ?></li><?php
                            }
                        ?>
                        <?php 
                            $cuisines = get_the_terms($post->ID, 'cuisine');
                            if ($cuisines) {
                                ?><li>
                                <span>Cuisine:</span> <?php
                                foreach($cuisines as $i => $c) {
                                    if ($i > 0) { echo ', ';}
                                    echo '<a target="_blank" href="'.get_term_link($c).'">'.$c->name.'</a>';
                                }
                                ?></li><?php
                            }
                        ?>
                        </ul>
                        <?php } ?>
                    </div>
                </div>
                <ul class="rb-gallery-list">
                    <?php 
                        $gallery_pics = get_post_meta($post->ID, 'gallery_images', true);
                        if (is_array($gallery_pics)) {
                        foreach($gallery_pics as $key => $pic) {
                            $image_attributes = wp_get_attachment_image_src( $pic, 'full' ); 
                            ?>
                            <li>
                                <a href="<?php echo $image_attributes[0]; ?>" target="_blank"><div class="rb-square-img" style="background-image: url('<?php echo $image_attributes[0]; ?>')"></div></a>
                            </li>
                        <?php
                        }
                    } 
                    ?>
                </ul>
            <div class="rb-basic-content-block">
            <?php
                $timesJson = get_post_meta( $post->ID, '_rb_recipe_times', true );
                $times = json_decode( $timesJson.'' );
                if ($times) {
            ?>

            <table class="rb-times-table">
                <tr>
                    <td>
                        <h3>Prep Time</h3>
                        <div class="rb-time-amount">
                            <?php 
                                if ($times->prepHrs) 
                                    echo '<div class="rb-time-part">'.$times->prepHrs.'<span>h</span></div>';
                                
                                if ($times->prepMins) 
                                    echo '<div class="rb-time-part">'.$times->prepMins.'<span>m</span></div>';
                            
                            ?>           
                        </div>
                    </td>
                    <td>
                        <h3>Cook Time</h3>
                        <div class="rb-time-amount">
                        <?php 
                                if ($times->cookHrs) 
                                    echo '<div class="rb-time-part">'.$times->cookHrs.'<span>h</span></div>';
                                
                                if ($times->cookMins) 
                                    echo '<div class="rb-time-part">'.$times->cookMins.'<span>m</span>';
                            
                            ?>  
                        </div>
                    </td>
                    <td>
                        <h3>Total Time</h3>
                        <div class="rb-time-amount">
                        <?php 
                                if ($times->totalHrs) 
                                    echo '<div class="rb-time-part">'.$times->totalHrs.'<span>h</span></div>';
                                
                                if ($times->totalMins) 
                                    echo '<div class="rb-time-part">'.$times->totalMins.'<span>m</span></div>';
                            
                            ?>  
                        </div>
                    </td>
                </tr>
            </table>
            <?php } ?>
            
            
            <?php
             
                $componentsJson = get_post_meta( $post->ID, '_rb_recipe_components', false );
                $components = array();
                $multi = false;
                foreach($componentsJson as $index => $compJson) {
                    $components[] = json_decode( $compJson );
                    if ($index > 0) {
                        $multi = true;
                    } 
                }
                
                    
                foreach($components as $component) {
                    if ($component == null) {
                        continue;
                    } 
                    

                    if ($multi) {
                        echo '<h4 class="rb-comp-subtitle">'.$component->title.'</h4>';
                    } else {
                        echo '<h4 class="rb-comp-subtitle">Ingredients</h4>';
                    }

                    echo '<div class="rb-comp-ing">';
                    ?>
                    <table class="rb-basic-ing-list">
                    <?php 
                        foreach ($component->ingredients as $objInfo) 
                        { 
                        ?>
                        <tr>
                            <td>
                                <b><?php echo toFrac($objInfo->amount) ?></b> <?php echo $objInfo->denom; ?>
                            </td>
                            <td>
                                <?php echo $objInfo->title; ?>
                                <i><?php echo $objInfo->extra ?></i>
                            </td>
                        </tr>
                        <?php 
                        } ?>
                    </table>
                   
                    <?php
                        echo '</div>';
                    }        
                ?> 
       </div>
       <div class="rb-basic-content-block">   
            <?php 
                $steps = get_post_meta( $post->ID, '_rb_recipe_steps', false );
                $notes = get_post_meta( $post->ID, '_rb_recipe_notes', true );
                // $componentsJson = get_post_meta( $post->ID, '_rb_recipe_components', false );
            ?>
            <h4 class="rb-comp-subtitle">Directions</h4>
            <ol class="rb-basic-steps">
            
            <?php 
                
                foreach($steps as $index => $step) {
                    echo '<li><div class="rb-comp-ing"><p>'.$step.'</p></div></li>';
                }
            ?>
            
            </ol>
            <?php if ($notes) { ?>
                <p class="rb-notes"><i><?php echo $notes; ?></i></p>
            <?php } ?>

        </div>
        <div class="rb-meta">
            
                <?php 
                    $detailsNutTitles = get_post_meta( $post->ID, '_rb_nut_titles', true );
                    $detailsNutAmounts = get_post_meta( $post->ID, '_rb_nut_amounts', true );
                ?>
                    <table class="rb-nutrients">
                        <thead>
                            <tr ><?php
                            if(is_array($detailsNutTitles)) {
                                foreach($detailsNutTitles as $key => $value) {
                                    echo '<th>'.$value.'</th>';
                                }
                            }
                        ?></tr>
                        </thead>
                        <tbody>
                            <tr><?php
                            if(is_array($detailsNutTitles)) {
                                foreach($detailsNutAmounts as $key => $value) {
                                    echo '<td>'.$value.'</td>';
                                }
                            }
                        ?></tr>
                        </tbody>
                    </table>
                </div>
                <?php 
                if ( comments_open() || get_comments_number() ) {
                    comments_template();
                }
                endwhile;
            ?>
            </div>
		</div><!-- #content -->
	</div><!-- #primary -->