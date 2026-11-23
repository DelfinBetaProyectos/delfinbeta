<?php

return [
    'brand_tagline' => 'Formulario de contacto',
    'location' => 'Maracay, Venezuela',

    'received' => [
        'subject' => '[:tag] Nuevo mensaje de :name · delfinbeta.tech',
        'tags' => [
            'project' => 'Proyecto',
            'talk' => 'Charla',
            'role' => 'Rol remoto',
            'other' => 'Otro tema',
        ],
        'locales' => [
            'es' => 'español',
            'en' => 'inglés',
        ],
        'preheader' => 'Responder antes de :hours horas',
        'topic_badge' => 'Motivo · :topic',
        'title' => 'Nuevo mensaje de :name',
        'meta' => ':date (UTC-4) · Versión en :locale',
        'name' => 'Nombre',
        'email' => 'Correo',
        'topic' => 'Motivo',
        'project_topic' => ':topic (lo atiende AraguaneyCode)',
        'page' => 'Página',
        'message' => 'Mensaje',
        'reply' => 'Responder a :name',
        'project_note' => 'Este mensaje es una oportunidad para la agencia. Se envió copia a :email para que el equipo agende el diagnóstico.',
        'footer' => 'Enviado automáticamente por el formulario de delfinbeta.tech · ID :reference',
    ],

    'confirmation' => [
        'subject' => 'Recibí tu mensaje, :name',
        'preheader' => 'Gracias por escribirme. Te respondo pronto.',
        'greeting' => 'Hola, :name:',
        'intro' => 'Gracias por escribirme. Recibí tu mensaje y te respondo personalmente en menos de :hours horas.',
        'summary' => 'Esto es lo que me enviaste:',
        'topic' => 'Motivo',
        'message' => 'Mensaje',
        'project_note' => 'Como me escribes por un proyecto, también lo compartí con mi equipo en :agency, donde construimos software a medida e IA para negocios. Te contactaremos desde ahí con los siguientes pasos.',
        'meanwhile' => 'Mientras tanto, puedes ver en qué estoy trabajando:',
        'projects' => 'Ver mis proyectos',
        'blog' => 'Leer el blog',
        'role' => 'Arquitecta de soluciones cloud y desarrolladora full stack · Cofundadora de AraguaneyCode',
        'footer' => 'Recibes este correo porque escribiste en el formulario de :site. No te agregué a ninguna lista.',
    ],

    'notification' => [
        'greeting' => 'Hola, :name:',
        'default_greeting' => 'Hola:',
        'salutation' => 'Saludos,',
        'trouble' => 'Si el botón ":action" no funciona, copia y pega este enlace en tu navegador:',
        'footer' => 'Recibes este correo por tu cuenta en :site.',
    ],

    'preview' => [
        'message' => "Hola Dayan,\nTe escribo porque me gustaría conversar contigo.\n¡Gracias!",
    ],
];
