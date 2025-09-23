<?php declare(strict_types=1);

namespace HypeCodeTeam\AiRules\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ScriptHandler
{
    private const AGENTS_FILE = 'AGENTS.md';

    public static function copyAgents(Event $event): void
    {
        $filesystem = new Filesystem();
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

        $source = sprintf('%s/%s', $vendorDir, self::AGENTS_FILE);
        $target = getcwd();

        $filesystem->copy($source, $target, true);

        $event->getIO()->write('AGENTS.md copied to root directory.');
    }
}
