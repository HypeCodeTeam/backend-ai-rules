<?php declare(strict_types=1);

namespace HypeCodeTeam\BackendAiRules\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ScriptHandler
{
    private const AGENTS_FILE = 'AGENTS.md';
    private const PACKAGE_DIRECTORY = '/hypecodeteam/backend-ai-rules';

    public static function copyAgents(Event $event): void
    {
        if (!$event->isDevMode()) {
            return;
        }
        $filesystem = new Filesystem();
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

        $source = sprintf('%s/%s', $vendorDir . self::PACKAGE_DIRECTORY, self::AGENTS_FILE);
        $target = getcwd() . '/'. self::AGENTS_FILE;

        $filesystem->copy($source, $target, true);

        $event->getIO()->write('AGENTS.md copied to root directory.');
    }
}
