<?php

    $detailsJson = get_post_meta( $post->ID, '_rb_recipe_details', true );
    $details = json_decode( $detailsJson.'' );
    $options = get_option( 'recipebook_settings' ); 
?>
    <style>
        :root {
        --rb-accent-color: <?php echo isset($options['accent_color']) ? esc_attr($options['accent_color']) : '#ff9800'; ?>; 
        --rb-accent-color-base: <?php echo isset($options['accent_color_base']) ? esc_attr($options['accent_color_base']) : '#92621a'; ?>;
        }
    </style>
    <?php
        $basic_css_path = plugin_dir_path( dirname( __FILE__ ) ) . 'styles/style-basic.css';
        $basic_css_version = file_exists( $basic_css_path ) ? filemtime( $basic_css_path ) : '1.0';
    ?>
    <link href="<?php echo esc_url( recipebook_url . 'styles/style-basic.css?ver=' . $basic_css_version ); ?>" rel="stylesheet" type="text/css" />
    
    <?php $post_body = get_the_content(); 
    
    if ($post_body) {
        echo wp_kses_post( $post_body );
        ?><hr/><?php
    }
    ?>
    <div class="rb-title-block">
        <div class="rb-title-footer">
            <?php if($details) { ?>
            <ul class="rb-details-list">
            <?php if($details->cals) { ?>
                <li>
                    <span>Calories:</span> <?php echo esc_html( $details->cals ); ?>
                </li>
            <?php }  if($details->serves) { ?>
                <li>
                    <span>Serves:</span> <?php echo esc_html( $details->serves ); ?>
                </li>
            <?php } if($details->difficulty) { ?>
                <li>
                    <span>Difficulty:</span> <?php echo esc_html( $details->difficulty ); ?>
                </li>     
            <?php } 

                $mealTypes = get_the_terms($post->ID, 'mealtime');
                if ($mealTypes) {
                    ?><li>
                    <span>Meal type:</span> <?php
                    foreach($mealTypes as $i => $mt) {
                        if ($i > 0) { echo ', ';}
                        $term_url = get_term_link( $mt );
                        if ( ! is_wp_error( $term_url ) ) { echo '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $term_url ) . '">' . esc_html( $mt->name ) . '</a>'; }
                    }
                    ?></li><?php
                }
                $cuisines = get_the_terms($post->ID, 'cuisine');
                if ($cuisines) {
                    ?><li>
                    <span>Cuisine:</span> <?php
                    foreach($cuisines as $i => $c) {
                        if ($i > 0) { echo ', ';}
                        $term_url = get_term_link( $c );
                        if ( ! is_wp_error( $term_url ) ) { echo '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $term_url ) . '">' . esc_html( $c->name ) . '</a>'; }
                    }
                    ?></li><?php
                }
            ?>
            </ul>
            <?php } ?>
        </div>
    </div>
    <div class="rb-basic-content-block">
        <?php
            $timesJson = get_post_meta( $post->ID, '_rb_recipe_times', true );
            $times = json_decode( $timesJson.'' );
            if ($times && ($times->prepHrs || $times->prepMins || $times->cookHrs || $times->cookMins || $times->totalHrs || $times->totalMins)) {
        ?>

        <table class="rb-times-table">
            <tr>
                <?php if ($times->prepHrs || $times->prepMins ) { ?>
                <td>
                    <h3>Prep Time</h3>
                    <div class="rb-time-amount"><?php 
                            if ($times->prepHrs) 
                                echo '<div class="rb-time-part">' . esc_html( $times->prepHrs ) . '<span>h</span></div>';
                            
                            if ($times->prepMins) 
                                echo '<div class="rb-time-part">' . esc_html( $times->prepMins ) . '<span>m</span></div>';
                    ?></div>
                </td>
                <?php 
                }
                if ($times->cookHrs || $times->cookMins ) {
                ?>
                <td>
                    <h3>Cook Time</h3>
                    <div class="rb-time-amount"><?php 
                            if ($times->cookHrs) 
                                echo '<div class="rb-time-part">' . esc_html( $times->cookHrs ) . '<span>h</span></div>';
                            
                            if ($times->cookMins) 
                                echo '<div class="rb-time-part">' . esc_html( $times->cookMins ) . '<span>m</span></div>';
                    ?></div>
                </td>
                 <?php 
                }
                if ($times->totalHrs || $times->totalMins ) {
                ?>
                <td>
                    <h3>Total Time</h3>
                    <div class="rb-time-amount">
                    <?php 
                            if ($times->totalHrs) 
                                echo '<div class="rb-time-part">' . esc_html( $times->totalHrs ) . '<span>h</span></div>';
                            
                            if ($times->totalMins) 
                                echo '<div class="rb-time-part">' . esc_html( $times->totalMins ) . '<span>m</span></div>';
                        
                        ?>  
                    </div>
                </td>
                 <?php } ?>
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
            } ?>
            <h4 class="rb-comp-subtitle"><?php
                if ($multi) {
                    echo esc_html( $component->title );
                } else {
                    echo '<p>Ingredients</p>';
                } ?>
                <div class="recipe-multiplier">
                    <button class="rb-rm-btn" data-multi="0.5"><span>x</span>½</button>
                    <button class="rb-rm-btn -selected" data-multi="1"><span>x</span>1</button>
                    <button class="rb-rm-btn" data-multi="2"><span>x</span>2</button>
                    <button class="rb-rm-btn" data-multi="4"><span>x</span>4</button>
                </div>
            </h4>
            <div class="rb-comp-ing">
                <table class="rb-basic-ing-list">
                <?php 
                    foreach ($component->ingredients as $objInfo) 
                    { 
                    ?>
                    <tr>
                        <td>
                            <b class="rb-rm-amount" data-amount="<?php echo esc_attr( trim( $objInfo->amount ) ); ?>"><?php echo esc_html( toFrac( $objInfo->amount ) ); ?></b> <span class="rb-rm-unit" data-unit="<?php echo esc_attr( $objInfo->denom ); ?>"><?php echo esc_html( $objInfo->denom ); ?></span>
                        </td>
                        <td>
                            <?php echo esc_html( $objInfo->title ); ?> <i><?php echo esc_html( $objInfo->extra ); ?></i>
                        </td>
                    </tr>
                    <?php 
                    } ?>
                </table>
            </div> <?php /* END of components */ ?>
            <?php
            }        
        ?> 
    </div> <?php /* END of  */ ?>
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
                echo '<li><div class="rb-comp-ing"><p>' . esc_html( $step ) . '</p></div></li>';
            }
        ?>
        
        </ol>
        <?php if ($notes) { ?>
            <div class="rb-notes"><?php echo esc_html( $notes ); ?></div>
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
                        echo '<th>' . esc_html( $value ) . '</th>';
                    }
                }
            ?></tr>
            </thead>
            <tbody>
                <tr><?php
                if(is_array($detailsNutTitles)) {
                    foreach($detailsNutAmounts as $key => $value) {
                        echo '<td>' . esc_html( $value ) . '</td>';
                    }
                }
            ?></tr>
            </tbody>
        </table>
    </div>
    <ul class="rb-gallery-list"><?php 
    $gallery_pics = get_post_meta($post->ID, 'gallery_images', true);
    if (is_array($gallery_pics)) {
        foreach($gallery_pics as $key => $pic) {
        $image_attributes = wp_get_attachment_image_src( absint( $pic ), 'full' ); 
        if ( ! $image_attributes ) { continue; }
        ?><li>
            <a href="<?php echo esc_url( $image_attributes[0] ); ?>" target="_blank" rel="noopener noreferrer">
                <span class="rb-square-img" style="background-image: url('<?php echo esc_url( $image_attributes[0] ); ?>')"></span>
            </a>
        </li><?php
        }
    } 
    ?></ul>