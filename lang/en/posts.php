<?php

/*
| Static content: will be replaced by the translatable Post model.
| Keyed by slug. `pillar` is the pillar id (see blog.pillars).
| The home Writing section is only shown with 3 or more articles.
*/

return [
    'items' => [
        'git-github-gitlab' => [
            'pillar' => 'tech', 'date' => null, 'reading_time' => null, 'featured' => true,
            'title' => 'Git, GitHub and GitLab: the updated guide',
            'summary' => '[Article summary: 2 or 3 lines about what the reader will learn.]',
            'code' => "$ git switch -c feat/registro\n$ git commit -m \"feat: perfil único\"\n$ git push origin feat/registro",
        ],
        'claude-code-proyecto-real' => ['pillar' => 'tech', 'date' => null, 'title' => 'Claude Code on a real project', 'summary' => '[2-line summary]'],
        'ci-cd-github-actions' => ['pillar' => 'tech', 'date' => null, 'title' => 'From zero to production: CI/CD with GitHub Actions', 'summary' => '[2-line summary]'],
        'tecno-d-2-0' => ['pillar' => 'business', 'date' => null, 'title' => '11 years running Tecno D 2.0: what I learned', 'summary' => '[2-line summary]'],
        'programar-con-un-nino' => ['pillar' => 'mama', 'date' => null, 'title' => 'Coding with a toddler at home', 'summary' => '[2-line summary]'],
        'pagos-recurrentes-saas' => ['pillar' => 'tech', 'date' => null, 'title' => 'Recurring payments in a SaaS: what nobody tells you', 'summary' => '[2-line summary]'],
        'cofundar-una-agencia' => ['pillar' => 'business', 'date' => null, 'title' => 'Co-founding an agency with partners', 'summary' => '[2-line summary]'],
        'aws-o-digitalocean' => ['pillar' => 'tech', 'date' => null, 'title' => 'AWS or DigitalOcean: how I choose for each project', 'summary' => '[2-line summary]'],
        'startup-weekend-maracay' => ['pillar' => 'mama', 'date' => null, 'title' => 'What I learned organizing Startup Weekend Maracay', 'summary' => '[2-line summary]'],
    ],
];
