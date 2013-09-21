<?php

class EmployeeclaimController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	protected $menuname = 'employeeclaim';

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
	
	public $employee;

	public function lookupdata()
	{
	  $this->employee=new Employee('searchwstatus');
	  $this->employee->unsetAttributes();  // clear any default values
	  if(isset($_GET['Employee']))
		$this->employee->attributes=$_GET['Employee'];
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
	  parent::actionCreate();
	  $this->lookupdata();
	  $model=new Employeeclaim;
	  if (Yii::app()->request->isAjaxRequest)
	  {
		  echo CJSON::encode(array(
			  'status'=>'success',
			  'divcreate'=>$this->renderPartial('_form', array('model'=>$model,
		  'employee'=>$this->employee), true)
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
	  $this->lookupdata();
	  $id=$_POST['id'];
	  $model=$this->loadModel($id[0]);
	  if ($model != null)
      {
        if ($this->CheckDataLock($this->menuname, $id[0]) == false)
        {
          $this->InsertLock($this->menuname, $id[0]);
		  echo CJSON::encode(array(
			  'status'=>'success',
			  'employeeclaimid'=>$model->employeeclaimid,
			  'employeeid'=>$model->employeeid,
			  'fullname'=>$model->employee->fullname,
			  'claimenterdate'=>date(Yii::app()->params['dateviewfromdb'], strtotime($model->claimenterdate)),
			  'claimexitdate'=>date(Yii::app()->params['dateviewfromdb'], strtotime($model->claimexitdate)),
			  'claimprice'=>$model->claimprice,
			  'claimappdate'=>date(Yii::app()->params['dateviewfromdb'], strtotime($model->claimappdate)),
			  'claimappprice'=>$model->claimappprice,
			  'reason'=>$model->reason,
			  'recordstatus'=>$model->recordstatus,
			  'div'=>$this->renderPartial('_form', array('model'=>$model,
		  'employee'=>$this->employee), true)
			  ));
		  Yii::app()->end();
        }
	  }
	}

    public function actionCancelWrite()
    {
      $this->DeleteLockCloseForm($this->menuname, $_POST['Employeeclaim'],
              $_POST['Employeeclaim']['employeeclaimid']);
    }

	public function actionWrite()
	{
	  parent::actionWrite();
	  if(isset($_POST['Employeeclaim']))
	  {
        $messages = $this->ValidateData(
                array(array($_POST['Employeeclaim']['employeeid'],'heecemptyemployeeid','emptystring'),
                array($_POST['Employeeclaim']['claimenterdate'],'heecemptyclaimenterdate','emptystring'),
                    array($_POST['Employeeclaim']['claimexitdate'],'heecemptyclaimexitdate','emptystring'),
                array($_POST['Employeeclaim']['claimprice'],'heecemptyclaimprice','emptystring'),
            )
        );
        if ($messages == '') {
		//$dataku->attributes=$_POST['Employeeclaim'];
		if ((int)$_POST['Employeeclaim']['employeeclaimid'] > 0)
		{
		  $model=$this->loadModel($_POST['Employeeclaim']['employeeclaimid']);
		  $model->employeeid = $_POST['Employeeclaim']['employeeid'];
		  $model->claimenterdate = $_POST['Employeeclaim']['claimenterdate'];
		  $model->claimexitdate = $_POST['Employeeclaim']['claimexitdate'];
		  $model->claimprice = $_POST['Employeeclaim']['claimprice'];
		  $model->claimappdate = $_POST['Employeeclaim']['claimappdate'];
		  $model->claimappprice = $_POST['Employeeclaim']['claimappprice'];
		  $model->reason = $_POST['Employeeclaim']['reason'];
		  $model->recordstatus = $_POST['Employeeclaim']['recordstatus'];
		}
		else
		{
		  $model = new Employeeclaim();
		  $model->attributes=$_POST['Employeeclaim'];
		}
		try
          {
            if($model->save())
            {
              $this->DeleteLock($this->menuname, $_POST['Employeeclaim']['employeeclaimid']);
              $this->GetSMessage('heejinsertsuccess');
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
	 * If deletion is successful, the browser will be redirected to the 'index' page.
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
	  $this->lookupdata();
	  $model=new Employeeclaim('search');
	  $model->unsetAttributes();  // clear any default values
	  if(isset($_GET['Employeeclaim']))
			$model->attributes=$_GET['Employeeclaim'];
	  if (isset($_GET['pageSize']))
	  {
		Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
		unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
	  }
	  $this->render('index',array(
		'model'=>$model,
		'employee'=>$this->employee
	  ));
	}

	public function actionUpload()
	{
      parent::actionUpload();
	  Yii::import("ext.EAjaxUpload.qqFileUploader");
	  $folder=$_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/upload/';// folder for uploaded files
	  $allowedExtensions = array("csv");
	  $sizeLimit = (int)Yii::app()->params['sizeLimit'];// maximum file size in bytes
	  $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
	  $result = $uploader->handleUpload($folder,true);
	  $row = 0;
	  if (($handle = fopen($folder.$uploader->file->getName(), "r")) !== FALSE) {
		  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($row>0) {
			  $model=Employeeclaim::model()->findByPk((int)$data[0]);
			  if ($model=== null) {
				$model = new Employeeclaim();
			  }
			  $model->employeeclaimid = (int)$data[0];
			  $model->employeeid = (int)$data[1];
			  $model->jamsostekdate = $data[2];
			  $model->jamsostekno = $data[3];
			  $model->recordstatus = (int)$data[4];
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
    $pdf = new PDF();
	  $pdf->title='Employee List';
	  $pdf->AddPage('P');
	  $pdf->setFont('Arial','B',12);

	  // definisi font
	  $pdf->setFont('Arial','B',8);

	  // menuliskan tabel
	  $connection=Yii::app()->db;
    $sql = "select a.employeeid,a.fullname, a.oldnik, b.levelorgname, c.structurename,d.positionname,e.employeetypename,
        f.sexname,joindate,email,phoneno,alternateemail,hpno,a.addressbookid
      from employee a
      left join levelorg b on b.levelorgid = a.levelorgid
      left join orgstructure c on c.orgstructureid = a.orgstructureid
      left join position d on d.positionid = a.positionid
      left join employeetype e on e.employeetypeid = a.employeetypeid
      left join sex f on f.sexid = a.sexid
      left join employeeclaim g on g.employeeid = a.employeeid ";
if ($_GET['id'] !== '') {
				$sql = $sql . "where g.employeeclaimid = ".$_GET['id'];
		}
		$sql = $sql . " order by employeeid";
    $command=$connection->createCommand($sql);
    $dataReader=$command->queryAll();

    foreach($dataReader as $row)
    {
      if (file_exists($_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/images/employee/photo-'.$row['oldnik'].'.jpg'))
      {
        $pdf->Image($_SERVER['DOCUMENT_ROOT'].Yii::app()->request->baseUrl.'/images/employee/photo-'. $row['oldnik'] .'.jpg',10,30,30);
      }
      $pdf->setFont('Arial','B',10);
      $pdf->text(50,30,'Nama: '.$row['fullname']);
      $pdf->setFont('Arial','',8);
      $pdf->text(50,35,'Golongan: '.$row['levelorgname']);
      $pdf->text(50,40,'Struktur: '.$row['structurename']);
      $pdf->text(50,45,'Posisi: '.$row['positionname']);
      $pdf->text(50,50,'Jenis Kelamin: '.$row['sexname']);
      $pdf->text(50,55,'Email Utama: '.$row['email']);
      $pdf->text(50,65,'Email ke-2: '.$row['alternateemail']);
      $pdf->text(50,70,'Telp: '.$row['phoneno']);
      $pdf->text(50,75,'No HP: '.$row['hpno']);

      $sql1 = "select claimenterdate,claimexitdate,claimprice,reason
        from employeeclaim
        where employeeid = ".$row['employeeid'];
      $command1=$connection->createCommand($sql1);
      $dataReader1=$command1->queryAll();

      $pdf->text(10,90,'Address List');
      $pdf->SetY(95);
      $pdf->setaligns(array('C','C','C','C'));
      $pdf->setwidths(array(30,30,30,50));
      $pdf->Row(array('Claim Enter Date','Claim Exit Date','Claim Price','Reason'));
      $pdf->setaligns(array('L','L','L','L'));
      foreach($dataReader1 as $row1)
      {
        $pdf->row(array(
            date(Yii::app()->params['dateviewfromdb'], strtotime($row1['claimenterdate'])),
            date(Yii::app()->params['dateviewfromdb'], strtotime($row1['claimexitdate'])),
            Yii::app()->numberFormatter->format(Yii::app()->params["defaultnumberprice"],$row1['claimprice']),
            $row1['reason']));
      }

      $pdf->AddPage('P');
    }
    $pdf->Output('employeeclaim.pdf','D');
  }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Employeeclaim::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='employeeclaim-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
