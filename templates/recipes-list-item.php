
<?php
/**
* The template used to display archive content
*/
?>
<?php 
        $url = get_permalink();
        $detailsJson = get_post_meta( $post->ID, '_rb_recipe_details', true );
        $details = json_decode( $detailsJson.'' );
        
        ?>
        <a href="<?php echo $url; ?>">
        <div class="rb-list-block">
            <div class="rb-title-top">
                <div class="rb-list-title">
                    <h2 class="rb-recipe-title"><?php the_title(); ?></h2>
                    <div class="rb-excerpt-text">
                        <?php 
                            $ex = get_the_excerpt(); 
                            echo trim(substr($ex, 0, 60)).(strlen($ex) > 60 ? '...' : '');
                        ?>
                    </div>
                </div>
                <div class="rb-list-image">
                    <?php  
                        if ( has_post_thumbnail() ) {
                            $mainImg = get_the_post_thumbnail_url();
                            ?>
                            <div style="background-image: url('<?php echo $mainImg; ?>')"></div>
                            <?php
                        } else {
                            ?>
                            <div style="background-image: url('<?php echo recipebook_url; ?>'/images/recipe_noimage.jpg)"></div>
                            <?php
                        }
                    ?>
                </div>
            </div>
            <div class="rb-title-footer">
                <ul class="rb-details-list">
                <?php if($details) { ?>
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
                <?php } ?>
                    <li>&nbsp;</li>
                </ul>
            </div>
        </div></a>
<?php
    
?>