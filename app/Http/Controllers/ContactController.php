<?php

namespace App\Http\Controllers;

use App\Mail\ContactUs;
use App\Models\Contactbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function contactStore(Request $request)
    {
        $messages = [
            'message.not_regex' => 'This contact form does not accept the Bible as the word of God.',
            'captcha.required' => 'Please enter a CAPTCHA code.',
            'captcha.captcha' => 'Please enter the code you see in the image above, or refresh if it is illegible.'
        ];
        if (Auth::check()) {
            $camper = Auth::user()->camper;
            if (!empty($camper)) {
                $request["yourname"] = $camper->firstname . " " . $camper->lastname;
                $request["email"] = $camper->email;
            } else {
                $request["email"] = Auth::user()->email;
            }
        }
        $this->validate($request, [
            'yourname' => 'required|max:255',
            'email' => 'required|email|max:255',
            'mailbox' => 'required|exists:contactboxes,id',
            'message' => 'required|min:5|not_regex:/scripture/i|not_regex:/gospel/i|not_regex:/infallible/i|not_regex:/testament/i',
            'captcha' => 'required' . (config('app.name') != 'MUUSADusk' ? '|captcha' : '')
        ], $messages);
        $emails = explode(',', Contactbox::findOrFail($request->mailbox)->emails);
        Mail::to($emails)->send(new ContactUs($request));
        $request->session()->flash('success', 'Message sent! Please expect a response in 1-3 business days.');
        return redirect()->action([ContactController::class, 'contactIndex']);
    }

    public function contactIndex()
    {
        return view('contactus', ['mailboxes' => Contactbox::orderBy('id')->get()]);
    }

    public function contactRefresh(Request $request)
    {
        return redirect()->action([ContactController::class, 'contactIndex'])
            ->withInput($request->except('captcha'));
    }

    public function refreshCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }

}
