<?php
$tabs = [
		[
			'title' 		=> 'Home',
			'controller' 	=> 'home',
		],
		[
			'title' 		=> 'Product',
			'controller' 	=> 'product',
		]
	];
	
	if( current_user_can( 'administrator' ) ) {
		$admin_tabs = [
			[
				'title' 		=> 'Api',
				'controller' 	=> 'api',
			]
		];
		$tabs = array_merge( $tabs, $admin_tabs );
	}
	
	$_GET['controller'] = ( isset($_GET['controller']) ) ? $_GET['controller'] : 'home';
?>

<ul class="nav nav-tabs mt-4" id="myTab" role="tablist">
	<?php foreach( $tabs as $tab ):
		$link = AppUrlHelper::build([ 'controller' => $tab['controller'] ]);
		$class = '';
		if( $_GET['controller'] == $tab['controller'] ){
			$class = 'active';
		}

	?>
	<li class="nav-item">
	    <a href="<?= $link;?>" class="nav-link <?= $class; ?>"><?= $tab['title'];?></a>
  	</li>	
	<?php endforeach;?>
</ul>