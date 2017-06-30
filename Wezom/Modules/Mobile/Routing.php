<?php   
    
    return [
        'wezom/mobile/config' => 'mobile/mobile/config',
		'wezom/mobile/delete_image/<id:[0-9]*>' => 'mobile/mobile/deleteImage',
		
			// Orders
        'wezom/mobile_orders/index' => 'mobile/orders/index',
        'wezom/mobile_orders/index/page/<page:[0-9]*>' => 'mobile/orders/index',
        'wezom/mobile_orders/edit/<id:[0-9]*>' => 'mobile/orders/edit',
        'wezom/mobile_orders/delete/<id:[0-9]*>' => 'mobile/orders/delete',
        'wezom/mobile_orders/print/<id:[0-9]*>' => 'mobile/orders/print',
        'wezom/mobile_orders/add_position/<id:[0-9]*>' => 'mobile/orders/addPosition',
        'wezom/mobile_orders/add' => 'mobile/orders/add',
		
		// resize images for mobile
		'wezom/mobile/images' => 'mobile/mobile/images',
		
		// Banners
        'wezom/mobile_banners/index' => 'mobile/banners/index',
        'wezom/mobile_banners/index/page/<page:[0-9]*>' => 'mobile/banners/index',
        'wezom/mobile_banners/edit/<id:[0-9]*>' => 'mobile/banners/edit',
        'wezom/mobile_banners/delete/<id:[0-9]*>' => 'mobile/banners/delete',
        'wezom/mobile_banners/delete_image/<id:[0-9]*>' => 'mobile/banners/deleteImage',
        'wezom/mobile_banners/add' => 'mobile/banners/add',
    ];