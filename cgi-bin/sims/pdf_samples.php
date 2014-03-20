<?php

/*
##################################################
## START: CREATE PDF WITH IMAGE HEADER & FOOTER ##
##################################################
require('fpdf/fpdf.php');
//include_once('FX/FX.php');
//include_once('FX/server_data.php');

class PDF extends FPDF 
{
//Page header
function Header() 
{
	//Logo
	$this->Image('images/header-logo.jpg',5,5);
	//Arial bold 15
	$this->SetFont('Arial','B',15);
	//Move to the right
	//$this->Cell(80);
	//Title
	//$this->Cell(30,30,'Title',1,0,'C');
	//Line break
$this->Ln(20);
}

//Page footer
function Footer() 
{
	//Position at 1.5 cm from bottom
	$this->SetY(-15);
	//Arial italic 8
	$this->SetFont('Arial','I',8);
	//Page number
$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

//Instanciation of inherited class
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L');
$pdf->SetFont('Times','',12);
for($i=1;$i<=40;$i++)
$pdf->Cell(0,10,'Printing line number '.$i,0,1);
$pdf->Output();
################################################
## END: CREATE PDF WITH IMAGE HEADER & FOOTER ##
################################################


*/

/*
################################################################
## START: CREATE PDF WITH HEADER, FOOTER, AND MULTI-PAGE TEXT ##
################################################################
require('fpdf/fpdf.php');
//include_once('FX/FX.php');
//include_once('FX/server_data.php');

class PDF extends FPDF
{
function Header()
{
	global $title;
	
	//Arial bold 15
	$this->SetFont('Arial','B',15);
	//Calculate width of title and position
	$w=$this->GetStringWidth($title)+6;
	$this->SetX((210-$w)/2);
	//Colors of frame, background and text
	$this->SetDrawColor(0,80,180);
	$this->SetFillColor(230,230,0);
	$this->SetTextColor(220,50,50);
	//Thickness of frame (1 mm)
	$this->SetLineWidth(1);
	//Title
	$this->Cell($w,9,$title,1,1,'C',1);
	//Line break
	$this->Ln(10);
}

function Footer()
{
	//Position at 1.5 cm from bottom
	$this->SetY(-15);
	//Arial italic 8
	$this->SetFont('Arial','I',8);
	//Text color in gray
	$this->SetTextColor(128);
	//Page number
	$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}

function ChapterTitle($num,$label)
{
	//Arial 12
	$this->SetFont('Arial','',12);
	//Background color
	$this->SetFillColor(200,220,255);
	//Title
	$this->Cell(0,6,"Chapter $num : $label",0,1,'L',1);
	//Line break
	$this->Ln(4);
}

function ChapterBody($file)
{
	//Read text file
	$f=fopen($file,'r');
	$txt=fread($f,filesize($file));
	fclose($f);
	//Times 12
	$this->SetFont('Times','',12);
	//Output justified text
	$this->MultiCell(0,5,$txt);
	//Line break
	$this->Ln();
	//Mention in italics
	$this->SetFont('','I');
	$this->Cell(0,5,'(end of excerpt)');
}

function PrintChapter($num,$title,$file)
{
	$this->AddPage();
	$this->ChapterTitle($num,$title);
	$this->ChapterBody($file);
}
}

$pdf=new PDF();
$title='20000 Leagues Under the Seas';
$pdf->SetTitle($title);
$pdf->SetAuthor('Jules Verne');
$pdf->PrintChapter(1,'A RUNAWAY REEF','fpdf/tutorial/20k_c1.txt');
$pdf->PrintChapter(2,'THE PROS AND CONS','fpdf/tutorial/20k_c2.txt');
$pdf->Output();
##############################################################
## END: CREATE PDF WITH HEADER, FOOTER, AND MULTI-PAGE TEXT ##
##############################################################
*/

/*
#####################################################################
## START: CREATE PDF WITH HEADER, FOOTER, AND MULTIPLE COLUMN TEXT ##
#####################################################################
require('fpdf/fpdf.php');
//include_once('FX/FX.php');
//include_once('FX/server_data.php');

class PDF extends FPDF
{
//Current column
var $col=0;
//Ordinate of column start
var $y0;

function Header()
{
	//Page header
	global $title;
	
	$this->SetFont('Arial','B',15);
	$w=$this->GetStringWidth($title)+6;
	$this->SetX((210-$w)/2);
	$this->SetDrawColor(0,80,180);
	$this->SetFillColor(230,230,0);
	$this->SetTextColor(220,50,50);
	$this->SetLineWidth(1);
	$this->Cell($w,9,$title,1,1,'C',1);
	$this->Ln(10);
	//Save ordinate
	$this->y0=$this->GetY();
}

function Footer()
{
	//Page footer
	$this->SetY(-15);
	$this->SetFont('Arial','I',8);
	$this->SetTextColor(128);
	$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}

function SetCol($col)
{
	//Set position at a given column
	$this->col=$col;
	$x=10+$col*65;
	$this->SetLeftMargin($x);
	$this->SetX($x);
}

function AcceptPageBreak()
{
	//Method accepting or not automatic page break
	if($this->col<2)
	{
		//Go to next column
		$this->SetCol($this->col+1);
		//Set ordinate to top
		$this->SetY($this->y0);
		//Keep on page
		return false;
	}
	else
	{
		//Go back to first column
		$this->SetCol(0);
		//Page break
		return true;
}
}

function ChapterTitle($num,$label)
{
	//Title
	$this->SetFont('Arial','',12);
	$this->SetFillColor(200,220,255);
	$this->Cell(0,6,"Chapter $num : $label",0,1,'L',1);
	$this->Ln(4);
	//Save ordinate
	$this->y0=$this->GetY();
}

function ChapterBody($fichier)
{
	//Read text file
	$f=fopen($fichier,'r');
	$txt=fread($f,filesize($fichier));
	fclose($f);
	//Font
	$this->SetFont('Times','',12);
	//Output text in a 6 cm width column
	$this->MultiCell(60,5,$txt);
	$this->Ln();
	//Mention
	$this->SetFont('','I');
	$this->Cell(0,5,'(end of excerpt)');
	//Go back to first column
	$this->SetCol(0);
}

function PrintChapter($num,$title,$file)
{
	//Add chapter
	$this->AddPage();
	$this->ChapterTitle($num,$title);
	$this->ChapterBody($file);
}
}

$pdf=new PDF();
$title='20000 Leagues Under the Seas';
$pdf->SetTitle($title);
$pdf->SetAuthor('Jules Verne');
$pdf->PrintChapter(1,'A RUNAWAY REEF','fpdf/tutorial/20k_c1.txt');
$pdf->PrintChapter(2,'THE PROS AND CONS','fpdf/tutorial/20k_c2.txt');
$pdf->Output();
###################################################################
## END: CREATE PDF WITH HEADER, FOOTER, AND MULTIPLE COLUMN TEXT ##
###################################################################
*/

/*
###################################
## START: CREATE PDF WITH TABLES ##
###################################
require('fpdf/fpdf.php');
//include_once('FX/FX.php');
//include_once('FX/server_data.php');

class PDF extends FPDF
{
//Load data
function LoadData($file)
{
	//Read file lines
	$lines=file($file);
	$data=array();
	foreach($lines as $line)
		$data[]=explode(';',chop($line));
	return $data;
}

//Simple table
function BasicTable($header,$data)
{
	//Header
	foreach($header as $col)
		$this->Cell(40,7,$col,1);
	$this->Ln();
	//Data
	foreach($data as $row)
	{
		foreach($row as $col)
			$this->Cell(40,6,$col,1);
		$this->Ln();
}
}

//Better table
function ImprovedTable($header,$data)
{
	//Column widths
	$w=array(40,35,40,45);
	//Header
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],7,$header[$i],1,0,'C');
	$this->Ln();
	//Data
	foreach($data as $row)
	{
		$this->Cell($w[0],6,$row[0],'LR');
		$this->Cell($w[1],6,$row[1],'LR');
		$this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
		$this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
		$this->Ln();
	}
	//Closure line
	$this->Cell(array_sum($w),0,'','T');
}

//Colored table
function FancyTable($header,$data)
{
	//Colors, line width and bold font
	$this->SetFillColor(255,0,0);
	$this->SetTextColor(255);
	$this->SetDrawColor(128,0,0);
	$this->SetLineWidth(.3);
	$this->SetFont('','B');
	//Header
	$w=array(40,35,40,45);
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],7,$header[$i],1,0,'C',1);
	$this->Ln();
	//Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	//Data
	$fill=0;
	foreach($data as $row)
	{
		$this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
		$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
		$this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
		$this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
		$this->Ln();
		$fill=!$fill;
}
$this->Cell(array_sum($w),0,'','T');
}
}

$pdf=new PDF();
//Column titles
$header=array('Country','Capital','Area (sq km)','Pop. (thousands)');
//Data loading
$data=$pdf->LoadData('fpdf/tutorial/countries.txt');
$pdf->SetFont('Arial','',14);
$pdf->AddPage();
$pdf->BasicTable($header,$data);
$pdf->AddPage();
$pdf->ImprovedTable($header,$data);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output();
#################################
## END: CREATE PDF WITH TABLES ##
#################################
*/


#################################################
## START: CREATE PDF WITH LINKS & FLOWING TEXT ##
#################################################
require('fpdf/fpdf.php');
//include_once('FX/FX.php');
//include_once('FX/server_data.php');

class PDF extends FPDF
{
var $B;
var $I;
var $U;
var $HREF;

function PDF($orientation='P',$unit='mm',$format='A4')
{
	//Call parent constructor
	$this->FPDF($orientation,$unit,$format);
	//Initialization
	$this->B=0;
	$this->I=0;
	$this->U=0;
	$this->HREF='';
}

function WriteHTML($html)
{
	//HTML parser
	$html=str_replace("\n",' ',$html);
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			//Text
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			else
				$this->Write(5,$e);
		}
		else
		{
			//Tag
			if($e{0}=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				//Extract attributes
				$a2=explode(' ',$e);
				$tag=strtoupper(array_shift($a2));
				$attr=array();
				foreach($a2 as $v)
					if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
						$attr[strtoupper($a3[1])]=$a3[2];
				$this->OpenTag($tag,$attr);
			}
	}
}
}

function OpenTag($tag,$attr)
{
	//Opening tag
	if($tag=='B' or $tag=='I' or $tag=='U')
		$this->SetStyle($tag,true);
	if($tag=='A')
		$this->HREF=$attr['HREF'];
	if($tag=='BR')
		$this->Ln(5);
}

function CloseTag($tag)
{
	//Closing tag
	if($tag=='B' or $tag=='I' or $tag=='U')
		$this->SetStyle($tag,false);
	if($tag=='A')
		$this->HREF='';
}

function SetStyle($tag,$enable)
{
	//Modify style and select corresponding font
	$this->$tag+=($enable ? 1 : -1);
	$style='';
	foreach(array('B','I','U') as $s)
		if($this->$s>0)
			$style.=$s;
	$this->SetFont('',$style);
}

function PutLink($URL,$txt)
{
	//Put a hyperlink
	$this->SetTextColor(0,0,255);
	$this->SetStyle('U',true);
	$this->Write(5,$txt,$URL);
	$this->SetStyle('U',false);
	$this->SetTextColor(0);
}
}

$html='You can now easily print text mixing different
styles : <B>bold</B>, <I>italic</I>, <U>underlined</U>, or
<B><I><U>all at once</U></I></B>!<BR>You can also insert links
on text, such as <A HREF="http://www.fpdf.org">www.fpdf.org</A>,
or on an image: click on the logo.';

$pdf=new PDF();
//First page
$pdf->AddPage();
$pdf->SetFont('Arial','',20);
$pdf->Write(5,'To find out what\'s new in this tutorial, click ');
$pdf->SetFont('','U');
$link=$pdf->AddLink();
$pdf->Write(5,'here',$link);
$pdf->SetFont('');
//Second page
$pdf->AddPage();
$pdf->SetLink($link);
$pdf->Image('fpdf/tutorial/logo.png',10,10,30,0,'','http://www.fpdf.org');
$pdf->SetLeftMargin(45);
$pdf->SetFontSize(14);
$pdf->WriteHTML($html);
$pdf->Output();
###############################################
## END: CREATE PDF WITH LINKS & FLOWING TEXT ##
###############################################

?>
