<?php
    wp_nonce_field( 'recipebook_recipe_save_meta', 'recipebook_recipe_meta_box_nonce' );
    //$nutritionalInfo = get_post_meta( $post->ID, '_ag_product_nutinfo', false );

    //$sizes = get_post_meta( $post->ID, '_ag_product_sizes', true );
    //$keyword = get_post_meta( $post->ID, '_ag_product_keyword', true );
    //$featured = get_post_meta( $post->ID, '_ag_is_featured', true );

    // $ingredients = get_post_meta( $post->ID, '_rb_recipe_ingredients', false );
    $componentsJson = get_post_meta( $post->ID, '_rb_recipe_components', false );
    // var_dump($componentsJson);
    $in_counter = 0;
    $components = array();
    foreach($componentsJson as $index => $compJson) {
        $components[] = json_decode( $compJson );
    }
?>   
<p><i>Click below to add each component and the ingredients that make it...</i></p>
<div class="rb-comp-cards">
    <ul class="rb-comp-card-title-list" id="rb-components-titles">
        <?php 
            $compCount = 0;
            foreach($components as $index => $component) {
                // $component = json_decode( $compJson );
                if ($component == null) {
                    continue;
                } 
                echo '<li id="rb-comp-card-title-'.$index.'" onclick="showTab('.$index.')">
                <input onchange="changeTitle('.$index.')" type="text" id="rb_components_'.$index.'" name="rb_components['.$index.']" value="'.$component->title.'">
                <label id="rb_title_'.$index.'" for="rb_components_'.$index.'">'.$component->title.'</label>
                <span onclick="rbDeleteC('.$index.');" class="rb-delete-btn" title="Remove Component">-</span>
                </li>';
                $compCount++;
            }
            if ($compCount == 0) {
                echo '<li onclick="showTab(0)"><input onchange="changeTitle(0)" type="text" name="rb_components[0]" id="rb_components_0" value="Main"><label  id="rb_title_0" for="rb_components_0">Main</label></li>';
            }
        ?>
        <li id="rb-newComp-btn" title="Add New Component">+</li>
    </ul>
    <ul class="rb-comp-card-content-list" id="rb-components-body">
        <?php 
        
            $compCount = 0;
            foreach($components as $component) {
                $in_counter = 0;
                // $component = json_decode( $compJson );
                if ($component == null) {
                    continue;
                } 
                ?>
                <li class="rb-recipe-card-content">
                    <table class="rb-table" id="rb-ingredient-list-<?php echo $compCount; ?>">
                        <thead>
                            <tr>
                                <th width="60">Amount</th>
                                <th width="60"></th>
                                <th>Ingredient</th>
                                <th width="30%">Notes</th>
                                <th width="24"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($component->ingredients as $objInfo) 
                            { 
                            ?>
                            <tr id="rb-ingredient-<?php echo $compCount; ?>-<?php echo $in_counter; ?>">
                                <td><input value="<?php echo $objInfo->amount ?>" type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_amount[<?php echo $in_counter; ?>]"/></td>
                                <td><input value="<?php echo $objInfo->denom ?>" type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_denominator[<?php echo $in_counter; ?>]"/></td>
                                <td><input value="<?php echo $objInfo->title ?>" type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_name[<?php echo $in_counter; ?>]"/></td>
                                <td><input class="rb_i_note" value="<?php echo $objInfo->extra ?>" type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_extra[<?php echo $in_counter; ?>]"/></td>
                                <td><span onclick="rbDeleteI(<?php echo $compCount; ?>,<?php echo $in_counter; ?>);" class="rb-delete-btn" title="Remove Ingredient">x</span></td>
                            </tr>
                            <?php 
                            $in_counter++; 
                            } ?>
                            <tr id="rb-ingredient-<?php echo $compCount; ?>-<?php echo $in_counter; ?>">
                                <td><input type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_amount[<?php echo $in_counter; ?>]"/></td>
                                <td><input type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_denominator[<?php echo $in_counter; ?>]"/></td>
                                <td><input type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_name[<?php echo $in_counter; ?>]"/></td>
                                <td><input class="rb_i_note" type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_extra[<?php echo $in_counter; ?>]"/></td>
                                <td><span onclick="rbDeleteI(<?php echo $compCount; ?>,<?php echo $in_counter; ?>);" class="rb-delete-btn" title="Remove Ingredient" >x</span></td>
                            </tr>           
                        </tbody>        
                    </table> 
                    <a href="javascript:void(0)" class="taxonomy-add-new" onclick="add_ingredient(<?php echo $compCount; ?>)">+ Add New Ingredient</a>                
                </li>
                <?php
                $compCount++;
            }
            if ($compCount == 0) {
                ?>
                <li class="rb-recipe-card-content">
                    <table class="rb-table" id="rb-ingredient-list-<?php echo $compCount; ?>">
                        <thead>
                            <tr>
                                <th width="60">Amount</th>
                                <th width="60"></th>
                                <th>Ingredient</th>
                                <th width="30%">Notes</th>
                                <th width="24"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="rb-ingredient-<?php echo $compCount; ?>-<?php echo $in_counter; ?>">
                                <td><input type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_amount[<?php echo $in_counter; ?>]"/></td>
                                <td><input type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_denominator[<?php echo $in_counter; ?>]"/></td>
                                <td><input type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_name[<?php echo $in_counter; ?>]"/></td>
                                <td><input class="rb_i_note" type="text" name="rb_component<?php echo $compCount; ?>.rb_ingredient_extra[<?php echo $in_counter; ?>]"/></td>
                                <td><span onclick="rbDeleteI(<?php echo $compCount; ?>,<?php echo $in_counter; ?>);" class="rb-delete-btn" title="Remove Ingredient">-</span></td>
                            </tr>           
                        </tbody>        
                    </table> 
                    <a href="javascript:void(0)" class="taxonomy-add-new" onclick="add_ingredient(<?php echo $compCount; ?>)">+ Add New Ingredient</a>
                </li>
                <?php
            }
        ?> 
    </ul>
</div>
<?php 
    $steps = get_post_meta( $post->ID, '_rb_recipe_steps', false );
    $notes = get_post_meta( $post->ID, '_rb_recipe_notes', true );
?>
<h2 class="rb-subtitle" id="rb-instruction-header">Recipe Instructions</h2>
<ol id="rb-step-list"><?php 
    foreach($steps as $index => $step) {
        echo '<li id="rb-step-'.$index.'"><textarea rows="2" class="rb-textarea" name="rb_steps['.$index.']">'.$step.'</textarea><span onclick="rbDeleteS('.$index.');" class="rb-delete-btn" title="Remove Step" >-</span></li>';
    }
    ?><li id="rb-step-<?php echo count($steps); ?>"><textarea rows="2" class="rb-textarea" name="rb_steps[<?php echo count($steps); ?>]"></textarea><span onclick="rbDeleteS(<?php echo count($steps); ?>);" class="rb-delete-btn" title="Remove Step" >-</span></li>
</ol>
<a id="rb-step-add" class="taxonomy-add-new" href="javascript:void(0)"><span class="rb-add-btn">+</span> Add New Step</a>
<br/>
<h2 class="rb-subtitle" id="rb-notes-header">Recipe Notes</h2>
<textarea rows="5" class="rb-textarea rb_i_note" name="rb_notes"><?php echo $notes; ?></textarea>
<br/>
<script>
    var components = <?php echo (max(0,(count($components)-1))) ?>;
    var ingredients = [];
    ingredients[0] = 0;
    <?php 
        foreach($components as $key => $component) {
            echo 'ingredients['.$key.'] = '.(count( $component->ingredients ) - 1).'; ';
        }            
    ?>

    var steps = <?php echo count($steps); ?>;

    function add_ingredient(comp) {
        ingredients[comp]++;
        var ingrCount = ingredients[comp];
        var html = `<tr id="rb-ingredient-`+comp+'-'+ingrCount+`">
            <td><input type="text" name="rb_component`+comp+`.rb_ingredient_amount[`+ingrCount+`]"/></td>
            <td><input type="text" name="rb_component`+comp+`.rb_ingredient_denominator[`+ingrCount+`]"/></td>
            <td><input type="text" name="rb_component`+comp+`.rb_ingredient_name[`+ingrCount+`]"/></td>
            <td><input class="rb_i_note" type="text" name="rb_component`+comp+`.rb_ingredient_extra[`+ingrCount+`]"/></td>
            <td><span onclick="rbDeleteI(`+comp+','+ingrCount+`);" class="rb-delete-btn" title="Remove Ingredient">-</span></td>
        </tr>`;
        jQuery("#rb-ingredient-list-"+comp).append(html);
        return false;
    }


    function add_component() {
        components++;
        ingredients[components] = -1;


        // Tab
        html = '<li onclick="showTab('+components+')"><input onchange="changeTitle('+components+')"  type="text" name="rb_components['+components+']" id="rb_components_'+components+'" value="" placeholder="..." ><label id="rb_title_'+components+'" for="rb_components_'+components+'">...</label></li>';
        jQuery("#rb-newComp-btn").before(html);
        
        html = `<li class="rb-recipe-card-content">
                    <table class="rb-table" id="rb-ingredient-list-`+components+`">
                    <thead><tr><th width="80">Amount</th><th width="100">Unit</th><th>Ingredient</th><th>Method</th><th></th></tr></thead>
                    <tbody></tbody></table> 
                    <a href="javascript:void(0)" class="taxonomy-add-new" onclick="add_ingredient(`+components+`)">+ Add New Ingredient</a>
                    </li>`;
        jQuery("#rb-components-body").append(html);
        add_ingredient(components);
        showLastComponent();
        return false;

    }

    function addStep() {
        steps++;
        var html = '<li id="rb-step-'+steps+'"><textarea rows="3" class="rb-textarea" name="rb_steps['+steps+']"></textarea><span onclick="rbDeleteS('+steps+');" class="rb-delete-btn" title="Remove Step" >-</span></li>';
        jQuery("#rb-step-list").append(html);
    }

    function rbDeleteI(comp, index) {
        // Delete Ingredient
        jQuery("#rb-ingredient-"+comp+'-'+index+" input").val("");
        jQuery("#rb-ingredient-"+comp+'-'+index).hide();
    }

    function rbDeleteS(index) {
        // Delete Step
        jQuery("#rb-step-"+index+" textarea").val("");
        jQuery("#rb-step-"+index).hide();
    }

    function rbDeleteC(index) {
        // Delete Component
        //for (var i = 0; i < ingredients[index]; i++) {
            // rbDeleteI(index, i);
            jQuery("#rb-comp-card-title-" + index).remove();

        //}

        // move each element down one
        for (var c = index + 1; c <= components; c++) {
            const nC = c-1;
            for (var i = 0; i <= ingredients[c]; i++) {
                jQuery("#rb-ingredient-" + e + "-" + i + " input").foreach(input => {
                    var nameSplit = jQuery(input).attr("name").split(".");
                    nameSplit[0] = "rb_component" + nC;
                    jQuery(input).attr("name", nameSplit.join("."));
                });
                jQuery("#rb-ingredient-" + c + "-" + i).attr("id", "#rb-ingredient-" + nC + "-" + i);
            }
            jQuery("#rb_components_" + c).attr("id", "rb_components_" + nC).attr("name", "rb_components[" + nC + "]").attr("onchange", "changeTitle(" + nC + ")");
            jQuery("#rb_title_" + c).attr("for","rb_components_" + nC).attr("id", "rb_title_" + nC);
            jQuery("#rb-comp-card-title-" + c + " .rb-delete-btn").attr("onclick", "rbDeleteC(" + nC + ");")
            jQuery("#rb-comp-card-title-" + c).attr("id", "rb-comp-card-title-" + nC).attr("onclick", "showTab(" + nC + ")");
            ingredients[nC] = ingredients[c];
        }

        components--;
    }

    function showLastComponent() {
        jQuery("#rb-components-titles li").removeClass('active');
        jQuery("#rb-components-titles li:nth-last-child(2)").addClass('active');
        jQuery("#rb-components-titles li:nth-last-child(2) input").focus();

        jQuery("#rb-components-body li").removeClass('active');
        jQuery("#rb-components-body li:last").addClass('active');
    }

    function showTab(i) {
        var n = i + 1;
        jQuery("#rb-components-titles > li").removeClass('active');
        jQuery("#rb-components-titles > li:nth-child(" + n + ")").addClass('active');

        jQuery("#rb-components-body li").removeClass('active');
        jQuery("#rb-components-body li:nth-child("+n+")").addClass('active');
    }

    function changeTitle(i) {
        var title = jQuery("#rb_components_"+i).val();
        jQuery("#rb_title_"+i).html(title);
    }

    jQuery(document).ready(function () {
        console.log("ready");
        jQuery('#rb-newComp-btn').click(function (e) {
            e.preventDefault();
            add_component();
            return false;
        });

        jQuery("#rb-step-add").click(function (e) {
            e.preventDefault();
            addStep();
        });

        showTab(0);
    });
    
</script>    