<?php
// Register and load the widget
function wpb_load_widget() {
    register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );

// Creating the widget 
class wpb_widget extends WC_Widget {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->widget_cssclass    = 'woocommerce widget_product_categories';
        $this->widget_description = __( "Filter by recently purchased", 'woocommerce' );
        $this->widget_id          = 'woocommerce_recently_purchased_filter_products';
        $this->widget_name        = __( 'Filter Recently Purchased', 'woocommerce' );
        $this->settings           = array(
            'title'  => array(
                'type'  => 'text',
                'std'   => __( 'Filter Recently Purchased ', 'woocommerce' ),
                'label' => __( 'Title', 'woocommerce' ),
            )
        );

        parent::__construct();
    }

// Creating widget front-end

public function widget( $args, $instance ) {
     $current_user = wp_get_current_user();
            if ( 0 == $current_user->ID ) return;

    $title = apply_filters( 'widget_title', $instance['title'] );
    //$instance['number'] = 10;
        $this->widget_start( $args, $instance );
       
            echo '<ul class="product-categories">';
            echo '<li class="cat-item "><a href="'.get_permalink( wc_get_page_id( 'shop' ) ).'?recently_purchased=1">View products purchased</a></li>';
            echo '</ul>';
            
		$this->widget_end( $args );

    }
    


    
// Updating widget replacing old instances with new

public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    return $instance;
    }
} 
