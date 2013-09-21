<?php

class ReprekonbankController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
    protected $menuname = 'reprekonbank';

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
	 * Lists all models.
	 */
	public function actionIndex()
	{
            parent::actionIndex();
			if (isset($_POST['startperiod']) && isset($_POST['endperiod']) && isset($_POST['accountid']))
      {
        $this->pdf->title='Bank Reconciliation Report';
	  $this->pdf->AddPage('L');
		$this->pdf->iscustomborder = false;
		$this->pdf->isneedpage = true;
		$connection=Yii::app()->db;
		$sql = "
		select *
		from
		(
			select cashbankno, transdate, description, pono, supplier, sono, customer, accountcode, accountname, symbol, 
				case when debit < credit then credit-debit else 0 end as debit, case when debit < credit then credit-debit else 0 end as credit,currencyrate
			from 
			(
			select '' as cashbankno, date_sub('". $_POST['startperiod']. "', interval 1 day) as transdate,
			'Mutation' as description,'' as pono,'' as supplier,'' as sono,'' as customer,'' as accountcode,'' as accountname,'' as symbol,
			(select ifnull(sum(ifnull(debit,0)),0)
				from genledger 
				where date(postdate) < '".$_POST['startperiod']."') as debit, 
				(select ifnull(sum(ifnull(credit,0)),0)
				from genledger 
				where date(postdate) < '".$_POST['startperiod']."') as credit,
				0 as currencyrate
			) z1
			
			union
			
			select a.cashbankno,a.transdate,a.description,'','','','',f.accountcode,f.accountname,g.symbol,a.amount,0,a.currencyrate
from cashbank a
left join account f on f.accountid = a.accountid
left join currency g on g.currencyid = a.currencyid
where a.cashbanktypeid = 1 and a.accountid = ".$_POST['accountid']." 
and a.transdate between '". $_POST['startperiod']. "' and '". $_POST['endperiod']."' and a.recordstatus > 1

			union
			
			select a.cashbankno,a.transdate,a.description,'','','','',f.accountcode,f.accountname,g.symbol,0,a.amount,a.currencyrate
from cashbank a
left join account f on f.accountid = a.accountid
left join currency g on g.currencyid = a.currencyid
where a.cashbanktypeid = 2 and a.accountid = ".$_POST['accountid']." 
and a.transdate between '". $_POST['startperiod']. "' and '". $_POST['endperiod']."' and a.recordstatus > 1

			union
			
			select a.cashbankno,a.transdate,a.description,d.pono,e.fullname,'', '', f.accountcode,f.accountname,g.symbol,b.debit,b.credit,b.currencyrate
from cashbankacc b
left join cashbank a on a.cashbankid = b.cashbankid
left join invoice c on c.invoiceid = a.invoiceid
left join poheader d on d.poheaderid = c.poheaderid
left join addressbook e on e.addressbookid = d.addressbookid
left join account f on f.accountid = b.accountid
left join currency g on g.currencyid = a.currencyid
where b.accountid = ".$_POST['accountid']." 
and a.transdate between '". $_POST['startperiod']. "' and '". $_POST['endperiod']."'

union

select a.cashbankno,a.transdate,a.description,'','',d.sono,e.fullname,f.accountcode,f.accountname,g.symbol,b.debit,b.credit,b.currencyrate
from cashbankacc b
left join cashbank a on a.cashbankid = b.cashbankid
left join invoice c on c.invoiceid = a.invoiceid
left join soheader d on d.soheaderid = c.soheaderid
left join addressbook e on e.addressbookid = d.addressbookid
left join account f on f.accountid = b.accountid
left join currency g on g.currencyid = a.currencyid
where b.accountid = ".$_POST['accountid']." 
and a.transdate between '". $_POST['startperiod']. "' and '". $_POST['endperiod']."'
			
			
			) zz
			order by transdate

";
		$command=$this->connection->createCommand($sql);
		$dataReader=$command->queryAll();
		$this->pdf->Cell(0,15,'PERIODE : '. date(Yii::app()->params['dateviewfromdb'], strtotime($_POST['startperiod'])) . 
				' Up To '.date(Yii::app()->params['dateviewfromdb'], strtotime($_POST['endperiod'])),0,0,'C');
      $this->pdf->SetY($this->pdf->gety()+25);
		$this->pdf->setFont('Arial','B',8);
      $this->pdf->colalign = array('C','C','C','C','C','C','C','C','C');
      $this->pdf->setwidths(array(20,25,50,30,30,25,30,30,25,25));
	  $this->pdf->colheader = array(
		'Date',
		'Voucher',
		'Description',
		'PO No',
		'SO No',
		'Account',
		'Debit',
		'Credit',
		'Saldo'
		);
      $this->pdf->RowHeader();
      $this->pdf->coldetailalign = array('L','L','L','L','L','C','R','R','R');
	  $totaldebit = 0;$i=0;$totalcredit=0;$total=0;$symbol = '';
		foreach($dataReader as $row)
          {
		$this->pdf->setFont('Arial','B',12);
		  $i+=1;
		$this->pdf->setFont('Arial','',8);
$this->pdf->Row(array(
		date(Yii::app()->params['dateviewfromdb'], strtotime($row['transdate'])),
		$row['cashbankno'],
		$row['description'],
		$row['pono'],
		$row['sono'],
		$row['accountcode'],
		Yii::app()->numberFormatter->formatCurrency($row['debit'],$row['symbol']),
		Yii::app()->numberFormatter->formatCurrency($row['credit'],$row['symbol']),
			''
		));
			  $totaldebit += ($row['debit']*$row['currencyrate']);
			  $totalcredit += ($row['credit']*$row['currencyrate']);
			  $symbol = $row['symbol'];
		$this->pdf->CheckPageBreak(0);
		  		  }
				  $total = $totaldebit-$totalcredit;
				  $this->pdf->Row(array(
		'',
		'',
		'',
		'',
		'',
		'Total',
		Yii::app()->numberFormatter->formatCurrency($totaldebit,$symbol),
		Yii::app()->numberFormatter->formatCurrency($totalcredit,$symbol),	
		Yii::app()->numberFormatter->formatCurrency($total,$symbol),	
		));
		      $this->pdf->text(12,$this->pdf->gety()+10,'NOTE : Print Report as per Voucher Date and Account Name');
          $this->pdf->Output();
	  }
	  else
	  {
		$this->render('index');
	  }
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Genledger::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='genledger-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
