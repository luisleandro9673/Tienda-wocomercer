<?php
/**
 * BigBang Theme - functions.php
 * Tema hijo de Storefront optimizado para BigBang Wizard plugin
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BBT_VERSION', '1.0.0' );
define( 'BBT_URI', get_stylesheet_directory_uri() );
define( 'BBT_PATH', get_stylesheet_directory() );

/* ============================================================
   1. ENQUEUE STYLES & SCRIPTS
============================================================ */
add_action( 'wp_enqueue_scripts', 'bbt_enqueue_assets', 20 );
function bbt_enqueue_assets() {
    // Google Fonts
    wp_enqueue_style( 'bbt-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap', [], null );

    // Bootstrap Icons
    wp_enqueue_style( 'bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', [], '1.11.3' );

    // Theme CSS
    wp_enqueue_style( 'bbt-main', BBT_URI . '/assets/css/main.css', [ 'bbt-fonts' ], BBT_VERSION );

    // Theme JS
    wp_enqueue_script( 'bbt-main', BBT_URI . '/assets/js/main.js', [], BBT_VERSION, true );
}

/* ============================================================
   2. THEME SUPPORT
============================================================ */
add_action( 'after_setup_theme', 'bbt_setup' );
function bbt_setup() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ] );
}

/* ============================================================
   3. CUSTOMIZER: Colores del tema
============================================================ */
add_action( 'customize_register', 'bbt_customizer' );
function bbt_customizer( $wp_customize ) {
    $wp_customize->add_section( 'bbt_colors', [
        'title'    => 'BigBang Colores',
        'priority' => 30,
    ]);

    $colors = [
        'bbt_color_bg'     => [ 'Background', '#F8F9FB' ],
        'bbt_color_card'   => [ 'Cards', '#FFFFFF' ],
        'bbt_color_text'   => [ 'Texto', '#1E293B' ],
        'bbt_color_gray'   => [ 'Texto gris', '#64748B' ],
        'bbt_color_accent' => [ 'Acento', '#9F40E4' ],
        'bbt_color_accent2'=> [ 'Acento 2', '#FF3366' ],
        'bbt_color_brand'  => [ 'Marca', '#9D1F38' ],
    ];

    foreach ( $colors as $id => $c ) {
        $wp_customize->add_setting( $id, [
            'default'   => $c[1],
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ]);
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, [
            'label'   => $c[0],
            'section' => 'bbt_colors',
        ]));
    }
}

/* Output CSS variables */
add_action( 'wp_head', 'bbt_custom_css', 100 );
function bbt_custom_css() {
    $bg      = get_theme_mod( 'bbt_color_bg', '#F8F9FB' );
    $card    = get_theme_mod( 'bbt_color_card', '#FFFFFF' );
    $text    = get_theme_mod( 'bbt_color_text', '#1E293B' );
    $gray    = get_theme_mod( 'bbt_color_gray', '#64748B' );
    $accent  = get_theme_mod( 'bbt_color_accent', '#9F40E4' );
    $accent2 = get_theme_mod( 'bbt_color_accent2', '#FF3366' );
    $brand   = get_theme_mod( 'bbt_color_brand', '#9D1F38' );
    ?>
    <style id="bbt-dynamic-css">
        :root {
            --bbt-bg: <?php echo $bg; ?>;
            --bbt-card: <?php echo $card; ?>;
            --bbt-text: <?php echo $text; ?>;
            --bbt-gray: <?php echo $gray; ?>;
            --bbt-accent: <?php echo $accent; ?>;
            --bbt-accent2: <?php echo $accent2; ?>;
            --bbt-brand: <?php echo $brand; ?>;
            --bbt-gradient: linear-gradient(90deg, <?php echo $accent2; ?> 0%, <?php echo $accent; ?> 100%);
            --bbt-shadow-float: 0 15px 30px -5px <?php echo $accent; ?>66;

            /* Sync con plugin */
            --bbw-gradient: var(--bbt-gradient);
            --bbw-accent: <?php echo $accent; ?>;
            --bbw-bg: <?php echo $bg; ?>;
            --bbw-card: <?php echo $card; ?>;
            --bbw-text: <?php echo $text; ?>;
            --bbw-gray: <?php echo $gray; ?>;
            --bbw-shadow-float: var(--bbt-shadow-float);
        }
    </style>
    <?php
}

/* ============================================================
   4. REMOVE STOREFRONT DEFAULTS QUE NO QUEREMOS
============================================================ */
add_action( 'init', 'bbt_remove_storefront_stuff' );
function bbt_remove_storefront_stuff() {
    // Removemos sidebar para shop
    remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
}

/* ============================================================
   5. WOOCOMMERCE: Override shop loop
============================================================ */

// Full width shop
add_filter( 'body_class', function( $classes ) {
    if ( is_shop() || is_product_category() || is_front_page() ) {
        $classes[] = 'bbt-fullwidth-shop';
    }
    return $classes;
});

// Cambiar columns
add_filter( 'loop_shop_columns', function(){ return 4; } );

// Override product card template
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

// Custom card output
add_action( 'woocommerce_before_shop_loop_item', 'bbt_card_open', 5 );
add_action( 'woocommerce_after_shop_loop_item', 'bbt_card_close', 50 );

function bbt_card_open() {
    global $product;
    $pid = $product->get_id();
    $in_stock = $product->is_in_stock();
    $sale = $product->is_on_sale();
    $is_combo = $product->get_meta('_bbw_is_combo') === 'yes';

    $regular = (float) $product->get_regular_price();
    $sale_price = $product->get_sale_price() ? (float) $product->get_sale_price() : 0;
    $display_price = $sale_price ?: $regular;
    $pct_off = $sale && $regular > 0 ? round( (($regular - $sale_price) / $regular) * 100 ) : 0;

    $img_id = $product->get_image_id();
    $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src();

    $class = 'bbw-product-card' . (!$in_stock ? ' bbw-agotado' : '');
    ?>
    <div class="<?php echo esc_attr($class); ?>" data-bbw-pid="<?php echo $pid; ?>">
        <div class="bbw-img-container">
            <?php if ( $sale && $in_stock && $pct_off > 0 ) : ?>
                <div class="bbw-badge-oferta">
                    <span class="bbw-flash">⚡</span>
                    AHORRÁ <?php echo $pct_off; ?>%
                </div>
            <?php endif; ?>

            <?php if ( $is_combo ) : ?>
                <div class="bbw-badge-combo">★ COMBO</div>
            <?php endif; ?>

            <?php if ( ! $in_stock ) : ?>
                <div class="bbw-agotado-overlay"><span class="bbw-agotado-text">AGOTADO</span></div>
            <?php endif; ?>

            <img src="<?php echo esc_url($img_url); ?>" class="bbw-img-producto" loading="lazy" alt="<?php echo esc_attr($product->get_name()); ?>">
        </div>

        <div class="bbw-card-body">
            <h3 class="bbw-titulo-producto"><?php echo esc_html($product->get_name()); ?></h3>
            <p class="bbw-desc-producto"><?php echo esc_html($product->get_short_description()); ?></p>

            <div class="bbw-card-actions">
                <div class="bbw-price-block">
                    <?php if ( $sale ) : ?>
                        <span class="bbw-price-old">$<?php echo number_format($regular, 0, ',', '.'); ?></span>
                        <span class="bbw-price-current is-oferta">$<?php echo number_format($display_price, 0, ',', '.'); ?></span>
                    <?php else : ?>
                        <span class="bbw-price-current">$<?php echo number_format($display_price, 0, ',', '.'); ?></span>
                    <?php endif; ?>
                </div>
                <button class="bbw-btn-add <?php echo !$in_stock ? 'disabled' : ''; ?>">
                    <?php echo $in_stock ? '+' : '✕'; ?>
                </button>
            </div>
        </div>
    <?php
}

function bbt_card_close() {
    echo '</div>';
}

/* ============================================================
   6. BANNERS EN HOMEPAGE
============================================================ */
add_action( 'woocommerce_before_shop_loop', 'bbt_show_banners', 8 );
function bbt_show_banners() {
    if ( ! function_exists( 'BBW_DB' ) && ! class_exists( 'BBW_DB' ) ) return;
    echo do_shortcode( '[bbw_banners]' );
}

/* ============================================================
   7. CATEGORY SECTIONS (en vez de paginación estándar)
============================================================ */
add_action( 'woocommerce_after_shop_loop', 'bbt_category_sections', 5 );
function bbt_category_sections() {
    // El JS del plugin maneja las secciones por categoría
}

/* ============================================================
   8. HEADER PERSONALIZADO
============================================================ */
// El tema hereda el header de Storefront pero le agrega clases
add_filter( 'storefront_header_class', function( $class ) {
    return $class . ' bbt-header-glass';
});
