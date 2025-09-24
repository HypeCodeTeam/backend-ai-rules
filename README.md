# Hypecode Backend AI Rules

AI coding assistant rules and guidelines, that handles AGENTS.md, for Symfony projects.

## What it does

This package automatically copies **`AGENTS.md`**  file to your project root, that contains links to all the rules and guidelines for AI coding assistants
Don't hesitate to modify the file to fit your needs.

## Requirements

- AI agent that handle AGENTS.md
- PHP 8.1+

## Installation

- Add the following to your `composer.json` scripts section:
```json
{
    "scripts": {
        "copy-agents": "HypeCodeTeam\\BackendAiRules\\Composer\\ScriptHandler::copyAgents",
        ...
    }
}
```

- then add the package to your project
```bash
composer require --dev hypecodeteam/backend-ai-rules
```

- finally execute the composer script
```bash
composer run-script copy-agents
```

## License

MIT

---

**Hypecode Team** - Enhancing AI-assisted development workflows
