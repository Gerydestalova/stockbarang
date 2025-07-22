<?php
require __DIR__ . '/vendor/autoload.php';



$mpdf = new \mPDF();

$mpdf->WriteHTML('<h1>Hello World!</h1>');
$mpdf->Output();
