<?php

namespace App\Http\Controllers;

use App\Models\GrantAccess;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\User;

class DoctorController extends Controller
{
    public function patientList()
    {
        // Fetch patients who have granted access to the authenticated doctor
        $patients = GrantAccess::where('authorized_id', Auth::id())
            ->where('role_type', 'doctor')
            ->with('patient')
            ->get();

        return view('doctor.patient_list', compact('patients'));
    }

    public function patientDetails($id)
    {
        // Fetch the patient details by ID
        $patient = UserModel::findOrFail($id);

        return view('doctor.patient_details', compact('patient'));
    }
}