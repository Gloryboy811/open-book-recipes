<?php
/**
 * Modern, mobile-first recipe layout.
 *
 * @package OpenBookRecipes
 */

global $post;

$recipe_id = get_the_ID();
$css_path = plugin_dir_path( dirname( __FILE__ ) ) . 'styles/style-modern.css';
$css_version = file_exists( $css_path ) ? filemtime( $css_path ) : '1.0';
?>
<link rel="stylesheet" href="<?php echo esc_url( recipebook_url . 'styles/style-modern.css?ver=' . $css_version ); ?>" />
<article class="rb-modern">
    <?php
    $hero_image = get_the_post_thumbnail_url( $recipe_id, 'full' );
    $details = json_decode( (string) get_post_meta( $recipe_id, '_rb_recipe_details', true ) );
    $details = is_object( $details ) ? $details : null;
    $times = json_decode( (string) get_post_meta( $recipe_id, '_rb_recipe_times', true ) );
    $times = is_object( $times ) ? $times : null;
    $post_body = get_post_field( 'post_content', $recipe_id );
    $components_json = get_post_meta( $recipe_id, '_rb_recipe_components', false );
    $steps = get_post_meta( $recipe_id, '_rb_recipe_steps', false );
    $notes = get_post_meta( $recipe_id, '_rb_recipe_notes', true );
    $gallery = get_post_meta( $recipe_id, 'gallery_images', true );
    $nutrition_titles = get_post_meta( $recipe_id, '_rb_nut_titles', true );
    $nutrition_amounts = get_post_meta( $recipe_id, '_rb_nut_amounts', true );

    $format_time = static function( $hours, $minutes ) {
        $parts = array();
        if ( (int) $hours > 0 ) {
            $parts[] = sprintf( _n( '%d hr', '%d hrs', (int) $hours, 'recipebook' ), (int) $hours );
        }
        if ( (int) $minutes > 0 ) {
            $parts[] = sprintf( _n( '%d min', '%d mins', (int) $minutes, 'recipebook' ), (int) $minutes );
        }
        return $parts ? implode( ' ', $parts ) : '';
    };

    $meal_types = get_the_terms( $recipe_id, 'mealtime' );
    $meal_types = is_array( $meal_types ) ? $meal_types : array();
    $cuisines = get_the_terms( $recipe_id, 'cuisine' );
    $cuisines = is_array( $cuisines ) ? $cuisines : array();
    ?>

    <header class="rbm-hero<?php echo $hero_image ? ' has-image' : ' no-image'; ?>">
        <?php if ( $hero_image ) : ?>
            <img class="rbm-hero-image" src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( get_the_title( $recipe_id ) ); ?>" fetchpriority="high" />
        <?php endif; ?>
        <div class="rbm-hero-content">
            <p class="rbm-eyebrow"><?php esc_html_e( 'The recipe', 'recipebook' ); ?></p>
            <h1 class="rbm-title"><?php echo esc_html( get_the_title( $recipe_id ) ); ?></h1>
            <?php if ( has_excerpt( $recipe_id ) ) : ?>
                <p class="rbm-intro"><?php echo esc_html( get_the_excerpt( $recipe_id ) ); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <?php if ( $post_body ) : ?>
        <div class="rbm-description"><?php echo wp_kses_post( wpautop( do_blocks( $post_body ) ) ); ?></div>
    <?php endif; ?>

    <?php if ( $details || $meal_types || $cuisines ) : ?>
        <div class="rbm-meta">
            <?php if ( $details && ! empty( $details->serves ) ) : ?><span class="rbm-chip"><strong><?php esc_html_e( 'Serves', 'recipebook' ); ?></strong> <?php echo esc_html( $details->serves ); ?></span><?php endif; ?>
            <?php if ( $details && ! empty( $details->cals ) ) : ?><span class="rbm-chip"><strong><?php esc_html_e( 'Calories', 'recipebook' ); ?></strong> <?php echo esc_html( $details->cals ); ?></span><?php endif; ?>
            <?php if ( $details && ! empty( $details->difficulty ) ) : ?><span class="rbm-chip"><strong><?php esc_html_e( 'Difficulty', 'recipebook' ); ?></strong> <?php echo esc_html( ucfirst( $details->difficulty ) ); ?></span><?php endif; ?>
            <?php foreach ( array( $meal_types, $cuisines ) as $terms ) : ?>
                <?php if ( is_array( $terms ) ) : foreach ( $terms as $term ) : $term_url = get_term_link( $term ); ?>
                    <?php if ( ! is_wp_error( $term_url ) ) : ?><a class="rbm-chip rbm-chip-link" href="<?php echo esc_url( $term_url ); ?>"><?php echo esc_html( $term->name ); ?></a><?php endif; ?>
                <?php endforeach; endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ( $times ) :
        $prep_time = $format_time( $times->prepHrs ?? 0, $times->prepMins ?? 0 );
        $cook_time = $format_time( $times->cookHrs ?? 0, $times->cookMins ?? 0 );
        $total_time = $format_time( $times->totalHrs ?? 0, $times->totalMins ?? 0 );
        if ( $prep_time || $cook_time || $total_time ) : ?>
        <section class="rbm-time-grid" aria-label="<?php esc_attr_e( 'Recipe times', 'recipebook' ); ?>">
            <?php foreach ( array( 'Prep' => $prep_time, 'Cook' => $cook_time, 'Total' => $total_time ) as $label => $time ) : ?>
                <?php if ( $time ) : ?><div class="rbm-time-card"><span><?php echo esc_html( $label ); ?></span><strong><?php echo esc_html( $time ); ?></strong></div><?php endif; ?>
            <?php endforeach; ?>
        </section>
    <?php endif; endif; ?>

    <?php if ( $components_json ) : ?>
        <section class="rbm-section rbm-ingredients" aria-labelledby="rbm-ingredients-title">
            <div class="rbm-section-heading">
                <div><p class="rbm-eyebrow"><?php esc_html_e( 'What you need', 'recipebook' ); ?></p><h2 id="rbm-ingredients-title"><?php esc_html_e( 'Ingredients', 'recipebook' ); ?></h2></div>
                <div class="recipe-multiplier rbm-multiplier" aria-label="<?php esc_attr_e( 'Scale ingredients', 'recipebook' ); ?>">
                    <button type="button" class="rb-rm-btn" data-multi="0.5">½×</button>
                    <button type="button" class="rb-rm-btn -selected" data-multi="1">1×</button>
                    <button type="button" class="rb-rm-btn" data-multi="2">2×</button>
                    <button type="button" class="rb-rm-btn" data-multi="4">4×</button>
                </div>
            </div>
            <?php foreach ( $components_json as $index => $component_json ) :
                $component = json_decode( $component_json );
                if ( ! is_object( $component ) || empty( $component->ingredients ) || ! is_array( $component->ingredients ) ) { continue; }
            ?>
                <div class="rbm-ingredient-group<?php echo count( $components_json ) === 1 ? ' is-single' : ''; ?>">
                    <?php if ( count( $components_json ) > 1 && ! empty( $component->title ) ) : ?><h3><?php echo esc_html( $component->title ); ?></h3><?php endif; ?>
                    <ul class="rbm-ingredient-list">
                        <?php foreach ( $component->ingredients as $ingredient ) :
                            if ( ! is_object( $ingredient ) ) { continue; }
                            $amount = isset( $ingredient->amount ) ? (string) $ingredient->amount : '';
                        ?>
                            <li>
                                <span class="rbm-ingredient-amount"><b class="rb-rm-amount" data-amount="<?php echo esc_attr( $amount ); ?>"><?php echo esc_html( toFrac( $amount ) ); ?></b><?php if ( ! empty( $ingredient->denom ) ) : ?> <?php echo esc_html( $ingredient->denom ); ?><?php endif; ?></span>
                                <span class="rbm-ingredient-name"><?php echo esc_html( $ingredient->title ?? '' ); ?><?php if ( ! empty( $ingredient->extra ) ) : ?> <small><?php echo esc_html( $ingredient->extra ); ?></small><?php endif; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <?php if ( $steps ) : ?>
        <section class="rbm-section rbm-directions" aria-labelledby="rbm-directions-title">
            <p class="rbm-eyebrow"><?php esc_html_e( 'How to make it', 'recipebook' ); ?></p>
            <h2 id="rbm-directions-title"><?php esc_html_e( 'Method', 'recipebook' ); ?></h2>
            <ol class="rbm-step-list">
                <?php foreach ( $steps as $step ) : if ( trim( (string) $step ) === '' ) { continue; } ?>
                    <li><div><?php echo esc_html( $step ); ?></div></li>
                <?php endforeach; ?>
            </ol>
        </section>
    <?php endif; ?>

    <?php if ( $notes ) : ?>
        <aside class="rbm-notes"><p class="rbm-eyebrow"><?php esc_html_e( 'A little note', 'recipebook' ); ?></p><?php echo esc_html( $notes ); ?></aside>
    <?php endif; ?>

    <?php if ( is_array( $nutrition_titles ) && $nutrition_titles ) : ?>
        <section class="rbm-section rbm-nutrition" aria-labelledby="rbm-nutrition-title">
            <p class="rbm-eyebrow"><?php esc_html_e( 'Per serving', 'recipebook' ); ?></p>
            <h2 id="rbm-nutrition-title"><?php esc_html_e( 'Nutrition', 'recipebook' ); ?></h2>
            <div class="rbm-table-scroll"><table>
                <thead><tr><?php foreach ( $nutrition_titles as $title ) : ?><th scope="col"><?php echo esc_html( $title ); ?></th><?php endforeach; ?></tr></thead>
                <tbody><tr><?php foreach ( $nutrition_titles as $index => $title ) : ?><td><?php echo esc_html( is_array( $nutrition_amounts ) && isset( $nutrition_amounts[$index] ) ? $nutrition_amounts[$index] : '' ); ?></td><?php endforeach; ?></tr></tbody>
            </table></div>
        </section>
    <?php endif; ?>

    <?php if ( is_array( $gallery ) && $gallery ) : ?>
        <section class="rbm-section rbm-gallery" aria-label="<?php esc_attr_e( 'Recipe gallery', 'recipebook' ); ?>">
            <?php foreach ( $gallery as $attachment_id ) :
                $image = wp_get_attachment_image_src( absint( $attachment_id ), 'large' );
                if ( ! $image ) { continue; }
            ?>
                <a href="<?php echo esc_url( wp_get_attachment_image_url( absint( $attachment_id ), 'full' ) ); ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php echo esc_attr( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ); ?>" loading="lazy" />
                </a>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</article>
