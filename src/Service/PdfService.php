<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
   
    private $domPdf;

    public function __construct() {
        ob_end_clean();
        $this->domPdf = new Dompdf();
        $this->domPdf->setPaper('A4', 'portrait');
        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont( 'Garamond');
        $pdfOptions->setIsRemoteEnabled(true);
        $this->domPdf->setOptions($pdfOptions);
    }

    public function showPdfFile($html, $filename) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream($filename.".pdf");
    }

    public function generateBinaryPDF($html) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->setPaper('A4', 'portrait');
        $this->domPdf->render();
        $this->domPdf->output();
    }
}