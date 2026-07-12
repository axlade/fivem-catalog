<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class ContentPolicyService
{
    /**
     * Known cheat/mod-menu tools and generic cheating terminology. FiveM-Catalog
     * only distributes legitimate server resources and freelance dev services —
     * submissions referencing game-cheating tools are rejected outright.
     *
     * @var list<string>
     */
    private const PROHIBITED_KEYWORDS = [
        'cypher', 'cherax', '2take1', 'stand menu', 'yimmenu', 'iceberg menu', 'ice menu',
        'mod menu', 'cheat menu', 'aimbot', 'wallhack', 'wall hack', 'godmode hack',
        'money glitch', 'game trainer', 'undetected cheat', 'external cheat', 'internal cheat',
        'dll injector', 'memory injector', 'rage cheat', 'cheat engine',
    ];

    /**
     * Rejects a submission whose title or description references a known
     * cheat/mod-menu tool, run before the resource or service is persisted.
     *
     * @throws ValidationException
     */
    public function assertNoCheatContent(string $title, string $description): void
    {
        $haystack = strtolower($title.' '.$description);

        foreach (self::PROHIBITED_KEYWORDS as $keyword) {
            if (str_contains($haystack, $keyword)) {
                throw ValidationException::withMessages([
                    'title' => 'This submission appears to reference a game-cheating tool, which is not permitted on FiveM-Catalog.',
                ]);
            }
        }
    }
}
