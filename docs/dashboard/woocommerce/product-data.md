# Product Data

## Initialization

```php
add_action(
	'woocommerce_init',
	function() {
		add_action(
			'add_meta_boxes_product',
			function() {
				global $post;

				$options = (array) get_post_meta( $post->ID, Product_Data::$key, true );
				new Product_Data(
					array(
						'data' => array(
							'label'  => __( 'Custom Panel', '@@textdomain' ),
							'class'  => 'show_if_simple',
							'fields' => array(
								Options::text_field(
									array(
										'id'    => 'sixa_product_data_text',
										'label' => __( 'Text field', '@@textdomain' ),
										'name'  => sprintf( '%s[text-input]', Product_Data::$key ),
										'value' => isset( $options['text-input'] ) ? $options['text-input'] : '',
									),
									false
								),
							),
						),
					),
				);
			}
		);

		// Save and sanitize the submitted data.
		add_action( 'woocommerce_admin_process_product_object', array( 'SixaSnippets\Dashboard\WooCommerce\Product_Data', 'save' ) );
	}
);
```