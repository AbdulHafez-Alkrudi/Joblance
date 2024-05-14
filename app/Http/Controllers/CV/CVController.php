<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\CV;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\PDF as PDF;
use Illuminate\Support\Facades\Auth;

class CVController extends BaseController
{
    public function create(Request $request)
    {
        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'education' => [
                'degree' => 'phd',
                'institution' => 'du',
                'year' => '2023'
            ]
        ]; // Your data array

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('cv_template', [
            'name'  => $request->name,
            'email' => $request->email,
            'education' => [
                'degree' => 'phd',
                'institution' => 'du',
                'year' => '2023'
            ]
        ])
            ->setPaper('a4', 'portrait')
            ->setWarnings(false);

        // Define the path where you want to save the PDF
        $path = public_path('files/CVs/');
        $filename = time().'.pdf';

        // Save the PDF to the server
        $pdf->save($path . $filename);

        // Save the PDF to the database
        CV::create([
            'user_id' => Auth::id(),
            'cv'      => 'files/CVs/' . $filename,
        ]);

        // Return a response indicating success
        return $this->sendResponse();
    }
}
