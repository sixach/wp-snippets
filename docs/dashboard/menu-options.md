# Menu Options

## Initialization

```php
add_action(
    'admin_init',
    function() {
        new Menu_Options(
            array(
                array(
                    'default'     => 'no',
                    'type'        => 'checkbox',
                    'name'        => 'checkbox-choice', 
                    'label'       => __( 'Do a thing?', '@@textdomain' ),
                    'description' => __( 'Enable to do something', '@@textdomain' ),
                ),
            )
        );
    }
);
```