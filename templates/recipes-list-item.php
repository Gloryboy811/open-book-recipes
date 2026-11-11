<?php
/**
 * Recipe archive card.
 *
 * @package OpenBookRecipes
 */
$recipe_id = get_the_ID();
$details = json_decode( (string) get_post_meta( $recipe_id, '_rb_recipe_details', true ) );
$options = get_option( 'recipebook_settings', array() );
$card_style = isset( $options['archive_card_style'] ) ? sanitize_key( $options['archive_card_style'] ) : 'photo';
$card_style = in_array( $card_style, array( 'photo', 'classic' ), true ) ? $card_style : 'photo';
$thumbnail = get_the_post_thumbnail_url( $recipe_id, 'large' );
if ( ! $thumbnail ) { $thumbnail = recipebook_url . 'images/recipe-placeholder.svg'; }
?>
<article class="rb-recipe-card rb-recipe-card--<?php echo esc_attr( $card_style ); ?>">
    <a class="rb-recipe-card__link" href="<?php the_permalink(); ?>">
        <div class="rb-recipe-card__image">
            <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" />
            <?php if ( 'photo' === $card_style ) : ?>
                <span class="rb-recipe-card__image-shade"></span>
                <h2 class="rb-recipe-card__title"><?php the_title(); ?></h2>
            <?php endif; ?>
        </div>
        <div class="rb-recipe-card__content">
            <?php if ( 'classic' === $card_style ) : ?><h2 class="rb-recipe-card__title"><?php the_title(); ?></h2><?php endif; ?>
            <?php $excerpt = get_the_excerpt(); ?>
            <?php if ( $excerpt ) : ?><p class="rb-recipe-card__excerpt"><?php echo esc_html( wp_trim_words( $excerpt, 18 ) ); ?></p><?php endif; ?>
            <ul class="rb-recipe-card__meta">
                <?php if ( is_object( $details ) && ! empty( $details->cals ) ) : ?><li><?php echo esc_html( $details->cals ); ?> kcal</li><?php endif; ?>
                <?php if ( is_object( $details ) && ! empty( $details->serves ) ) : ?><li><?php echo esc_html( $details->serves ); ?> servings</li><?php endif; ?>
                <?php if ( is_object( $details ) && ! empty( $details->difficulty ) ) : ?><li><?php echo esc_html( ucfirst( $details->difficulty ) ); ?></li><?php endif; ?>
            </ul>
        </div>
    </a>
</article>
