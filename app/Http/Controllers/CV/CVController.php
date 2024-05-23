<?php

namespace App\Http\Controllers\CV;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BaseController;
use App\Http\Requests\CVRequest;
use App\Models\CV;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\PDF as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CVController extends BaseController
{
    public function create(Request $request)
    {
        $cvRequest = new CVRequest();
        $validator = Validator::make($request->all(), $cvRequest->rules());

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $request['profile_image'] = (new RegisterController)->get_image($request, "CV");

        $data = [
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'major' => $request->major,
            'link'  => $request->link,
            'summary' => $request->summary,
            'skills' => $request->skills,
            'certificates' => $request->certificates,
            'educations' => $request->educations,
            'experiences' => $request->experiences,
            'country' => $request->country,
            'birth_date' => $request->birth_date,
            'profile_image' => $request->profile_image,
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('cv_template', compact('data'))
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
        return $this->sendResponse(['cv' => $path . $filename]);
    }
}
