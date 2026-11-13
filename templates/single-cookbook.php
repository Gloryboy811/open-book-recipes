<?php
/**
 * Editorial cookbook layout with bold type, warm paper colours and photo-led sections.
 *
 * @package OpenBookRecipes
 */

$recipe_id = get_the_ID();
$css_path = plugin_dir_path( dirname( __FILE__ ) ) . 'styles/style-cookbook.css';
$css_version = file_exists( $css_path ) ? filemtime( $css_path ) : '1.0';
$hero_image = get_the_post_thumbnail_url( $recipe_id, 'full' );
$details = json_decode( (string) get_post_meta( $recipe_id, '_rb_recipe_details', true ) );
$details = is_object( $details ) ? $details : null;
$times = json_decode( (string) get_post_meta( $recipe_id, '_rb_recipe_times', true ) );
$times = is_object( $times ) ? $times : null;
$description = get_post_field( 'post_content', $recipe_id );
$components = get_post_meta( $recipe_id, '_rb_recipe_components', false );
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
    return implode( ' ', $parts );
};
$format_duration = static function( $hours, $minutes ) {
    $duration = 'PT';
    if ( (int) $hours > 0 ) { $duration .= (int) $hours . 'H'; }
    if ( (int) $minutes > 0 ) { $duration .= (int) $minutes . 'M'; }
    return $duration === 'PT' ? '' : $duration;
};
$prep_time = $times ? $format_time( $times->prepHrs ?? 0, $times->prepMins ?? 0 ) : '';
$cook_time = $times ? $format_time( $times->cookHrs ?? 0, $times->cookMins ?? 0 ) : '';
$total_time = $times ? $format_time( $times->totalHrs ?? 0, $times->totalMins ?? 0 ) : '';
$meal_types = get_the_terms( $recipe_id, 'mealtime' );
$cuisines = get_the_terms( $recipe_id, 'cuisine' );
$meal_types = is_array( $meal_types ) ? $meal_types : array();
$cuisines = is_array( $cuisines ) ? $cuisines : array();
?>
<link rel="stylesheet" href="<?php echo esc_url( recipebook_url . 'styles/style-cookbook.css?ver=' . $css_version ); ?>" />
<article class="rb-cookbook" itemscope itemtype="https://schema.org/Recipe" aria-labelledby="rbc-title">
    <header class="rbc-cover">
        <div class="rbc-cover-copy">
            <p class="rbc-kicker"><?php esc_html_e( 'From our kitchen', 'recipebook' ); ?></p>
            <h1 id="rbc-title" class="rbc-title" itemprop="name"><?php echo esc_html( get_the_title( $recipe_id ) ); ?></h1>
            <?php if ( has_excerpt( $recipe_id ) ) : ?>
                <p class="rbc-deck" itemprop="description"><?php echo esc_html( get_the_excerpt( $recipe_id ) ); ?></p>
            <?php endif; ?>
            <span class="rbc-stamp"><?php esc_html_e( 'Good food. No fuss.', 'recipebook' ); ?></span>
        </div>
        <?php if ( $hero_image ) : ?>
            <figure class="rbc-cover-image">
                <img src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( get_the_title( $recipe_id ) ); ?>" itemprop="image" fetchpriority="high" />
            </figure>
        <?php endif; ?>
    </header>

    <?php if ( $description ) : ?>
        <div class="rbc-intro" itemprop="articleBody"><?php echo wp_kses_post( wpautop( do_blocks( $description ) ) ); ?></div>
    <?php endif; ?>

    <section class="rbc-facts" aria-label="<?php esc_attr_e( 'Recipe details', 'recipebook' ); ?>">
        <?php if ( $details && ! empty( $details->serves ) ) : ?><div><span><?php esc_html_e( 'Serves', 'recipebook' ); ?></span><strong itemprop="recipeYield"><?php echo esc_html( $details->serves ); ?></strong></div><?php endif; ?>
        <?php if ( $prep_time ) : ?><div><span><?php esc_html_e( 'Prep', 'recipebook' ); ?></span><strong><time itemprop="prepTime" datetime="<?php echo esc_attr( $format_duration( $times->prepHrs ?? 0, $times->prepMins ?? 0 ) ); ?>"><?php echo esc_html( $prep_time ); ?></time></strong></div><?php endif; ?>
        <?php if ( $cook_time ) : ?><div><span><?php esc_html_e( 'Cook', 'recipebook' ); ?></span><strong><time itemprop="cookTime" datetime="<?php echo esc_attr( $format_duration( $times->cookHrs ?? 0, $times->cookMins ?? 0 ) ); ?>"><?php echo esc_html( $cook_time ); ?></time></strong></div><?php endif; ?>
        <?php if ( $total_time ) : ?><div><span><?php esc_html_e( 'Total', 'recipebook' ); ?></span><strong><time itemprop="totalTime" datetime="<?php echo esc_attr( $format_duration( $times->totalHrs ?? 0, $times->totalMins ?? 0 ) ); ?>"><?php echo esc_html( $total_time ); ?></time></strong></div><?php endif; ?>
        <?php if ( $details && ! empty( $details->difficulty ) ) : ?><div><span><?php esc_html_e( 'Level', 'recipebook' ); ?></span><strong><?php echo esc_html( ucfirst( $details->difficulty ) ); ?></strong></div><?php endif; ?>
    </section>

    <?php if ( $meal_types || $cuisines ) : ?>
        <p class="rbc-tags">
            <?php foreach ( array_merge( $meal_types, $cuisines ) as $term ) : ?>
                <?php $term_url = get_term_link( $term ); if ( ! is_wp_error( $term_url ) ) : ?>
                    <a href="<?php echo esc_url( $term_url ); ?>"><?php echo esc_html( $term->name ); ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </p>
    <?php endif; ?>

    <?php if ( $components ) : ?>
        <section class="rbc-section rbc-ingredients" aria-labelledby="rbc-ingredients-title">
            <div class="rbc-section-heading">
                <p class="rbc-kicker"><?php esc_html_e( 'Get everything ready', 'recipebook' ); ?></p>
                <h2 id="rbc-ingredients-title"><?php esc_html_e( 'What you need', 'recipebook' ); ?></h2>
                <div class="recipe-multiplier rbc-multiplier" aria-label="<?php esc_attr_e( 'Scale ingredients', 'recipebook' ); ?>">
                    <button type="button" class="rb-rm-btn" data-multi="0.5">½×</button>
                    <button type="button" class="rb-rm-btn -selected" data-multi="1">1×</button>
                    <button type="button" class="rb-rm-btn" data-multi="2">2×</button>
                    <button type="button" class="rb-rm-btn" data-multi="4">4×</button>
                </div>
            </div>
            <?php foreach ( $components as $index => $component_json ) :
                $component = json_decode( $component_json );
                if ( ! is_object( $component ) || empty( $component->ingredients ) || ! is_array( $component->ingredients ) ) { continue; }
            ?>
                <div class="rbc-ingredient-group">
                    <?php if ( count( $components ) > 1 && ! empty( $component->title ) ) : ?><h3><?php echo esc_html( $component->title ); ?></h3><?php endif; ?>
                    <ul>
                        <?php foreach ( $component->ingredients as $ingredient ) :
                            if ( ! is_object( $ingredient ) ) { continue; }
                            $amount = isset( $ingredient->amount ) ? (string) $ingredient->amount : '';
                        ?>
                            <li>
                                <span class="rbc-amount"><b class="rb-rm-amount" data-amount="<?php echo esc_attr( $amount ); ?>"><?php echo esc_html( toFrac( $amount ) ); ?></b><?php if ( ! empty( $ingredient->denom ) ) : ?> <?php echo esc_html( $ingredient->denom ); ?><?php endif; ?></span>
                                <span><?php echo esc_html( $ingredient->title ?? '' ); ?><?php if ( ! empty( $ingredient->extra ) ) : ?> <small><?php echo esc_html( $ingredient->extra ); ?></small><?php endif; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <?php if ( $steps ) : ?>
        <section class="rbc-section rbc-method" aria-labelledby="rbc-method-title">
            <div class="rbc-section-heading">
                <p class="rbc-kicker"><?php esc_html_e( 'Now make some magic', 'recipebook' ); ?></p>
                <h2 id="rbc-method-title"><?php esc_html_e( 'Let’s cook', 'recipebook' ); ?></h2>
            </div>
            <ol>
                <?php foreach ( $steps as $step ) : if ( trim( (string) $step ) === '' ) { continue; } ?>
                    <li itemprop="recipeInstructions"><?php echo esc_html( $step ); ?></li>
                <?php endforeach; ?>
            </ol>
        </section>
    <?php endif; ?>

    <?php if ( $notes ) : ?>
        <aside class="rbc-note"><span class="rbc-kicker"><?php esc_html_e( 'A little tip', 'recipebook' ); ?></span><p><?php echo esc_html( $notes ); ?></p></aside>
    <?php endif; ?>

    <?php if ( is_array( $nutrition_titles ) && $nutrition_titles ) : ?>
        <section class="rbc-section rbc-nutrition" aria-labelledby="rbc-nutrition-title">
            <p class="rbc-kicker"><?php esc_html_e( 'The good stuff', 'recipebook' ); ?></p>
            <h2 id="rbc-nutrition-title"><?php esc_html_e( 'Nutrition per serving', 'recipebook' ); ?></h2>
            <div class="rbc-table-scroll"><table>
                <thead><tr><?php foreach ( $nutrition_titles as $title ) : ?><th scope="col"><?php echo esc_html( $title ); ?></th><?php endforeach; ?></tr></thead>
                <tbody><tr><?php foreach ( $nutrition_titles as $index => $title ) : ?><td><?php echo esc_html( is_array( $nutrition_amounts ) && isset( $nutrition_amounts[$index] ) ? $nutrition_amounts[$index] : '' ); ?></td><?php endforeach; ?></tr></tbody>
            </table></div>
        </section>
    <?php endif; ?>

    <?php if ( is_array( $gallery ) && $gallery ) : ?>
        <section class="rbc-gallery" aria-label="<?php esc_attr_e( 'Recipe photos', 'recipebook' ); ?>">
            <?php foreach ( $gallery as $attachment_id ) :
                $image = wp_get_attachment_image_src( absint( $attachment_id ), 'large' );
                if ( ! $image ) { continue; }
            ?>
                <img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php echo esc_attr( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ); ?>" loading="lazy" />
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</article>
