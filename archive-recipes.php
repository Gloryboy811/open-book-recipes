<?php
/**
 * Recipe archive with search and taxonomy/meta filters.
 *
 * @package OpenBookRecipes
 */

get_header();
$archive_url = get_post_type_archive_link( 'recipes' );
$search_value = isset( $_GET['recipe_search'] ) ? sanitize_text_field( wp_unslash( $_GET['recipe_search'] ) ) : '';
$selected_meal = isset( $_GET['meal_type'] ) ? sanitize_title( wp_unslash( $_GET['meal_type'] ) ) : '';
$selected_cuisine = isset( $_GET['cuisine'] ) ? sanitize_title( wp_unslash( $_GET['cuisine'] ) ) : '';
$selected_difficulty = isset( $_GET['difficulty'] ) ? sanitize_text_field( wp_unslash( $_GET['difficulty'] ) ) : '';

$meal_terms = get_terms( array( 'taxonomy' => 'mealtime', 'hide_empty' => true ) );
$cuisine_terms = get_terms( array( 'taxonomy' => 'cuisine', 'hide_empty' => true ) );
$meal_terms = is_array( $meal_terms ) ? $meal_terms : array();
$cuisine_terms = is_array( $cuisine_terms ) ? $cuisine_terms : array();

// Difficulty is stored inside the existing _rb_recipe_details JSON meta.
$difficulties = get_transient( 'recipebook_archive_difficulties' );
if ( false === $difficulties ) {
    $difficulties = array();
    $recipe_ids = get_posts( array(
        'post_type'              => 'recipes',
        'post_status'            => 'publish',
        'numberposts'            => -1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
    ) );
    foreach ( $recipe_ids as $recipe_id ) {
        $details = json_decode( (string) get_post_meta( $recipe_id, '_rb_recipe_details', true ), true );
        if ( is_array( $details ) && ! empty( $details['difficulty'] ) ) {
            $difficulty = trim( sanitize_text_field( $details['difficulty'] ) );
            if ( $difficulty !== '' ) { $difficulties[ strtolower( $difficulty ) ] = $difficulty; }
        }
    }
    natcasesort( $difficulties );
    set_transient( 'recipebook_archive_difficulties', $difficulties, 12 * HOUR_IN_SECONDS );
}
?>
<link href="<?php echo esc_url( recipebook_url . 'styles/style-archive.css?ver=' . ( file_exists( plugin_dir_path( __FILE__ ) . 'styles/style-archive.css' ) ? filemtime( plugin_dir_path( __FILE__ ) . 'styles/style-archive.css' ) : '1' ) ); ?>" rel="stylesheet" type="text/css" />
<header class="archive-header has-text-align-center header-footer-group">
    <div class="archive-header-inner section-inner medium">
        <h1 class="archive-title"><span class="color-accent"><?php esc_html_e( 'Recipes', 'recipebook' ); ?></span></h1>
    </div>
</header>

<div class="container rb-archive-container">
    <form class="rb-archive-filters" method="get" action="<?php echo esc_url( $archive_url ); ?>" role="search" aria-label="<?php esc_attr_e( 'Search and filter recipes', 'recipebook' ); ?>">
        <div class="rb-archive-search">
            <label class="screen-reader-text" for="rb-recipe-search"><?php esc_html_e( 'Search recipes', 'recipebook' ); ?></label>
            <input id="rb-recipe-search" type="search" name="recipe_search" value="<?php echo esc_attr( $search_value ); ?>" placeholder="<?php esc_attr_e( 'Search recipes, ingredients, or keywords…', 'recipebook' ); ?>" />
            <button type="submit"><?php esc_html_e( 'Search', 'recipebook' ); ?></button>
        </div>

        <div class="rb-archive-filter-fields">
            <div class="rb-archive-filter">
                <label for="rb-meal-type"><?php esc_html_e( 'Meal type', 'recipebook' ); ?></label>
                <select id="rb-meal-type" name="meal_type">
                    <option value=""><?php esc_html_e( 'All meal types', 'recipebook' ); ?></option>
                    <?php foreach ( $meal_terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected_meal, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="rb-archive-filter">
                <label for="rb-cuisine"><?php esc_html_e( 'Cuisine', 'recipebook' ); ?></label>
                <select id="rb-cuisine" name="cuisine">
                    <option value=""><?php esc_html_e( 'All cuisines', 'recipebook' ); ?></option>
                    <?php foreach ( $cuisine_terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected_cuisine, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ( $difficulties ) : ?>
                <div class="rb-archive-filter">
                    <label for="rb-difficulty"><?php esc_html_e( 'Difficulty', 'recipebook' ); ?></label>
                    <select id="rb-difficulty" name="difficulty">
                        <option value=""><?php esc_html_e( 'Any difficulty', 'recipebook' ); ?></option>
                        <?php foreach ( $difficulties as $difficulty ) : ?>
                            <option value="<?php echo esc_attr( $difficulty ); ?>" <?php selected( strtolower( $selected_difficulty ), strtolower( $difficulty ) ); ?>><?php echo esc_html( ucfirst( $difficulty ) ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="rb-archive-filter-actions">
                <button type="submit" class="rb-filter-submit"><?php esc_html_e( 'Apply filters', 'recipebook' ); ?></button>
                <a href="<?php echo esc_url( $archive_url ); ?>" class="rb-filter-reset"><?php esc_html_e( 'Clear all', 'recipebook' ); ?></a>
            </div>
        </div>
    </form>

    <?php
    global $wp_query;
    $result_count = (int) $wp_query->found_posts;
    ?>
    <p class="rb-archive-result-count" aria-live="polite">
        <?php
        printf(
            esc_html( _n( '%s recipe found', '%s recipes found', $result_count, 'recipebook' ) ),
            esc_html( number_format_i18n( $result_count ) )
        );
        ?>
    </p>

    <main id="main" class="rb-recipe-list" role="main">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <?php include plugin_dir_path( __FILE__ ) . 'templates/recipes-list-item.php'; ?>
            <?php endwhile; ?>
            <?php
            the_posts_pagination( array(
                'mid_size'  => 1,
                'prev_text' => __( 'Previous', 'recipebook' ),
                'next_text' => __( 'Next', 'recipebook' ),
            ) );
            ?>
        <?php else : ?>
            <div class="rb-archive-empty">
                <h2><?php esc_html_e( 'No recipes found', 'recipebook' ); ?></h2>
                <p><?php esc_html_e( 'Try a different search or remove one of the filters.', 'recipebook' ); ?></p>
                <a href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'Show all recipes', 'recipebook' ); ?></a>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php get_footer(); ?>
