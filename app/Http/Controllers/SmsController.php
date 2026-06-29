<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SmsController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Afficher le formulaire d'envoi de SMS
     */
    public function create()
    {
        return Inertia::render('Auth/Sms/Create');
    }

    /**
     * Envoyer un SMS
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string|max:160',
            'sendAt' => 'datetime',
        ]);

        $result = $this->smsService->sendSms(
            $request->phone_number,
            $request->message,
            $request->sendAt
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Envoyer des SMS groupés
     */
    public function sendBulk(Request $request)
    {
        $request->validate([
            'phone_numbers' => 'required|array',
            'phone_numbers.*' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        $result = $this->smsService->sendBulkSms(
            $request->phone_numbers,
            $request->message
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Vérifier le solde
     */
    public function getBalance()
    {
        $result = $this->smsService->getBalance();

        return response()->json($result);
    }
}
