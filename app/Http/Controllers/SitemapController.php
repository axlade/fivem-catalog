<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml covering every publicly indexable page: the
     * catalog, services directory, ToS, and every approved resource,
     * active service, and creator profile that isn't hidden.
     */
    public function index(): Response
    {
        $urls = new Collection();

        $urls->push([
            'loc' => route('home'),
            'changefreq' => 'hourly',
            'priority' => '1.0',
        ]);

        $urls->push([
            'loc' => route('services.index'),
            'changefreq' => 'daily',
            'priority' => '0.8',
        ]);

        $urls->push([
            'loc' => route('legal.tos'),
            'changefreq' => 'yearly',
            'priority' => '0.3',
        ]);

        Resource::query()
            ->approved()
            ->whereHas('user', fn ($q) => $q->contentVisible())
            ->select('id', 'slug', 'updated_at')
            ->chunk(500, function ($resources) use ($urls) {
                foreach ($resources as $resource) {
                    $urls->push([
                        'loc' => route('resources.show', $resource),
                        'lastmod' => $resource->updated_at->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ]);
                }
            });

        Service::query()
            ->active()
            ->whereHas('user', fn ($q) => $q->contentVisible())
            ->select('id', 'slug', 'updated_at')
            ->chunk(500, function ($services) use ($urls) {
                foreach ($services as $service) {
                    $urls->push([
                        'loc' => route('services.show', $service),
                        'lastmod' => $service->updated_at->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.6',
                    ]);
                }
            });

        User::query()
            ->contentVisible()
            ->whereHas('resources', fn ($q) => $q->approved())
            ->select('id', 'username', 'updated_at')
            ->chunk(500, function ($users) use ($urls) {
                foreach ($users as $user) {
                    $urls->push([
                        'loc' => route('creators.show', $user),
                        'lastmod' => $user->updated_at->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.5',
                    ]);
                }
            });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
