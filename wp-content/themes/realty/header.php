<?php
header('X-UA-Compatible: IE=edge,chrome=1'); 
global $realty_theme_option;
if ( !empty( $realty_theme_option['logo-menu']['url'] ) ) {
	$logo = $realty_theme_option['logo-menu']['url'];
}
if ( !empty( $realty_theme_option['favicon']['url'] ) ) {
	$favicon = $realty_theme_option['favicon']['url'];
}
else {
	$favicon = '';
}
$phone = $realty_theme_option['site-header-phone'];
$email = $realty_theme_option['site-header-email'];
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php wp_title( '|', true, 'right' ); ?><?php bloginfo('name'); ?></title>
<?php if( $favicon ) { ?>
<link rel="shortcut icon" href="<?php echo $favicon; ?>" />
<?php } ?>
<?php wp_head(); ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/html5.js"></script>
<![endif]-->
</head>

<body <?php body_class(); ?>>

<?php if ( !is_page_template( 'template-intro.php' ) ) { ?>
<header class="navbar<?php if ( $realty_theme_option['header-layout'] == 'nav-right' ) { echo " nav-right"; } ?>">
  <div class="container">
    
    <?php if ( $realty_theme_option['header-layout'] == 'nav-right' ) { ?>
		<div class="navbar-contact-details">
	    <?php 
	    if( !empty( $phone ) ) { echo '<div class="navbar-phone-number">' . $phone . '</div>'; }
	    if ( $phone && $email ) { echo ' <div class="separator">&middot;</div> '; }
	    if( !empty( $email ) ) { echo '<div class="navbar-email">' . antispambot( $email ) . '</div>'; } 
	    ?>
    </div>
    <?php } ?>
    
  	<?php if ( !$realty_theme_option['disable-header-login-register-bar'] ) { get_template_part( 'lib/inc/template/login-bar-header' ); }	?>
	  
	  <div class="navbar-header">
	  
	    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
	    	<span class="sr-only"><?php _e( 'Skip navigation', 'tt' ); ?></span>
	    	<span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
		  </button>
		  
		  <div class="navbar-brand">
		    <a href="<?php echo esc_url( home_url( '/home/' ) ); ?>">
		    <?php 
		    if( empty( $logo ) ) { 
		    	echo '<span>' . bloginfo('name') . '</span>';
		    } else {
		    	echo '<img src="' . $logo . '" alt="" />';
 				} ?>
		    </a>
	    </div>
	    
	    <?php if ( $realty_theme_option['header-layout'] == 'default' ) { ?>
	    <div class="navbar-contact-details">
		    <?php 
		    if( !empty( $phone ) ) { echo '<div class="navbar-phone-number">' . $phone . '</div>'; }
		    if( !empty( $email ) ) { echo '<div class="navbar-email">' . antispambot( $email ) . '</div>'; } 
		    ?>
	    </div>
	    <?php } ?>
	    
	    <nav class="collapse navbar-collapse" role="navigation">
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => 'div', 'container_class' => 'nav navbar-nav', 'menu_class' => 'clearfix', 'depth' => 3 ) ); ?>
				<div id="toggle-navbar"><i class="icon-angle-right"></i></div>
			</nav>
			
		</div>
	  
  </div> 
</header>
<?php } ?>

<?php if ( !is_page_template( 'template-property-slideshow.php' ) && !is_page_template( 'template-intro.php' ) && !is_page_template( 'template-home-slideshow.php' ) && !is_page_template( 'template-home-properties-map.php' ) && !is_page_template( 'template-property-search.php' ) ) { ?>
<div class="container header-margin">
<?php } ?>