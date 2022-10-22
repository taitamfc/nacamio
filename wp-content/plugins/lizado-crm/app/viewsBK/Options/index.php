<main>
	<div class="container-fluid">
		<?php $this->element('tabs');?>
	    <div class="tab-content" id="myTabContent">
  			<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
		        <div class="card mt-0" style="border-radius: 0;border-top: 0;">
		            <div class="card-body">
	        	<form class="ajax-form" action="" method="POST">
		        	<div class="row">
			            <div class="col-md-9">
							<div class="form-group">
								<label>Cookie Content</label>
								<textarea class="form-control" name="tool_crawl_option_cookie"><?= get_option('tool_crawl_option_cookie');?></textarea>
							</div>
							<div class="form-group">
								<button class="btn btn-primary">Save</button>
							</div>
			            </div>
			            <div class="col-md-3">
			         
			            </div>
			        </div>
			    </form>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
