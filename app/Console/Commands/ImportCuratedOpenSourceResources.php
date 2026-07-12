<?php

namespace App\Console\Commands;

use App\Models\Resource;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Seeds the catalog with real, license-verified open-source FiveM resources
 * (MIT/Apache/GPL/AGPL projects on GitHub) so the site isn't empty while
 * real creators are onboarded. Every entry keeps a visible attribution to
 * its original author and license in the description, and all resources are
 * owned by a single transparent "curator" account rather than impersonating
 * the original authors.
 */
class ImportCuratedOpenSourceResources extends Command
{
    protected $signature = 'resources:import-curated';

    protected $description = 'Import a curated list of license-verified open-source FiveM resources from GitHub';

    public function handle(): int
    {
        $curator = User::firstOrCreate(
            ['username' => 'fivem-catalog'],
            [
                'name' => 'FiveM Catalog',
                'email' => 'curated@fivem-catalog.local',
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(40)),
                'role' => 'creator',
                'is_verified' => true,
                'bio' => 'Curated open-source picks from the FiveM community, imported with attribution while the catalog grows. Not the original author unless otherwise noted — see each listing for the source repo and license.',
            ]
        );

        $this->ensurePlaceholderThumbnails();

        $created = 0;
        $updated = 0;

        foreach ($this->curatedResources() as $item) {
            $licenseTag = Tag::firstOrCreate(['name' => $item['license']]);
            $ossTag = Tag::firstOrCreate(['name' => 'Open Source']);

            $resource = Resource::updateOrCreate(
                ['download_url' => $item['url']],
                [
                    'user_id' => $curator->id,
                    'title' => $item['title'],
                    'description' => $this->buildDescription($item),
                    'category' => $item['category'],
                    'framework' => $item['framework'],
                    'price' => 0,
                    'download_url' => $item['url'],
                    'tebex_url' => null,
                    'thumbnail_path' => $item['thumbnail'] ?? "thumbnails/curated/{$item['category']}.svg",
                    'status' => 'approved',
                ]
            );

            $resource->tags()->syncWithoutDetaching([$licenseTag->id, $ossTag->id]);

            $resource->wasRecentlyCreated ? $created++ : $updated++;
        }

        $this->info("Curated import done: {$created} created, {$updated} updated.");

        return self::SUCCESS;
    }

    private function buildDescription(array $item): string
    {
        return <<<TEXT
        {$item['description']}

        ---
        Curated open-source pick, not an original submission by this account. Originally created by {$item['author']}, licensed under {$item['license']}. Source and support: {$item['url']}
        TEXT;
    }

    /**
     * Creates one simple, original SVG placeholder per category (no
     * third-party screenshots — avoids any image-rights ambiguity) so
     * curated listings don't render a "broken image" tile.
     */
    private function ensurePlaceholderThumbnails(): void
    {
        $labels = [
            'scripts' => 'Script',
            'mlos' => 'MLO',
            'eup' => 'EUP',
            'vehicles' => 'Vehicle',
        ];

        foreach ($labels as $category => $label) {
            $path = "thumbnails/curated/{$category}.svg";

            if (Storage::disk('public')->exists($path)) {
                continue;
            }

            Storage::disk('public')->put($path, $this->placeholderSvg($label));
        }
    }

    private function placeholderSvg(string $label): string
    {
        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="1280" height="720" viewBox="0 0 1280 720">
            <rect width="1280" height="720" fill="#18181b"/>
            <rect x="0" y="0" width="1280" height="8" fill="#FF9100"/>
            <text x="640" y="380" font-family="Arial, sans-serif" font-size="64" font-weight="700" fill="#e4e4e7" text-anchor="middle">{$label}</text>
            <text x="640" y="440" font-family="Arial, sans-serif" font-size="28" fill="#71717a" text-anchor="middle">Open-source curated pick</text>
        </svg>
        SVG;
    }

    /**
     * @return array<int, array{title: string, url: string, author: string, license: string, category: string, framework: string, description: string}>
     */
    private function curatedResources(): array
    {
        return [
            ['title' => 'ox_lib', 'url' => 'https://github.com/overextended/ox_lib', 'author' => 'overextended', 'license' => 'MIT', 'category' => 'scripts', 'framework' => 'standalone', 'description' => "A shared utility library for FiveM/RedM resources written in Lua and JS, providing common UI components (menus, context menus, input dialogs), locales, and helper functions so other resources don't need to reinvent them. It underpins most of the modern \"ox\" ecosystem of resources."],
            ['title' => 'ox_target', 'url' => 'https://github.com/overextended/ox_target', 'author' => 'overextended', 'license' => 'MIT', 'category' => 'scripts', 'framework' => 'ox', 'description' => 'A drop-in interaction/targeting system letting players interact with entities, objects, and zones via configurable options, with better collision handling than older targeting resources. Built to be framework-agnostic and widely used as a dependency by other ox-ecosystem scripts.'],
            ['title' => 'ox_inventory', 'url' => 'https://github.com/overextended/ox_inventory', 'author' => 'overextended', 'license' => 'GPL-3.0', 'category' => 'scripts', 'framework' => 'ox', 'description' => 'A slot-based inventory system with shops, stashes, crafting, and vehicle trunk/glovebox storage, all validated server-side to prevent duping/exploits. One of the most widely adopted inventory replacements in the FiveM ecosystem.'],
            ['title' => 'ox_doorlock', 'url' => 'https://github.com/overextended/ox_doorlock', 'author' => 'overextended', 'license' => 'GPL-3.0', 'category' => 'scripts', 'framework' => 'ox', 'description' => 'A door-locking/access-control resource supporting passcodes, job/grade permissions, item-gated access, and lockpicking, compatible with ox_core, ESX, qbox, and other frameworks. Lets admins configure per-door rules through a shared config file.'],
            ['title' => 'ox_fuel', 'url' => 'https://github.com/overextended/ox_fuel', 'author' => 'overextended', 'license' => 'GPL-3.0', 'category' => 'scripts', 'framework' => 'ox', 'description' => 'A lightweight fuel resource meant to pair with ox_inventory, exposing vehicle fuel levels through natives/statebags and supporting configurable payment methods at pumps. Positioned as a simpler alternative to LegacyFuel.'],
            ['title' => 'qb-core', 'url' => 'https://github.com/qbcore-framework/qb-core', 'author' => 'qbcore-framework', 'license' => 'GPL-3.0', 'category' => 'scripts', 'framework' => 'qb-core', 'description' => 'The core framework resource for QBCore, one of the two dominant FiveM roleplay frameworks, providing player data management, jobs, gangs, and the shared API other QBCore resources build on.'],
            ['title' => 'qb-smallresources', 'url' => 'https://github.com/qbcore-framework/qb-smallresources', 'author' => 'qbcore-framework', 'license' => 'GPL-3.0', 'category' => 'scripts', 'framework' => 'qb-core', 'description' => 'A bundled collection of small gameplay resources for QBCore (consumables, weapon behavior tweaks, vehicle spawning helpers, animations, and misc quality-of-life scripts) packaged as one resource instead of many.'],
            ['title' => 'qb-menu', 'url' => 'https://github.com/qbcore-framework/qb-menu', 'author' => 'qbcore-framework', 'license' => 'GPL-3.0', 'category' => 'scripts', 'framework' => 'qb-core', 'description' => 'A menu UI system for QBCore built on a modified NH Context menu, letting other resources spawn headers, submenus, and clickable options that trigger events, commands, or callbacks.'],
            ['title' => 'qbx_core', 'url' => 'https://github.com/Qbox-project/qbx_core', 'author' => 'Qbox-project', 'license' => 'GPL-3.0-or-later', 'category' => 'scripts', 'framework' => 'ox', 'description' => 'The core resource for Qbox, a community-maintained successor to QBCore built on the overextended/ox libraries, offering backwards compatibility with existing QB resources plus multi-character, multi-job, and queue systems.'],
            ['title' => 'FiveM-phone', 'url' => 'https://github.com/Greigh/FiveM-phone', 'author' => 'Greigh', 'license' => 'MIT', 'category' => 'scripts', 'framework' => 'standalone', 'description' => 'A set of five universal phone apps (invoicing, notes, business cards, calculator, gallery) designed to work across ESX, QBCore, and QBX with multiple phone systems (LB Phone, QB Phone, QS Smartphone).'],
            ['title' => 'FiveM-NPC-Controller', 'url' => 'https://github.com/gamingnotice/FiveM-NPC-Controller', 'author' => 'gamingnotice', 'license' => 'MIT', 'category' => 'scripts', 'framework' => 'standalone', 'description' => 'Lets players spawn and command up to two NPCs, ordering them to follow, enter/exit vehicles, or change aggression level, controllable through both an in-game menu and slash commands.'],
            ['title' => 'mc-crosshair', 'url' => 'https://github.com/MindDevelopment/mc-crosshair', 'author' => 'MindDevelopment', 'license' => 'MIT', 'category' => 'scripts', 'framework' => 'qb-core', 'description' => 'A configurable crosshair overlay that appears while aiming, with multiple preset styles, adjustable size/opacity/color, and a persistent in-game settings panel for QBCore servers.'],
            ['title' => 'ESX-QBCore-Converter', 'url' => 'https://github.com/sledgehamm3r/ESX-QBCore-Converter', 'author' => 'sledgehamm3r', 'license' => 'MIT', 'category' => 'scripts', 'framework' => 'standalone', 'description' => 'A conversion utility that rewrites Lua/HTML/JS script files between ESX and QBCore syntax via configurable string replacements, intended to speed up porting existing resources between the two frameworks.'],
            ['title' => 'jobcreator-fivem', 'url' => 'https://github.com/cdeivid/jobcreator-fivem', 'author' => 'cdeivid', 'license' => 'MIT', 'category' => 'scripts', 'framework' => 'esx', 'description' => 'A job management system for ESX and QBCore letting admins create jobs, configure ranks/permissions, and place interactive markers for job actions, plus a "Nexus" system for sharing job configs across servers.'],
            ['title' => 'egl_garage', 'url' => 'https://github.com/0xEagle1337/egl_garage', 'author' => '0xEagle1337', 'license' => 'MIT', 'category' => 'scripts', 'framework' => 'esx', 'description' => 'A garage/impound system for ESX Legacy 1.8.5+ built around a RageUI interface, letting players store, retrieve, and manage owned vehicles with multi-language support.'],
            ['title' => 'ts_hud', 'url' => 'https://github.com/Thomasdev18/ts_hud', 'author' => 'Thomasdev18', 'license' => 'GPL-3.0', 'category' => 'scripts', 'framework' => 'standalone', 'description' => 'A React/TypeScript + Mantine-based HUD replacement with a settings menu, multiple display modes, and stated compatibility with both QBCore and Qbox.'],
            ['title' => 'Vehicles', 'url' => 'https://github.com/five-m/Vehicles', 'author' => 'five-m', 'license' => 'MIT', 'category' => 'vehicles', 'framework' => 'standalone', 'description' => "A collection of pre-packaged, FiveM-ready add-on vehicles organized by manufacturer, assembled so server owners don't have to manually convert single-player car mods themselves."],
            ['title' => 'FiveM-Vehicle-Editor', 'url' => 'https://github.com/STR0UDY/FiveM-Vehicle-Editor', 'author' => 'STR0UDY', 'license' => 'MIT', 'category' => 'vehicles', 'framework' => 'standalone', 'description' => 'A real-time vehicle handling editor with a React web UI, letting admins tweak handling parameters in-game with changes applied instantly and tracked in a change history, no server restart required.'],
            ['title' => 'qb-interior', 'url' => 'https://github.com/qbcore-framework/qb-interior', 'author' => 'qbcore-framework', 'license' => 'GPL-3.0', 'category' => 'mlos', 'framework' => 'qb-core', 'description' => 'A pack of building/interior shells for QBCore, sourced from K4MB1Maps, providing ready-made interior map assets for customizing roleplay locations.'],
            ['title' => 'GTA-V-MLO-Open-Interior-MANSION-2', 'url' => 'https://github.com/FiveMPost/GTA-V-MLO-Open-Interior-MANSION-2', 'author' => 'FiveMPost', 'license' => 'AGPL-3.0', 'category' => 'mlos', 'framework' => 'standalone', 'description' => 'An open-interior mansion MLO map mod for FiveM servers, distributed as a drag-in resource. Note: AGPL is more strictly copyleft than MIT/GPL (network-use clause) — review the license before repackaging.'],
            ['title' => 'lev-laundromat', 'url' => 'https://github.com/levdevlev/lev-laundromat', 'author' => 'levdevlev', 'license' => 'MIT', 'category' => 'mlos', 'framework' => 'standalone', 'thumbnail' => 'thumbnails/curated/lev-laundromat.png', 'description' => "A detailed laundromat interior MLO with multiple themed rooms (main floor, bathroom, reception, manager's office, basement), shipped with screenshots and drag-in install instructions."],
            ['title' => 'fivem-appearance', 'url' => 'https://github.com/pedr0fontoura/fivem-appearance', 'author' => 'pedr0fontoura', 'license' => 'MIT', 'category' => 'eup', 'framework' => 'standalone', 'description' => 'A flexible, framework-agnostic player/ped customization script consolidating clothing, hair, and appearance editing behind a single React-based UI.'],
            ['title' => 'illenium-appearance', 'url' => 'https://github.com/iLLeniumStudios/illenium-appearance', 'author' => 'iLLeniumStudios', 'license' => 'MIT', 'category' => 'eup', 'framework' => 'qb-core', 'description' => 'A feature-rich clothing/appearance replacement built on the fivem-appearance base, adding job/gang clothing rooms, tattoos, and plastic-surgeon functionality across QBCore, ESX, and ox_core.'],
            ['title' => 'FiveM-Clothes', 'url' => 'https://github.com/xchopin/FiveM-Clothes', 'author' => 'xchopin', 'license' => 'Apache-2.0', 'category' => 'eup', 'framework' => 'standalone', 'description' => 'A clothing-shop plugin letting players browse, try on, and purchase outfits through an in-game menu with map blips/markers, multi-language support, and fast load times.'],
            ['title' => 'bl_appearance', 'url' => 'https://github.com/Byte-Labs-Studio/bl_appearance', 'author' => 'Byte-Labs-Studio', 'license' => 'GPL-3.0', 'category' => 'eup', 'framework' => 'ox', 'thumbnail' => 'thumbnails/curated/bl-appearance.png', 'description' => 'An advanced character customization script (ped selection, face mixing, tattoos, outfit management) that works across QBCore, Qbox, and ESX via a framework bridge. Note: the upstream repo is archived/no longer maintained.'],
        ];
    }
}
