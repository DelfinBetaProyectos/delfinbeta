<?php

namespace Database\Seeders;

use App\Enums\ArticleCategory;
use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the planned articles (formerly static in lang/{es,en}/posts.php) as drafts to write from the dashboard.
     * Drafts keep the blog hidden. Safe to re-run: existing articles (matched by slug) are never overwritten.
     */
    public function run(): void
    {
        foreach ($this->articles() as $article) {
            Article::firstOrCreate(['slug' => $article['slug']], [
                ...$article,
                'published_at' => null,
                'summary' => ['es' => '[Resumen del artículo: 2 o 3 líneas sobre qué aprende quien lo lee.]', 'en' => '[Article summary: 2 or 3 lines about what the reader will learn.]'],
                'body' => ['es' => '[Artículo en Markdown.]', 'en' => '[Article in Markdown.]'],
            ]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function articles(): array
    {
        return [
            [
                'slug' => 'git-github-gitlab',
                'category' => ArticleCategory::Tech,
                'is_featured' => true,
                'title' => ['es' => 'Git, GitHub y GitLab: la guía, actualizada', 'en' => 'Git, GitHub and GitLab: the updated guide'],
            ],
            ['slug' => 'claude-code-proyecto-real', 'category' => ArticleCategory::Tech, 'title' => ['es' => 'Claude Code en un proyecto real', 'en' => 'Claude Code on a real project']],
            ['slug' => 'ci-cd-github-actions', 'category' => ArticleCategory::Tech, 'title' => ['es' => 'De cero a producción: CI/CD con GitHub Actions', 'en' => 'From zero to production: CI/CD with GitHub Actions']],
            ['slug' => 'tecno-d-2-0', 'category' => ArticleCategory::Business, 'title' => ['es' => '11 años dirigiendo Tecno D 2.0: lo que aprendí', 'en' => '11 years running Tecno D 2.0: what I learned']],
            ['slug' => 'programar-con-un-nino', 'category' => ArticleCategory::Mama, 'title' => ['es' => 'Programar con un niño pequeño en casa', 'en' => 'Coding with a toddler at home']],
            ['slug' => 'pagos-recurrentes-saas', 'category' => ArticleCategory::Tech, 'title' => ['es' => 'Pagos recurrentes en un SaaS: lo que no te cuentan', 'en' => 'Recurring payments in a SaaS: what nobody tells you']],
            ['slug' => 'cofundar-una-agencia', 'category' => ArticleCategory::Business, 'title' => ['es' => 'Cofundar una agencia con socios', 'en' => 'Co-founding an agency with partners']],
            ['slug' => 'aws-o-digitalocean', 'category' => ArticleCategory::Tech, 'title' => ['es' => 'AWS o DigitalOcean: cómo elijo para cada proyecto', 'en' => 'AWS or DigitalOcean: how I choose for each project']],
            ['slug' => 'startup-weekend-maracay', 'category' => ArticleCategory::Community, 'title' => ['es' => 'Lo que aprendí co-organizando Startup Weekend Maracay', 'en' => 'What I learned co-organizing Startup Weekend Maracay']],
        ];
    }
}
