<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Response;

class ResumeController extends Controller
{
    /**
     * Generate and stream the official PDF Resume for Cedric James Leala.
     */
    public function download(): Response
    {
        /** @var PDF $pdf */
        $pdf = app('dompdf.wrapper');

        $pdf->loadView('pdf.resume');
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Cedric-James-Leala-Resume.pdf');
    }
}
