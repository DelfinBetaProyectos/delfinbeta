<x-mail::message>
# Mensaje de Contacto

Usted a recibido un mensaje desde el website con los siguientes datos:

Persona contacto: {{ $message->fullName }}

Email: {{ $message->email }}

Teléfono: {{ $message->phone }}

Mensaje: 

{{ $message->content }}

{{ config('app.name') }}
</x-mail::message>
