<?php
$this->breadcrumbs=array(
	'Moduletypes',
);

$this->menu=array(
	array('label'=>'Manage', 'url'=>array('admin')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('moduletype-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Moduletypes</h1>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'moduletype-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'moduletypeid',
		'moduletypename',
		'recordstatus',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
