<?php
const NJSS_SOCIAL_NETWORKS = array(
	array(
		'name' => "facebook",
		'display_name' => "Facebook",
		'url' => 'https://www.facebook.com/sharer.php?u=[[PERMALINK]]',
		'link_title' => "Share on Facebook!",
		'image' => "assets/images/facebook.svg",
		/*'image' => array(
			'png' => "/assets/images/social_facebook.png",
			'webp' => "/assets/images/social_facebook.webp",
		)*/
	),
	array(
		'name' => "twitter",
		'display_name' => "Twitter",
		'url' => 'https://twitter.com/intent/tweet?text=[[TITLE]]%0a&url=[[PERMALINK]]',
		'link_title' => "Tweet this!",
		'image' => "assets/images/twitter.svg",
	), 
	array(
		'name' => "pinterest",
		'display_name' => "Pinterest",
		'url' => 'http://pinterest.com/pin/create/button/?url=[[PERMALINK]]&description=[[TITLE]]',
		'link_title' => "Share on Pinterest!",
		'image' => "assets/images/pinterest.svg",
	),
	array(
		'name' => "email",
		'display_name' => "Email",
		'url' => 'mailto:?subject=Check out [[BLOGINFO]]&amp;body=Check out this page: [[PERMALINK]]',
		'link_title' => "Share by Email",
		'image' => "assets/images/email.svg",
	),
);

const NJSS_DEFAULT_SETTINGS = array(
	'networks'      => array(
		'facebook'    => 'on',
		'twitter'     => 'on',
		'pinterest'   => 'on',
		'email'       => 'on',
	),
	'placement'     => 'none',
    'loc_post'      => 'on',
    'sharing_title' => 'Share this post on:', 
    'icon_size'     => 32,
);
