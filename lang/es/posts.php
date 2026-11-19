<?php

/*
| Contenido estático: se reemplazará por el modelo Post con traducciones.
| Indexado por slug. `pillar` es el id del pilar (ver blog.pillars).
| La sección Contenido de la home solo se muestra con 3 o más artículos.
*/

return [
    'items' => [
        'git-github-gitlab' => [
            'pillar' => 'tech', 'date' => null, 'reading_time' => null, 'featured' => true,
            'title' => 'Git, GitHub y GitLab: la guía, actualizada',
            'summary' => '[Resumen del artículo: 2 o 3 líneas sobre qué aprende quien lo lee.]',
            'code' => "$ git switch -c feat/registro\n$ git commit -m \"feat: perfil único\"\n$ git push origin feat/registro",
        ],
        'claude-code-proyecto-real' => ['pillar' => 'tech', 'date' => null, 'title' => 'Claude Code en un proyecto real', 'summary' => '[Resumen de 2 líneas]'],
        'ci-cd-github-actions' => ['pillar' => 'tech', 'date' => null, 'title' => 'De cero a producción: CI/CD con GitHub Actions', 'summary' => '[Resumen de 2 líneas]'],
        'tecno-d-2-0' => ['pillar' => 'business', 'date' => null, 'title' => '11 años dirigiendo Tecno D 2.0: lo que aprendí', 'summary' => '[Resumen de 2 líneas]'],
        'programar-con-un-nino' => ['pillar' => 'mama', 'date' => null, 'title' => 'Programar con un niño pequeño en casa', 'summary' => '[Resumen de 2 líneas]'],
        'pagos-recurrentes-saas' => ['pillar' => 'tech', 'date' => null, 'title' => 'Pagos recurrentes en un SaaS: lo que no te cuentan', 'summary' => '[Resumen de 2 líneas]'],
        'cofundar-una-agencia' => ['pillar' => 'business', 'date' => null, 'title' => 'Cofundar una agencia con socios', 'summary' => '[Resumen de 2 líneas]'],
        'aws-o-digitalocean' => ['pillar' => 'tech', 'date' => null, 'title' => 'AWS o DigitalOcean: cómo elijo para cada proyecto', 'summary' => '[Resumen de 2 líneas]'],
        'startup-weekend-maracay' => ['pillar' => 'mama', 'date' => null, 'title' => 'Lo que aprendí organizando Startup Weekend Maracay', 'summary' => '[Resumen de 2 líneas]'],
    ],
];
