<?php
    wp_nonce_field( 'recipebook_recipe_save_meta', 'recipebook_recipe_details_box_nonce' );
    $timesJson = get_post_meta( $post->ID, '_rb_recipe_times', true );
    $times = json_decode( $timesJson.'' );

    $detailsJson = get_post_meta( $post->ID, '_rb_recipe_details', true );
    $details = json_decode( $detailsJson.'' );
    wp_enqueue_script( 'myuploadscript', recipebook_url . '/admin/script/image-upload.js', array('jquery'), null, false );
    //$sizes = get_post_meta( $post->ID, '_ag_product_sizes', true );
    //$keyword = get_post_meta( $post->ID, '_ag_product_keyword', true );
    //$featured = get_post_meta( $post->ID, '_ag_is_featured', true );
?>
    <link href="<?php echo recipebook_url; ?>/admin/admin.css" rel="stylesheet" type="text/css" />
<div class="row rb-times">
    <div class="col-sm-4">
        <h3>Prep Time</h3>
        <table>
            <tr>
                <td>
                    <input type="number" name="rb_prep_hrs" id="rb_prep_hrs" min="0" max="72" value="<?php echo $times ? $times->prepHrs : ''; ?>" />
                    <label for="rb_prep_hrs" class="rb-dd-label">hrs</label>
                </td>
                <td>
                    <input type="number" name="rb_prep_mins" id="rb_prep_mins" min="0" max="59" value="<?php echo $times ? $times->prepMins : ''; ?>" />
                    <label for="rb_prep_mins" class="rb-dd-label">mins</label>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-sm-4">
        <h3>Cook Time</h3>
        <table>
            <tr>
                <td>
                    <input type="number" name="rb_cook_hrs" id="rb_cook_hrs" min="0" max="72" value="<?php echo $times ? $times->cookHrs : ''; ?>" />
                    <label for="rb_cook_hrs" class="rb-dd-label">hrs</label>
                </td>
                <td>
                    <input type="number" name="rb_cook_mins" id="rb_cook_mins" min="0" max="59" value="<?php echo $times ? $times->cookMins : ''; ?>" />
                    <label for="rb_cook_mins" class="rb-dd-label">mins</label>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-sm-4">
        <h3>Total Time</h3>
        <table>
            <tr>
                <td>
                    <input type="number" name="rb_total_hrs" id="rb_total_hrs" min="0" max="72" value="<?php echo $times ? $times->totalHrs : ''; ?>" />
                    <label for="rb_total_hrs" class="rb-dd-label">hrs</label>
                </td>
                <td>
                    <input type="number" name="rb_total_mins" id="rb_total_mins" min="0" max="59" value="<?php echo $times ? $times->totalMins : ''; ?>" />
                    <label for="rb_total_mins" class="rb-dd-label">mins</label>
                </td>
            <tr>
        </table>
    </div>
</div>
<hr/>
<div class="row rb-details">
    <div class="col-sm-4">
        <table>
            <tr>
                <td><label for="rb_d_calories">Calories:</label></td>
                <td><input type="text" name="rb_d_calories" id="rb_d_calories" value="<?php echo $details ? $details->cals : ''; ?>" /></td>
            </tr>
        </table>
    </div>
    <div class="col-sm-4">
        <table>
            <tr>
                <td><label for="rb_d_serves">Serves:</label></td>
                <td><input type="text" name="rb_d_serves" id="rb_d_serves" value="<?php echo $details ? $details->serves : ''; ?>" /></td>
            </tr>
        </table>
    </div>
    <div class="col-sm-4">
        <table>
            <tr>
                <td><label for="rb_d_difficulty">Difficulty:</label></td>
                <td><input type="text" name="rb_d_difficulty" id="rb_d_difficulty" value="<?php echo $details ? $details->difficulty : ''; ?>" /></td>
            </tr>
        </table>
    </div>
</div>
<div class="rb_meta_nuts">
    <?php 
        $detailsNutTitles = get_post_meta( $post->ID, '_rb_nut_titles', true );
        $detailsNutAmounts = get_post_meta( $post->ID, '_rb_nut_amounts', true );
    ?>
    <i>Nutritional Infomation:</i><br/>
    <button id="rb_nut_add" class="button">Add Item</button>
    <table class="rb_nut_table">
        <tr id="rb_nut_title"><?php
            if(is_array($detailsNutTitles)) {
                foreach($detailsNutTitles as $key => $value) {
                    echo '<td><input name="rb_n_t['.$key.']" value="'.$value.'" tabindex="'.$key.'" /></td>';
                }
            }
        ?></tr>
        <tr id="rb_nut_amount"><?php
            if(is_array($detailsNutAmounts)) {
                foreach($detailsNutAmounts as $key => $value) {
                    echo '<td><input name="rb_n_a['.$key.']" value="'.$value.'" tabindex="'.$key.'" /></td>';
                }
            }
        ?></tr>
        <tr id="rb_nut_remove"><?php
            if(is_array($detailsNutTitles)) {
                foreach($detailsNutTitles as $key => $value) {
                    echo '<td><button onclick="remove_nut(event, '.$key.')">Remove</button></td>';
                }
            }
        ?></tr>
    </table>
    <script>
        var rbNuts = 0<?php if(is_array($detailsNutTitles)) { echo count($detailsNutTitles); } ?>;
        jQuery('#rb_nut_add').click(e => {
            e.preventDefault();
            jQuery('#rb_nut_title').append('<td><input name="rb_n_t['+rbNuts+']" tabindex="'+rbNuts+'" /></td>');
            jQuery('#rb_nut_amount').append('<td><input name="rb_n_a['+rbNuts+']" tabindex="'+rbNuts+'" /></td>');
            jQuery('#rb_nut_remove').append('<td><button onclick="remove_nut(event, '+rbNuts+')">Remove</button></td>');
            rbNuts++;
        });

        function remove_nut(e, i) {
            e.preventDefault();
            if (jQuery("input[name='rb_n_t["+i+"]']").val() || jQuery("input[name='rb_n_a["+i+"]']").val())
            {
                if (!confirm("Are you sure you want to remove this column?")) {
                    return;
                }
            }
            jQuery('#rb_nut_title td:nth-child('+(i+1)+')').remove();
            jQuery('#rb_nut_amount td:nth-child('+(i+1)+')').remove();
            jQuery('#rb_nut_remove td:nth-child('+(i+1)+')').remove();
            rbNuts--;
            reassignNutNumbers();
        }

        function reassignNutNumbers() {
            for(var i = 0; i < rbNuts; i++) {
                jQuery('#rb_nut_title td:nth-child('+(i+1)+') input').attr('name','rb_n_t['+i+']').attr('tabindex',i);
                jQuery('#rb_nut_amount td:nth-child('+(i+1)+') input').attr('name','rb_n_a['+i+']').attr('tabindex',i);
                jQuery('#rb_nut_remove td:nth-child('+(i+1)+') button').attr('onclick','remove_nut(event, '+i+')');
            }
        }
    </script>
</div>