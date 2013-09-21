<?php
$this->breadcrumbs=array(
	'Giheaders',
);
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']);
?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.autoNumeric.js" ></script>
<script type="text/javascript">
var inputConfig = {aSep:',', aNeg:'-', mDec:2, mRound:'S', mNum:30};
$(function() {
            $('#Gidetail_qty').autoNumeric(inputConfig);
        });	// here is the magic
function adddata()
{
    <?php echo CHtml::ajax(array(
			'url'=>array('giheader/create'),
            'data'=> "js:$(this).serialize()",
            'type'=>'post',
            'dataType'=>'json',
            'success'=>"function(data)
            {
				document.getElementById('messages').innerHTML = '';
                if (data.status == 'success')
                {
                    $('#createdialog div.divcreate').html(data.div);
					$('#Giheader_giheaderid').val(data.giheaderid);
					$('#Giheader_gino').val('');
					$('#Giheader_gidate').val('');
					$('#Giheader_postdate').val('');
					$('#Giheader_deliveryadviceid').val('');
					$('#dano').val('');
					$('#Giheader_soheaderid').val('');
					$('#sono').val('');
					$('#Giheader_location').val('');
					$('#Giheader_headernote').val('');
document.forms[2].elements[1].value=data.giheaderid;
$.fn.yiiGridView.update('detaildatagrid',{data:{'Gidetail[giheaderid]':data.giheaderid}});
                          // Here is the trick: on submit-> once again this function!
                    $('#createdialog').dialog('open');
                }
                else
                {
                    document.getElementById('messages').innerHTML = data.div;
                }
            } ",
            ))?>;
    return false;
}
</script>
<script type="text/javascript">
function editdata()
{
    <?php
	echo CHtml::ajax(array(
			'url'=>array('giheader/update'),
            'data'=> array('id'=>'js:$.fn.yiiGridView.getSelection("datagrid")'),
            'type'=>'post',
            'dataType'=>'json',
            'success'=>"function(data)
            {
document.getElementById('messages').innerHTML = '';
                if (data.status == 'success')
                {
                    $('#createdialog div.divcreate').html(data.div);
					$('#Giheader_giheaderid').val(data.giheaderid);
					$('#Giheader_gino').val(data.gino);
					$('#Giheader_gidate').val(data.gidate);
					$('#Giheader_postdate').val(data.postdate);
					$('#Giheader_deliveryadviceid').val(data.deliveryadviceid);
					$('#dano').val(data.dano);
					$('#Giheader_soheaderid').val(data.soheaderid);
					$('#sono').val(data.sono);
					$('#Giheader_location').val(data.location);
					$('#Giheader_headernote').val(data.headernote);
document.forms[2].elements[1].value=data.giheaderid;
$.fn.yiiGridView.update('detaildatagrid',{data:{'Gidetail[giheaderid]':data.giheaderid}});
                          // Here is the trick: on submit-> once again this function!
                    $('#createdialog').dialog('open');
                }
                else
                {
                    document.getElementById('messages').innerHTML = data.div;
                }
            } ",
            ))?>;
    return false;
}
</script>
<script type="text/javascript">
function deletedata()
{
    <?php
	echo CHtml::ajax(array(
			'url'=>array('giheader/delete'),
            'data'=> array('id'=>'js:$.fn.yiiGridView.getSelection("datagrid")'),
            'type'=>'post',
            'dataType'=>'json',
            'success'=>"function(data)
            {

            } ",
            ))?>;
	$.fn.yiiGridView.update('datagrid');
    return false;
}
</script>
<script type="text/javascript">
function approvedata()
{
    <?php
	echo CHtml::ajax(array(
			'url'=>array('giheader/approve'),
            'data'=> array('id'=>'js:$.fn.yiiGridView.getSelection("datagrid")'),
            'type'=>'post',
            'dataType'=>'json',
            'success'=>"function(data)
            {
if (data.status == 'failure') {
				document.getElementById('messages').innerHTML = data.div;}
            } ",
            ))?>;
	$.fn.yiiGridView.update('datagrid');
    return false;
}
</script>
<script type="text/javascript">
function refreshdata()
{
    $.fn.yiiGridView.update('datagrid');
    return false;
}
</script>
<script type="text/javascript">
function generatedata() {
	jQuery.ajax({
        'url': '/smlive/index.php?r=giheader/generatedata',
        'data': {
            'id': $('#Gidetail_productdetailid').val(),
        },
        'type': 'post',
        'dataType': 'json',
        'success': function(data) {
            if(data.status=='failure') {
            document.getElementById('messages').innerHTML = data.div;
            } else {
                $('#productname').val(data.productname);
                $('#Gidetail_qty').val(data.qty);
                $('#Gidetail_unitofmeasureid').val(data.unitofmeasureid);
                $('#uomcode').val(data.uomcode);
                $('#Gidetail_slocid').val(data.slocid);
                $('#sloccode').val(data.sloccode);
                $('#serialno').val(data.serialno);
            }
        },
        'cache': false
    });;
    return false;
}
</script>
<script type="text/javascript">
function helpdata(value) {
	jQuery.ajax({
        'url': '/smlive/index.php?r=giheader/help',
        'data': {
            'id': value
        },
        'type': 'post',
        'dataType': 'json',
        'success': function(data) {
            if (data.status == 'success') {
				document.getElementById('divhelp').innerHTML = data.div;
                $('#helpdialog').dialog('open');
            } else {
                document.getElementById('messages').innerHTML = data.div;
            }
        },
        'cache': false
    });;
    return false;
}
</script>
<script type="text/javascript">
function showdetail() {
    $.fn.yiiGridView.update('indatagrid', {
                    data: {
                        'Gidetail[giheaderid]': $.fn.yiiGridView.getSelection("datagrid")[0]
                    }
                });
    return false;
}
</script>
<script type="text/javascript">
function downloaddata() {
	window.open('/smlive/index.php?r=giheader/download&id='+$.fn.yiiGridView.getSelection("datagrid"));
}
</script>
<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array( // the dialog
    'id'=>'createdialog',
    'options'=>array(
        'title'=>'Form Dialog',
        'autoOpen'=>false,
        'modal'=>true,
        'width'=>'auto',
        'height'=>'auto',
    ),
));?>
<div id="divcreate"></div>
<?php echo $this->renderPartial('_form', array('model'=>$model,
					'gidetail'=>$gidetail)); ?>
<?php $this->endWidget();?>
<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array( // the dialog
    'id'=>'helpdialog',
    'options'=>array(
        'title'=>'Help',
        'autoOpen'=>false,
        'modal'=>true,
        'width'=>'auto',
        'height'=>'auto',
    ),
));?>
<div id="divhelp"></div>
<?php $this->endWidget();?>
<h1>Transaction: Goods Issues</h1>
<div id="toolbar">
<ul>
<?php
$imgcreate=CHtml::image(Yii::app()->request->baseUrl.'/images/create.png');
echo CHtml :: openTag('li');
echo CHtml::link($imgcreate,'#',array(
   'style'=>'cursor: pointer; text-decoration: underline;',
   'onclick'=>"{adddata()}",
	'title'=>Yii::t('app','create data')
));
echo CHtml :: closeTag('li');

$imgedit=CHtml::image(Yii::app()->request->baseUrl.'/images/edit.png');
echo CHtml :: openTag('li');
echo CHtml::link($imgedit,'#',array(
   'style'=>'cursor: pointer; text-decoration: underline;',
   'onclick'=>"{editdata()}",
	'title'=>Yii::t('app','edit data')
));
echo CHtml :: closeTag('li');

$imgapprove=CHtml::image(Yii::app()->request->baseUrl.'/images/approve.png');
echo CHtml :: openTag('li');
echo CHtml::link($imgapprove,'#',array(
   'style'=>'cursor: pointer; text-decoration: underline;',
   'onclick'=>"{approvedata()}",
   'title'=>Yii::t('app','approve data')
));
echo CHtml :: closeTag('li');

$imgdelete=CHtml::image(Yii::app()->request->baseUrl.'/images/delete.png');
echo CHtml :: openTag('li');
echo CHtml::link($imgdelete,'#',array(
   'style'=>'cursor: pointer; text-decoration: underline;',
   'onclick'=>"{deletedata()}",
   'title'=>Yii::t('app','delete data')
));
echo CHtml :: closeTag('li');

$imgdown=CHtml::image(Yii::app()->request->baseUrl.'/images/down.png');
echo CHtml :: openTag('li');
echo CHtml::link($imgdown,'#',array(
   'style'=>'cursor: pointer; text-decoration: underline;',
    'onclick'=>"{downloaddata()}",
   'title'=>Yii::t('app','download data')
));
echo CHtml :: closeTag('li');

$imgrefresh=CHtml::image(Yii::app()->request->baseUrl.'/images/refresh.png');
echo CHtml :: openTag('li');
echo CHtml::link($imgrefresh,'#',array(
   'style'=>'cursor: pointer; text-decoration: underline;',
   'onclick'=>"{refreshdata()}",
   'title'=>Yii::t('app','refresh data')
));
echo CHtml :: closeTag('li');

$imghelp=CHtml::image(Yii::app()->request->baseUrl.'/images/help.png');
echo CHtml :: openTag('li');
echo CHtml::link($imghelp,'#',array(
   'style'=>'cursor: pointer; text-decoration: underline;',
   'onclick'=>"{helpdata(1)}",
   'title'=>Yii::t('app','help')
));
echo CHtml :: closeTag('li');
?>
<div class="recordpage">Record/page<?php echo CHtml::textField($pageSize,'',array('size'=>'5',
        // change 'user-grid' to the actual id of your grid!!
        'onchange'=>"$.fn.yiiGridView.update('datagrid',{ data:{pageSize: $(this).val() }})",
	  ));  ?></div></ul></div>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'datagrid',
	'dataProvider'=>$model->Searchwfstatus(),
	'filter'=>$model,
    'selectionChanged'=>'showdetail',
  'selectableRows'=>1,
	'template'=>'{pager}<br>{items}{pager}',
	'columns'=>array(
    array(
      'class'=>'CCheckBoxColumn',
      'id'=>'ids',
    ),
	array('name'=>'giheaderid','visible'=>false, 'header'=>'ID','value'=>'$data->giheaderid','htmlOptions'=>array('width'=>'1%')),
		'gino',
        array(
      'name'=>'gidate',
      'type'=>'raw',
         'value'=>'date(Yii::app()->params["dateviewfromdb"], strtotime($data->gidate))'
     ),
	array('name'=>'deliveryadviceid', 'value'=>'($data->deliveryadvice!==null)?$data->deliveryadvice->dano:""'),
	array('name'=>'soheaderid', 'value'=>'($data->soheader!==null)?$data->soheader->sono:""'),
        'location',
        'headernote',
	array('header'=>'Status','value'=>'Wfstatus::model()->findstatusname("appgi",$data->recordstatus)')
  ),
));
?>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'indatagrid',
	'dataProvider'=>$gidetail->search(),
  'selectableRows'=>1,
	'template'=>'{pager}<br>{items}{pager}',
	'columns'=>array(
	array('name'=>'gidetailid','visible'=>false, 'header'=>'ID','value'=>'$data->gidetailid','htmlOptions'=>array('width'=>'1%')),
	array('name'=>'productdetailid','value'=>'($data->productdetail!==null)?$data->productdetail->product->productname:""'),
	 array(
      'name'=>'qty',
      'type'=>'raw',
         'value'=>'Yii::app()->numberFormatter->format(Yii::app()->params["defaultnumberqty"],$data->qty)',
     ),
	array('name'=>'unitofmeasureid','value'=>'($data->unitofmeasure!==null)?$data->unitofmeasure->uomcode:""'),
	array('name'=>'slocid','value'=>'($data->sloc!==null)?$data->sloc->description:""'),
        'itemnote',
        'serialno'
  ),
));
?>