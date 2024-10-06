<?php
// Add custom shipping fee based on location
add_action('woocommerce_cart_calculate_fees', 'custom_location_based_shipping', 20, 1);
function custom_location_based_shipping($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    $shipping_cost = 0;
    $user = wp_get_current_user();
    $shipping_state = WC()->customer->get_shipping_state();
    $shipping_city = WC()->customer->get_shipping_city();

    // Define city arrays
    $karachi_districts = ['DistrictCentral', 'DistrictEast', 'DistrictWest', 'DistrictNorth', 'DistrictSouth'];
    $major_cities = ['Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Hyderabad', 'Sukkur', 'Nawab Shah', 
                     'Mirpurkhas', 'Thatta', 'Multan', 'Sahiwal', 'Kasur', 'Okara', 'Mian Channu', 'Gujarat', 
                     'Bhawalpur', 'Jehlum', 'Sargodha', 'Attock', 'Wazirabad', 'Wah', 'Peshawar', 'Mardan', 
                     'Swabi', 'Mansehra', 'Abbotabad', 'Swat', 'Quetta'];

    // Karachi Zones
    if ($shipping_city == 'Karachi') {
        if (in_array($shipping_state, ['Sindh'])) {
            if (in_array($shipping_city, $karachi_districts)) {
                $shipping_cost = 250; // Main 5 Districts of Karachi
            } else {
                $shipping_cost = 350; // Other Areas of Karachi
            }
        }
    }
    // Major Cities in Pakistan
    elseif (in_array($shipping_city, $major_cities)) {
        $shipping_cost = 350; // Major cities
    }
    // Remote Areas
    else {
        $shipping_cost = 500; // Remote areas
    }

    // Add the fee to cart
    WC()->cart->add_fee('Custom Shipping', $shipping_cost);
}

// Hide WooCommerce shipping options when custom fee is applied
add_filter('woocommerce_package_rates', 'hide_shipping_when_custom_fee_applied', 10, 2);
function hide_shipping_when_custom_fee_applied($rates, $package) {
    if (!is_admin()) {
        $custom_shipping_fee = false;
        
        // Check if the custom location-based fee is applied
        foreach (WC()->cart->get_fees() as $fee) {
            if ($fee->name == 'Custom Shipping') {
                $custom_shipping_fee = true;
                break;
            }
        }

        // Hide all shipping methods if custom shipping fee is applied
        if ($custom_shipping_fee) {
            $rates = [];
        }
    }
    
    return $rates;
}
?>
