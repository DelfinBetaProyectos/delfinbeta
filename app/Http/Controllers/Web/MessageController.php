<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Message;
// use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'message' => 'required|string'
        ], [
            'firstname.required' => 'El Nombre es Obligatorio',
            'firstname.max' => 'El Nombre debe ser de máximo 255 caracteres',
            'lastname.required' => 'El Apellido es Obligatorio',
            'lastname.max' => 'El Apellido debe ser de máximo 255 caracteres',
            'email.required' => 'El Email es Obligatorio',
            'email.email' => 'El Email no tiene el formato correcto',
            'message.required' => 'El Mensaje es Obligatorio',
        ]);
 
        if ($validator->fails()) {
            return redirect()->to(route('welcome').'#contact')
                             ->withErrors($validator)
                             ->withInput();
        }
 
        // Retrieve the validated input...
        $validated = $validator->validated();

        $message = Message::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'content' => $validated['message']
        ]);

        // Mail::to('arifloresal@gmail.com', 'Adriana Flores')->send(new ContactMail($mailMessage));

        return redirect()->to(route('welcome').'#contact')
                         ->with('message', 'Mensaje enviado con éxito. Gracias por contactarme, pronto recibirás mi respuesta.');
    }
}
