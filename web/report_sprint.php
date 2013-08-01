<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mark
 * Date: 7/30/13
 * Time: 8:01 PM
 * To change this template use File | Settings | File Templates.
 */

require('fpdf17/fpdf.php');
/*
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");
*/

//Variables needed to be passed into this script
$Project="Project";
$SprintNum=1;
$opt=1; //0=Excel, 1=pdf
// Column headings
$header = array('TeamMate Name', 'Item Name', 'Priority Level', 'eta/points');
// Data loading
$data = array('Procrastinate,Blah,0,1','Balance,Blah,2,2','Suite,Blah,3,3','quid,Blah,4,4');//$pdf->LoadData('countries.txt');

if($opt){
    $file='./reports/out.pdf';

    class PDF extends FPDF{
        // Page header
        function Header()
        {
            // Logo
            $this->Image('models/site-templates/images/logo.png',10,6,30);
            // Arial bold 15
            $this->SetFont('Arial','BU',14);
            // Move to the right
            //$this->Cell(80);
            // Title
            $this->Cell(0,0,'Sprint Work Assignment Report',0,0,'C');
            // Line break
            $this->Ln(20);
        }

        // Page footer
        function Footer()
        {
            // Position at 1.5 cm from bottom
            $this->SetY(-25);
            // Arial italic 8
            $this->SetFont('Arial','I',8);
            // Page number
            $this->Cell(0,10,'Page '.$this->PageNo().'/{nb} - '.date("F j, Y"),0,0,'C');
        }

        function FancyTable($header, $data){
            // Colors, line width and bold font
            $this->SetFillColor(255,0,0);
            $this->SetTextColor(255);
            $this->SetDrawColor(128,0,0);
            $this->SetLineWidth(.3);
            $this->SetFont('','B');
            // Header
            $w = array(50, 50, 50, 50);
            for($i=0;$i<count($header);$i++)
                $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
            $this->Ln();
            // Color and font restoration
            $this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont('');
            // Data
            $fill = false;
            for($i=0;$i<count($data);$i++){
                $pieces=explode(",",$data[$i]);
                $this->Cell($w[0],6,$pieces[0],1,0,'L',$fill);
                $this->Cell($w[1],6,$pieces[1],1,0,'L',$fill);
                $this->Cell($w[2],6,$pieces[2],1,0,'C',$fill);
                $this->Cell($w[3],6,$pieces[3],1,0,'C',$fill);
                $this->Ln();
                $fill = !$fill;
            }
            // Closing line
            $this->Cell(array_sum($w),0,'','T');
        }
    }

    $pdf = new PDF();
    $pdf->SetFont('Arial','',14);
    $pdf = new PDF('L','mm','Letter');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->FancyTable($header,$data);
    $pdf->Output($file);
}else{ //excel

}




?>