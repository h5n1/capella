<?php

class PermitinController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
protected $menuname = 'permitin';

	public function actionHelp()
	{
		if (isset($_POST['id'])) {
			$id= (int)$_POST['id'];
			switch ($id) {
				case 1 : $this->txt = '_help'; break;
				case 2 : $this->txt = '_helpmodif'; break;
			}
		}
	  parent::actionHelp();
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
	  parent::actionCreate();
	  $snro=new Snro('searchwstatus');
	  $snro->unsetAttributes();  // clear any default values
	  if(isset($_GET['Snro']))
		$snro->attributes=$_GET['Snro'];

		$model=new Permitin;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (Yii::app()->request->isAjaxRequest)
        {
            echo CJSON::encode(array(
                'status'=>'success',
                'div'=>$this->renderPartial('_form', array('model'=>$model,
			'snro'=>$snro), true)
				));
            Yii::app()->end();
        }
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
	  parent::actionUpdate();
	  $snro=new Snro('searchwstatus');
	  $snro->unsetAttributes();  // clear any default values
	  if(isset($_GET['Snro']))
		$snro->attributes=$_GET['Snro'];
		$id=$_POST['id'];
	  $model=$this->loadModel($id[0]);
if ($model != null)
      {
        if ($this->CheckDataLock($this->menuname, $id[0]) == false)
        {
          $this->InsertLock($this->menuname, $id[0]);
            echo CJSON::encode(array(
                'status'=>'success',
				'permitinid'=>$model->permitinid,
				'permitinname'=>$model->permitinname,
				'snroid'=>$model->snroid,
				'description'=>$model->snro->description,
				'recordstatus'=>$model->recordstatus,
                'div'=>$this->renderPartial('_form', array('model'=>$model,
				  'snro'=>$snro), true)
				));
            Yii::app()->end();
        }
        }
	}

    public function actionCancelWrite()
    {
      $this->DeleteLockCloseForm($this->menuname, $_POST['Permitin'], $_POST['Permitin']['permitinid']);
    }


	public function actionWrite()
	{
	  parent::actionWrite();
	  if(isset($_POST['Permitin']))
	  {
        $messages = $this->ValidateData(
                array(array($_POST['Permitin']['permitinname'],'hpapiemptypermitinname','emptystring'),
                    array($_POST['Permitin']['snroid'],'hpapiemptysnroid','emptystring'),
            )
        );
        if ($messages == '') {
		//$dataku->attributes=$_POST['Permitin'];
		if ((int)$_POST['Permitin']['permitinid'] > 0)
		{
		  $model=$this->loadModel($_POST['Permitin']['permitinid']);
		  $model->permitinname = $_POST['Permitin']['permitinname'];
		  $model->snroid = $_POST['Permitin']['snroid'];
		  $model->recordstatus = $_POST['Permitin']['recordstatus'];
		}
		else
		{
		  $model = new Permitin();
		  $model->attributes=$_POST['Permitin'];
		}
		try
          {
            if($model->save())
            {
              $this->DeleteLock($this->menuname, $_POST['Permitin']['permitinid']);
              $this->GetSMessage('hpapiinsertsuccess');
            }
            else
            {
              $this->GetMessage($model->getErrors());
            }
          }
          catch (Exception $e)
          {
            $this->GetMessage($e->getMessage());
          }
        }
	  }
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete()
	{
	  parent::actionDelete();
		$id=$_POST['id'];
		foreach($id as $ids)
		{
		  $model=$this->loadModel($ids);
		  $model->recordstatus=0;
		  $model->save();
		}
		echo CJSON::encode(array(
                'status'=>'success',
                'div'=>'Data deleted'
				));
        Yii::app()->end();
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
	  parent::actionIndex();
	  $snro=new Snro('searchwstatus');
	  $snro->unsetAttributes();  // clear any default values
	  if(isset($_GET['Snro']))
		$snro->attributes=$_GET['Snro'];
		$model=new Permitin('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Permitin']))
			$model->attributes=$_GET['Permitin'];
if (isset($_GET['pageSize']))
	  {
		Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
		unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
	  }
		$this->render('index',array(
			'model'=>$model,
			'snro'=>$snro
		));
	}

	public function actionUpload()
	{
      parent::actionUpload();
	  $folder=$_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/upload/';// folder for uploaded files
	  $allowedExtensions = array("csv");
	  $sizeLimit = (int)Yii::app()->params['sizeLimit'];// maximum file size in bytes
	  $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
	  $result = $uploader->handleUpload($folder,true);
	  $row = 0;
	  if (($handle = fopen($folder.$uploader->file->getName(), "r")) !== FALSE) {
		  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($row>0) {
			  $model=Permitin::model()->findByPk((int)$data[0]);
			  if ($model=== null) {
				$model = new Permitin();
			  }
			  $model->permitinid = (int)$data[0];
			  $model->permitinname = $data[1];
			  $model->snroid = (int)$data[2];
			  $model->recordstatus = (int)$data[3];
			  try
			  {
				if(!$model->save())
				{
				  $errormessage=$model->getErrors();
				  if (Yii::app()->request->isAjaxRequest)
				  {
					echo CJSON::encode(array(
					  'status'=>'failure',
					  'div'=>$errormessage
					));
				  }
				}
			  }
			  catch (Exception $e)
			  {
				$errormessage=$e->getMessage();
				if (Yii::app()->request->isAjaxRequest)
				  {
					echo CJSON::encode(array(
					  'status'=>'failure',
					  'div'=>$errormessage
					));
				  }
			  }
			}
			$row++;
		  }
		  fclose($handle);
	  }
	  $result=htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	  echo $result;
  }

  public function actionDownload()
  {
    parent::actionDownload();
    $sql = "select a.permitinname
      from permitin a ";
		if ($_GET['id'] !== '') {
				$sql = $sql . "where a.permitinid = ".$_GET['id'];
		}
		$command=$this->connection->createCommand($sql);
		$dataReader=$command->queryAll();
	  $this->pdf->title='Permit In List';
	  $this->pdf->AddPage('P');
	  $this->pdf->setFont('Arial','B',12);

	  // definisi font
	  $this->pdf->setFont('Arial','B',8);

    $this->pdf->setaligns(array('C','C'));
    $this->pdf->setwidths(array(50,90));
    $this->pdf->Row(array('Permit In'));
    $this->pdf->setaligns(array('L','L'));
    foreach($dataReader as $row1)
    {
      $this->pdf->row(array($row1['permitinname']));
    }
    // me-render ke browser
    $this->pdf->Output();
  }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Permitin::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='permitin-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
