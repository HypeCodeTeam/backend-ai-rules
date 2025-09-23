# Git Commit Convention - API Config Project

## Overview
This document defines the commit message conventions for the API Config project. All commits MUST follow the format `type(scope): subject` with specific types and scopes. Subject lines must be lowercase, imperative, and under 50 characters. Use references to tickets and ensure one logical change per commit. This ensures clear, searchable commit history and proper changelog generation.

## Quick Reference

### Format
```
type(scope): subject

<body>

<footer>
```

### Required Types
- **feat**: New feature or enhancement to existing feature
- **fix**: Bug fix or issue resolution
- **test**: Adding or modifying tests
- **refactor**: Code refactoring without changing functionality
- **perf**: Performance improvements
- **doc**: Documentation updates
- **chore**: Maintenance tasks (dependencies, build tools)
- **build**: Build system or Docker changes
- **style**: Code formatting, CSS changes
- **review**: Changes from code review feedback
- **bc** or **!**: Breaking changes (append ! after type)

### Required Scopes
- **domain**: Domain logic changes
- **controller**: Controller changes
- **service**: Service layer changes
- **repository**: Repository/database queries
- **entity**: Entity/model changes
- **dto**: Data transfer objects
- **validator**: Validation logic
- **config**: Configuration files
- **migration**: Database migrations
- **test**: Test-specific changes
- **api**: API endpoints/routes
- **docker**: Docker-related changes

### Subject Rules
- Start with lowercase verb in imperative mood
- Maximum 50 characters
- No period at the end
- Be specific and descriptive

## Examples

### Simple Commits
```
feat(domain): improve domain stuff
fix(service): correct monthly statistics calculation
feat(controller): add CSV export endpoint
test(repository): add tests for KpiRepository
refactor(dto): simplify statistics DTO structure
perf(repository): optimize database queries for stats
chore(composer): update Symfony to 6.4
```

### Complex Changes (with body)
```
refactor(service): restructure KPI calculation logic

- Extract business logic from repository
- Add Redis caching for heavy calculations
- Improve error handling
- Update related tests
```

### Breaking Changes
```
feat(api)!: change /api/stats response format

BREAKING CHANGE: Response format changed from array to object.
All API clients must update their integration.
```

### Common Patterns for This Project

#### Statistics Features
```
feat(service): add date range filtering to statistics
fix(repository): correct GROUP BY clause in stats query
perf(service): cache statistics results for 5 minutes
```

#### API Changes
```
feat(controller): add pagination to /api/stats endpoint
fix(dto): validate date format in query parameters
refactor(controller): extract validation to dedicated service
```

#### Testing
```
test(functional): add tests for export endpoint
test(unit): cover edge cases in StatisticsService
fix(test): correct mock setup for KpiRepository
```

## Validation Rules

1. **MUST** include both type and scope
2. **MUST NOT** use generic messages ("fix", "update", "WIP")
3. **MUST** be specific about what changed
4. **SHOULD** reference ticket numbers with `Refs TCKT-XXX`
5. **MUST** use consistent language (prefer English for this project)

## Decision Guide

### Which Type?
- Adding new endpoint? → `feat`
- Fixing a bug? → `fix`
- Changing code structure? → `refactor`
- Adding tests? → `test`
- Updating dependencies? → `chore`
- Improving performance? → `perf`

### Which Scope?
- Look at the main file/directory changed
- Use the most specific scope that applies
- For multiple areas, use the primary one

### Writing the Subject
Ask: "If applied, this commit will..."
- ✅ "fix memory leak in statistics service"
- ❌ "fixed stuff"
- ❌ "statistics updates"

## Pre-commit Checklist
- [ ] Type and scope are correct
- [ ] Subject clearly describes the change
- [ ] No debug statements (var_dump, dump, die, exit)
- [ ] Tests pass (`make test`)
- [ ] Code style is correct (`make quality`)
- [ ] One logical change per commit

## AI Usage Guidelines
When generating commit messages with LLMs:
- **Provide the change description** and let AI apply the format
- **Specify the scope** based on affected files (controller, service, etc.)
- **Use examples** from this document as templates
- **Ensure compliance** with rules: lowercase imperative, <50 chars, no period
- **Reference tickets** when available with `Refs TCKT-XXX`

