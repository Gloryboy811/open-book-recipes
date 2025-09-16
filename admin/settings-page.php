<link href="<?php echo recipebook_url; ?>admin/admin.css" rel="stylesheet" type="text/css" />
<div class="wrap">
    <h2>Recipe Book Settings</h2>
    <form action="options.php" method="post"><?php
        settings_fields( 'recipebook_settings' );
        do_settings_sections( __FILE__ );
        
        //get the older values, wont work the first time
        
        $options = get_option( 'recipebook_settings' ); 
        $theme = $options['layout_theme'] ? $options['layout_theme'] : 'basic';
        ?>

        
        <table class="form-table">
            <tr>
                <th scope="row">Layout Style</th>
                <td>
                    <fieldset>
                        <ul class="rb-image-select">
                            <li>
                                <label>
                                    <input name="recipebook_settings[layout_theme]" type="radio" value="basic" id="" <?php echo ($theme == 'basic') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/theme-basic.png"/>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input name="recipebook_settings[layout_theme]" type="radio" value="olive" id="" <?php echo ($theme == 'olive') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/theme-olive.png"/>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input name="recipebook_settings[layout_theme]" type="radio" value="magazine" id="" <?php echo ($theme == 'magazine') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/theme-mag.png"/>
                                </label>
                            </li>
                        </ul>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Card Style</th>
                <td>
                    <fieldset>
                        <ul class="rb-image-select">
                            <li>
                                <label>
                                    <input name="recipebook_settings[title_card]" type="radio" value="icons" id="" <?php echo (isset($options['title_card']) && $options['title_card'] == 'icons') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/title-1.png"/>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input name="recipebook_settings[title_card]" type="radio" value="heading" id="" <?php echo (isset($options['title_card']) && $options['title_card'] == 'heading') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/title-2.png"/>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input name="recipebook_settings[title_card]" type="radio" value="excerpt" id="" <?php echo (isset($options['title_card']) && $options['title_card'] == 'excerpt') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/title-3.png"/>
                                </label>
                            </li>
                        </ul>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Image Size</th>
                <td>
                    <fieldset>
                        <ul class="rb-image-select">
                            <li>
                                <label>
                                    <input name="recipebook_settings[image_size]" type="radio" value="corner" id="" <?php echo (isset($options['image_size']) && $options['image_size'] == 'corner') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/image-corner.png"/>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input name="recipebook_settings[image_size]" type="radio" value="half" id="" <?php echo (isset($options['image_size']) && $options['image_size'] == 'half') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/image-half.png"/>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input name="recipebook_settings[image_size]" type="radio" value="excewiderpt" id="" <?php echo (isset($options['image_size']) && $options['image_size'] == 'wide') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/image-wide.png"/>
                                </label>
                            </li>
                        </ul>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Rating Placement</th>
                <td>
                    <fieldset>
                        <ul class="rb-image-select">
                            <li>
                                <label>
                                    <input name="recipebook_settings[rating_place]" type="radio" value="over" id="" <?php echo (isset($options['rating_place']) && $options['rating_place'] == 'over') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/rating-over.png"/>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input name="recipebook_settings[rating_place]" type="radio" value="side" id="" <?php echo (isset($options['rating_place']) && $options['rating_place'] == 'side') ? 'checked' : ''; ?> />
                                    <img src="<?php echo recipebook_url; ?>/admin/images/rating-side.png"/>
                                </label>
                            </li>
                        </ul>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Accent Colours</th>
                <td>
                    Accent:
                    <fieldset>
                        <input type="color" name="recipebook_settings[accent_color]" value="<?php echo isset($options['accent_color']) ? esc_attr($options['accent_color']) : '#ff9800'; ?>" />
                        <span style="margin-left:10px;">Preview: <span style="display:inline-block;width:24px;height:24px;background:<?php echo isset($options['accent_color']) ? esc_attr($options['accent_color']) : '#ff9800'; ?>;border:1px solid #ccc;"></span></span>
                    </fieldset>
                    &nbsp; Base:
                    <fieldset>
                        <input type="color" name="recipebook_settings[accent_color_base]" value="<?php echo isset($options['accent_color_base']) ? esc_attr($options['accent_color_base']) : '#a0650dff'; ?>" />
                        <span style="margin-left:10px;">Preview: <span style="display:inline-block;width:24px;height:24px;background:<?php echo isset($options['accent_color_base']) ? esc_attr($options['accent_color_base']) : '#ff9800'; ?>;border:1px solid #ccc;"></span></span>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Custom CSS</th>
                <td>
                    <fieldset>
                        <pre id="editor" style="height: 500px; width: 100%" name="recipebook_settings[custom_css]"></pre>
<textarea name="recipebook_settings[custom_css]" style="display:none;"><?php 
    echo isset($options['custom_css']) ? esc_textarea($options['custom_css']) : ''; 
?></textarea>
                    </fieldset>
                </td>
            </tr>
        </table>
        <input type="submit" class="button button-primary" value="Update Settings" />
    </form>
        
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.6/ace.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.6/ace.js"></script>
    <script>
    jQuery(document).ready(function($){
        var editor = ace.edit("editor");
        editor.session.setMode("ace/mode/css");
        editor.setTheme("ace/theme/monokai");
        var textarea = $('textarea[name="recipebook_settings[custom_css]"]');
        editor.setValue(textarea.val(), -1); // -1 moves cursor to start
        editor.session.on('change', function(){
            textarea.val(editor.getValue());
        });
    });
    </script>
</div>