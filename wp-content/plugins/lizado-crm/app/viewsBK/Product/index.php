<main>
    <div class="container-fluid">
    	<?php $this->element('tabs');?>
    	<div class="tab-content" id="app-root">
  			<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
		        <div class="card mt-0" style="border-radius: 0;border-top: 0;">
		            <div class="card-body">
		                <form action="" method="GET" id="app_ajax">
							
							<?php $this->element('action',[]);?>
							<table class="wp-list-table widefat fixed striped users">
								<thead>
									<tr>
										<th width="4%">ID</th>
										<th width="15%">Title </th>
										<th width="5%">Price</th>
										<th width="5%">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach( $items['objects'] as $key => $item ):?>
									<tr>
										<td><?= $item->id;?></td>
										<td><?= $item->title ;?></td>
										<td><?= $item->price;?></td>
										<td>
											<a href="<?= AppUrlHelper::build([ 'controller' => $_GET['controller'], 'action'=>'show','id'=>$item->id ])?>">View</a>
											<?php if( $item->wordpress_post_id ):?>
											| <a target="_blank" href="<?= get_the_permalink($item->wordpress_post_id)?>">View Post</a>
											<?php else:?>
											| <a href="javascript:;" @click="import_to_post(<?= $item->id; ?>)" class="product_<?= $item->id; ?>">Import</a>
											<?php endif;?>
										</td>
									</tr>
									<?php unset($items['objects'][$key]); endforeach;?>
								</tbody>
							</table>
							<?php $this->element('pagination',['items'=>$items]);?>
						</form>
		            </div>
		        </div>
		    </div>
        </div>
    </div>
</main>
<script type="text/javascript">

  	var crawl_app_table = new Vue({
		el: '#app-root',
		data: {
			isWorking:false,
			ajax_msg:'Ready',
			page:'<?= get_option("cron_craw_product"); ?>',
			ajax_import_url : '<?= $ajax_import_url; ?>'
		},
		methods: {
			import_to_post( id ){
				axios
				.get(this.ajax_import_url+'&id='+id)
				.then( res => {
					var response = res.data;
					if( response.status == 1 ){
						jQuery('.product_'+id).text('Imported');
						jQuery('.product_'+id).prop('disabled',true);
					}
				});
			},
			update_data(){
				this.isWorking = true;
				this.ajax_msg = 'Working on page '+this.page;
				axios
				.get(this.ajax_import_url+'&page='+this.page)
				.then( res => {
					var response = res.data;
					console.log(response);
					if( response.status == 1 ){
						this.page = parseInt(this.page) + 1;
						this.update_data();
					}else if( response.status == 0 ){
						this.page = 1;
						this.isWorking = false;
						this.ajax_msg = 'Ready !';
					}else{
						this.page = 1;
						this.isWorking = false;
						this.ajax_msg = response.msg;
						alert(response.msg);
					}
				})
			}
		}
  	});
</script>