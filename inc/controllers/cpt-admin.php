<?php 

function ig_sort_partner_name( $query ){
    global $pagenow;
    
    if( is_admin() && 'edit.php' == $pagenow && !isset( $_GET['orderby'] ) ):
        if( $_GET['post_type'] == 'partner' && !isset( $_GET['post_status'] ) ):

            $query->set( 'orderby', 'title' );
            $query->set( 'order', 'ASC' );
        endif;
            
    endif;
}
add_action( 'pre_get_posts', 'ig_sort_partner_name' );