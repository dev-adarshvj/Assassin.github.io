<?php defined('C5_EXECUTE') or die(_("Access Denied."));
use Concrete\Package\ProfessionalManagement\Src\MailExtended\MailService;
use Concrete\Package\ProfessionalManagement\Src\MailDatas;
use stdClass;
require_once ('tcpdf.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(400, 300), true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(Config::get('concrete.PDF_TITLE'));
$pdf->SetTitle(Config::get('concrete.PDF_TITLE'));
$pdf->SetSubject(Config::get('concrete.PDF_TITLE'));
$pdf->SetKeywords(Config::get('concrete.PDF_TITLE'));

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
//$pdf->setFooterData(array(0,64,0), array(0,64,128));
// set header and footer fonts
 $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
 $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
 $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once (dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
// set default font subsetting mode
$pdf->setFontSubsetting(true);
// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('times', '', 13, 30, true);
// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();
// set text shadow effect
$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));
$html =  <<<EOF
<style>
#prod-wrapper{color:#339fc6 !important; font-family:Arial, Helvetica, sans-serif !important;width: 100%;}
table{color:#339fc6;}
a{ cursor:pointer;}

.third {width:30%;}

div {float:left;}

.f-left{float:left;}

.f-right{float:right;}

.center{text-align:center;}

.block{display:block;}

.grad{ background: #e5e5e5; }

.prod-dialog-form lable{ 
width:150px; 
display:block; }

.hidden{ 
display:none !important;}
.min-t{font-size:12px;}
.min-t span{
width:40px;
display:inline-block;
}
h2{color:#b2d136; 
margin:0 0 10px 0;}
.info-column{
padding:10px 1%; 
height:185px; margin: 0 0.5%;}
.info-column p{font-size:12px;}
table#expense-table{
margin-top:40px;
border-spacing: 0px;
 border:1px solid #caecf8;
  padding:1px;
  font-size:12px;  }
td,th{ }
#expense-table th{
text-align:left; 
background:#cedee4; 
color:#1a90ba;
font-size:14px;}
#expense-table th:nth-child(even){ 
background:#e2eaed;}
#expense-table tr, #expense-table tr td{
border-bottom:1px solid #becbd0;
border-top:1px solid #becbd0;}
#expense-table tr td:nth-child(odd){ background:#e3eef2; }
.prod-bt{
color:white; 
background:#b1d038; 
border-radius:5px; 
font-weight:bold; 
padding:5px 10px;}
#nav-container a{
display:block;
float:left;
color:white;
font-size:18px;
padding:10px;}
#t-info p{ margin:5px 0;}
#t-info td>:nth-child(2){ margin:5px 0 10px 0;}
tr, td { border-bottom: 1px solid #becbd0; }
}
</style>
EOF;
$html =  $pdfcontent;

 //$html = '<h1>haiii</h1>';
//echo $html;exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// $pdf->AddPage();
// $pdf->writeHTMLCell(0, 0, '', '', $pddftable_image, 0, 1, 0, true, '', true);
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdfName = 'ndta_'.$employID.'_export_'.date("m-d-Y").'.pdf';
$pdfName = strtolower($pdfName);
ob_clean();
//$pdf->Output($pdfName, 'I');exit;
$FileNamePath = __DIR__ . '/pdf_download/' . $pdfName;
//Save the document to the server
$pdf->Output(__DIR__ . '/pdf_download/' . $pdfName, 'F');
$mh = new MailService();
$mh->to($employMail);
$mh->from('ndta@shaw.ca');
$mh->setSubject('Pro-D Summary from NDTA');
$bodyHtml = "<p>Please find your Pro-D Funds Summary attached as a PDF.</p><br /><p>If you have any problems opening this file, please first check to see if you have Adobe Acrobat Reader installed. It\'s a free software and can be downloaded from Adobe if you don\'t already have it on your system. </p>   
<p>Download Acrobat Reader (if you don\'t already have it installed) : 
<a href = 'http://get.adobe.com/reader/' > http://get.adobe.com/reader/</a><br />
----------</p ><br /><p > Nanaimo District Teachers� Association <br />
P 250.756.1237 <br />
F 250.756.0188 </p >";
$mh->setBodyHtml($bodyHtml);

if (file_exists(__DIR__ . '/pdf_download/' . $pdfName)) {
    $afiles = array();
    $pdffilepath = __DIR__ . '/pdf_download/' . $pdfName;
    $pdffileurl = __DIR__ . '/pdf_download/' . $pdfName;
    $afiles[0]['path'] = $pdffilepath;
    $afiles[0]['mime'] = 'application/pdf';
    $afiles[0]['name'] = basename($pdffilepath);
    $mh->addAttachments($afiles);
}
if ($employMail != '') {
   @$mh->sendMail();
$msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>( '. $teacher_name .' )</strong>  Mail sent successfully!!</div>';
}else{ $msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Can\'t sent.</strong></div>'; }
array_map( 'unlink', array_filter((array) glob(__DIR__ . '/pdf_download/*') ) );
if($status == 'single_mail'){
    $response = new \Stdclass;
    $response = $msg;
    echo json_encode($response);
    exit(); }
//============================================================+
// END OF FILE
//============================================================+
