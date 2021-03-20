# Plugin Options

## Initialization

```php
add_action(
	'sixa_options_fieldset',
	function( $slug ) {
		$options = get_option( Options::$key, array() );

		Options::add_field(
			'sixa_options_text',
			__( 'Text field', '@@textdomain' ),
			function() use ( $options ) {
				Options::text_field(
					array(
						'id'    => 'sixa_options_text',
						'name'  => sprintf( '%s[text-input]', Options::$key ),
						'value' => isset( $options['text-input'] ) ? $options['text-input'] : '',
					)
				);
			}
		);
	}
);
```