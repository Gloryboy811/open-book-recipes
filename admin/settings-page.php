<?php $admin_css_path = dirname( __FILE__ ) . '/admin.css'; $admin_css_version = file_exists( $admin_css_path ) ? filemtime( $admin_css_path ) : '1.0'; ?>
<link href="<?php echo esc_url( recipebook_url . 'admin/admin.css?ver=' . $admin_css_version ); ?>" rel="stylesheet" type="text/css" />
<div class="wrap">
    <h1>Recipe Book Settings</h1>
    <form action="options.php" method="post">
        <?php settings_fields( 'recipebook_settings' ); ?>
        <?php $options = get_option( 'recipebook_settings', array() ); ?>
        <table class="form-table">
            <tr>
                <th scope="row">Recipe Layout</th>
                <td><fieldset><ul class="rb-image-select">
                    <li><label><input name="recipebook_settings[layout_theme]" type="radio" value="basic" <?php checked( isset( $options['layout_theme'] ) ? $options['layout_theme'] : 'basic', 'basic' ); ?> /><img src="<?php echo esc_url( recipebook_url . 'admin/images/theme-basic.svg' ); ?>" alt="Layout 1 preview" /><span>Layout 1</span></label></li>
                    <li><label><input name="recipebook_settings[layout_theme]" type="radio" value="modern" <?php checked( isset( $options['layout_theme'] ) ? $options['layout_theme'] : 'basic', 'modern' ); ?> /><img src="<?php echo esc_url( recipebook_url . 'admin/images/theme-modern.svg' ); ?>" alt="Modern layout preview" /><span>Modern</span></label></li>
                    <li><label><input name="recipebook_settings[layout_theme]" type="radio" value="cookbook" <?php checked( isset( $options['layout_theme'] ) ? $options['layout_theme'] : 'basic', 'cookbook' ); ?> /><img src="<?php echo esc_url( recipebook_url . 'admin/images/theme-cookbook.svg' ); ?>" alt="Editorial cookbook layout preview" /><span>Cookbook</span></label></li>
                </ul></fieldset></td>
            </tr>
            <tr>
                <th scope="row">Archive Card Style</th>
                <td><fieldset><ul class="rb-image-select">
                    <li><label><input name="recipebook_settings[archive_card_style]" type="radio" value="photo" <?php checked( isset( $options['archive_card_style'] ) ? $options['archive_card_style'] : 'photo', 'photo' ); ?> /><img src="<?php echo esc_url( recipebook_url . 'admin/images/archive-card-photo.svg' ); ?>" alt="Photo overlay card preview" /><span>Photo overlay</span></label></li>
                    <li><label><input name="recipebook_settings[archive_card_style]" type="radio" value="classic" <?php checked( isset( $options['archive_card_style'] ) ? $options['archive_card_style'] : 'photo', 'classic' ); ?> /><img src="<?php echo esc_url( recipebook_url . 'admin/images/archive-card-classic.svg' ); ?>" alt="Classic card preview" /><span>Classic</span></label></li>
                </ul></fieldset><p class="description">Choose how recipe cards appear on the recipe archive.</p></td>
            </tr>
            <tr>
                <th scope="row">Accent Colours</th>
                <td>
                    <p>Accent <input type="color" name="recipebook_settings[accent_color]" value="<?php echo esc_attr( isset( $options['accent_color'] ) ? $options['accent_color'] : '#ff9800' ); ?>" /></p>
                    <p>Base <input type="color" name="recipebook_settings[accent_color_base]" value="<?php echo esc_attr( isset( $options['accent_color_base'] ) ? $options['accent_color_base'] : '#a0650d' ); ?>" /></p>
                </td>
            </tr>
            <tr>
                <th scope="row">Custom CSS</th>
                <td><textarea name="recipebook_settings[custom_css]" rows="14" class="large-text code"><?php echo esc_textarea( isset( $options['custom_css'] ) ? $options['custom_css'] : '' ); ?></textarea></td>
            </tr>
        </table>
        <?php submit_button( 'Save Settings' ); ?>
    </form>
</div>