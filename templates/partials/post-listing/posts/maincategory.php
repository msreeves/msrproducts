    <div class="col-xl-3 col-lg-3">
    <div class="post panel">  
        <div class="listing-image">
            	  <?php get_template_part( 'templates/partials/featured-image' ); ?>
            </div>
            <div class="listing-text">
                                    <p class="category"> <?php $post_id = get_the_ID(); // You can replace this with a specific ID, e.g., 123
$taxonomy = 'product_cat'; // Replace with 'post_tag' or your custom taxonomy
$categories = get_the_terms( $post_id, $taxonomy ); 
if( $categories ){
    $output = "";

    //display all the top-level categories first
    foreach ($categories as $category) {
        if( !$category->parent ){
            $output .= '<a href="' . esc_url( get_term_link( $category->term_id ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ><span>' . $category->name.'</span></a>';
        }
    }

    //now, display all the child categories
    foreach ($categories as $category) {
        if( $category->parent ){
            $output .= '<a href="' . esc_url( get_term_link( $category->term_id ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '" ><span>' . $category->name.'</span></a>';
        }
    }

    echo trim( $output, "," );
}
?></p>
                <?php
global $product;

if ( ! is_a( $product, 'WC_Product' ) ) {
    $product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : false;
}

if ( is_a( $product, 'WC_Product' ) ) {
    if ( $product->is_on_sale() ) {
        echo '<span> SALE ' . wc_price( $product->get_sale_price() ) . '</span>';
    } else {
        echo wc_price( $product->get_regular_price() );
    }
}
?>
                  <h3><?php the_title() ?></h3> 
                      <a href="<?php echo the_permalink(); ?>"><button>Find out more</button></a>
                    </div>
                </div>
                    </div>