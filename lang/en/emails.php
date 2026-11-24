<?php

return [
    'brand_tagline' => 'Contact form',
    'location' => 'Maracay, Venezuela (UTC-4)',

    'received' => [
        'subject' => '[:tag] New message from :name · delfinbeta.tech',
        'tags' => [
            'project' => 'Project',
            'talk' => 'Talk',
            'role' => 'Remote role',
            'other' => 'Other',
        ],
        'locales' => [
            'es' => 'Spanish',
            'en' => 'English',
        ],
        'preheader' => 'Reply within :hours hours',
        'topic_badge' => 'Topic · :topic',
        'title' => 'New message from :name',
        'meta' => ':date (UTC-4) · :locale version',
        'name' => 'Name',
        'email' => 'Email',
        'topic' => 'Topic',
        'project_topic' => ':topic (handled by AraguaneyCode)',
        'page' => 'Page',
        'message' => 'Message',
        'reply' => 'Reply to :name',
        'project_note' => 'This message is an opportunity for the agency. A copy was sent to :email so the team can schedule the assessment.',
        'footer' => 'Sent automatically by the delfinbeta.tech contact form · ID :reference',
    ],

    'confirmation' => [
        'subject' => 'Got your message, :name',
        'preheader' => 'Thanks for reaching out. I will reply soon.',
        'greeting' => 'Hi :name,',
        'intro' => 'Thanks for reaching out. I got your message and will reply personally within :hours hours.',
        'summary' => 'Here is what you sent:',
        'topic' => 'Topic',
        'message' => 'Message',
        'project_note' => 'Since this is about a project, I also shared it with my team at :agency, where we build custom software and AI for businesses. We will follow up from there with next steps.',
        'meanwhile' => 'In the meantime, here is what I am working on:',
        'projects' => 'See my projects',
        'blog' => 'Read the blog',
        'role' => 'Cloud Solutions Architect & Full Stack Developer · Co-founder of AraguaneyCode',
        'footer' => 'You are receiving this because you used the contact form on :site. You have not been added to any list.',
    ],

    'notification' => [
        'greeting' => 'Hi :name,',
        'default_greeting' => 'Hi,',
        'salutation' => 'Regards,',
        'trouble' => 'If the ":action" button does not work, copy and paste this link into your browser:',
        'footer' => 'You are receiving this email because of your account on :site.',
    ],

    'preview' => [
        'message' => "Hi Dayan,\nI am writing because I would like to talk with you.\nThanks!",
    ],

    'report' => [
        'subject' => 'Monthly report for :month · delfinbeta.tech',
        'tagline' => 'Monthly report',
        'preheader' => 'The :month figures against the 90-day plan goals',
        'title' => 'Report for :month',
        'intro' => 'The PDF report with this month\'s figures against the 90-day plan goals is attached. It is a good time to enter the goals tracked by hand.',
        'highlights' => 'In short: :visits visits, :messages form messages and :downloads resume downloads.',
        'footer' => 'You get this report on the first Sunday of each month as the administrator of :site.',
        'action' => 'Open the dashboard',
    ],
];
