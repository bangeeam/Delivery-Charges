// Make Zip/Postcode optional in WooCommerce for Pakistan
add_filter('woocommerce_default_address_fields', 'custom_override_default_address_fields_optional');
function custom_override_default_address_fields_optional($address_fields) {
    // Make postcode optional
    $address_fields['postcode']['required'] = false;
    return $address_fields;
}

// Ensure delivery options still work without the postcode
add_filter('woocommerce_checkout_fields', 'make_postcode_optional', 10, 1);
function make_postcode_optional($fields) {
    // Set the postcode field to optional for both billing and shipping
    if (isset($fields['billing']['billing_postcode'])) {
        $fields['billing']['billing_postcode']['required'] = false;
    }
    if (isset($fields['shipping']['shipping_postcode'])) {
        $fields['shipping']['shipping_postcode']['required'] = false;
    }
    return $fields;
}

// Allow checkout even if postcode is empty
add_action('woocommerce_after_checkout_validation', 'allow_checkout_without_postcode', 10, 2);
function allow_checkout_without_postcode($data, $errors) {
    // If in Pakistan, don't validate postcode
    if ($data['billing_country'] == 'PK' || $data['shipping_country'] == 'PK') {
        unset($errors->errors['billing_postcode']);
        unset($errors->errors['shipping_postcode']);
    }
}
