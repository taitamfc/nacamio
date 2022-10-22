<h3 class="mt-4"><?= $toolbar['title'];?></h3>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item">
    	<a href="<?= AppUrlHelper::build(['controller'=>'Home']);?>">Dashboard</a>
    </li>
    
    <li class="breadcrumb-item">
    	<a href="<?= AppUrlHelper::build(['controller'=>$_GET['controller']]);?>">
    		<?= $toolbar['title'];?></a>
    </li>
	
    <li class="breadcrumb-item active"><?= $toolbar['title'];?></li>
</ol>
<?php $this->element('flash');?>
<?php $this->element('progress');?>