# Coding Principles & Standards - API Config Project

*Requirements Level: MUST, MUST NOT, SHOULD, MAY (RFC 2119)*

## Overview
This document outlines the coding standards and principles for the API Config project. It emphasizes SOLID principles, KISS methodology, PHP 8 features, defensive programming, and consistent naming conventions. All code MUST follow these guidelines to ensure maintainability, readability, and quality. Use Yoda conditions with explicit type checks, declare classes as final when appropriate, and prefer business methods over getters/setters in domain models.

## Core Principles

### SOLID
**MUST** apply SOLID principles:
- **S**ingle Responsibility: One class = one purpose
- **O**pen/Closed: Open for extension, closed for modification
- **L**iskov Substitution: Subtypes must be substitutable
- **I**nterface Segregation: Many specific interfaces > one general
- **D**ependency Inversion: Depend on abstractions, not concretions

### KISS (Keep It Simple)
**MUST** prioritize simplicity:
- No over-engineering
- Simplest solution that works
- Clear over clever

## PHP Code Standards

### 1. Class Declaration
**MUST** declare classes as `final` when not extended.

```php
// ✅ CORRECT
final class StatisticsService
{
    // class content
}

// ❌ INCORRECT - Not final when not extended
class StatisticsService
{
    // class content
}
```

### 2. Constructor Property Promotion (PHP 8)
**MUST** use constructor property promotion for DTOs and services.

```php
// ✅ CORRECT - PHP 8 style
final class CustomerDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly DateTimeImmutable $birthDate,
    ) {}
}

// ❌ INCORRECT - Old style
class CustomerDto
{
    public string $name;
    public string $email;
    
    public function __construct(string $name, string $email) 
    {
        $this->name = $name;
        $this->email = $email;
    }
}
```

### 3. Class Component Ordering
**MUST** follow this exact order:

```php
final class ExampleClass
{
    // 1. Traits
    use SomeTrait;

    // 2. Constants (public → protected → private)
    public const PUBLIC_CONST = 'value';
    protected const PROTECTED_CONST = 'value';
    private const PRIVATE_CONST = 'value';
    
    // 3. Properties (public → protected → private, static first)
    public static string $publicStatic;
    public string $publicProperty;
    protected static string $protectedStatic;
    protected string $protectedProperty;
    private static string $privateStatic;
    private string $privateProperty;
    
    // 4. Magic methods
    public function __construct() {}
    public function __destruct() {}
    
    // 5. Public methods (static first)
    public static function publicStaticMethod() {}
    public function publicMethod() {}
    
    // 6. Protected methods (static first)
    protected static function protectedStaticMethod() {}
    protected function protectedMethod() {}
    
    // 7. Private methods (static first)
    private static function privateStaticMethod() {}
    private function privateMethod() {}
}
```

**Reason**: Public API first, implementation details last.

## Code Patterns

### 1. Yoda Conditions + Explicit Verification
**MUST** place constants first AND use explicit type checks.

```php
// ✅ CORRECT - Yoda + Explicit
if (false === $object instanceof ExpectedType) { }
if (false === is_string($value)) { }
if (false === is_int($id)) { }
if (null === $object) { }
if (0 === $count) { }

// ❌ INCORRECT - Not Yoda or not explicit
if (!$object instanceof ExpectedType) { }
if ($value !== null) { }
if (is_string($value)) { }
```

**Reason**: Clear expectations, prevents accidental assignment, explicit type verification.

### 2. Defensive Programming
**MUST** validate inputs early and exit fast.

```php
// ✅ CORRECT - Early returns with explicit checks
public function process(Object $object): void 
{
    if (false === $object instanceof ExpectedType) {
        return;
    }
    
    if (false === is_int($object->getId())) {
        throw new InvalidArgumentException('Integer ID required');
    }
    
    // Main logic here
    $this->repository->save($object);
}
```

### 3. Multiline If Statements (PSR-12)
**MUST** follow PSR-12 formatting for complex conditions.

```php
// ✅ CORRECT
if (
    true === $user->isActive()
    && false === $user->isBanned()
    && 0 < $user->getCredits()
) {
    // logic here
}

// ❌ INCORRECT
if ($user->isActive() && !$user->isBanned() 
    && $user->getCredits() > 0) {
    // logic here
}
```

### 4. DTO Usage
**MUST** use DTOs with validation for data transfer. For controllers, deserialize request content into RequestDto.

```php
// ✅ CORRECT - RequestDto with validation
final class CreateCampaignRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $name,

        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $affiliateId,

        #[Assert\Positive]
        public readonly ?float $payout = null,
    ) {}
}

// In controller: deserialize and validate
$dto = $serializer->deserialize($request->getContent(), CreateCampaignRequestDto::class, 'json');
$violations = $validator->validate($dto);
if ($violations->count() > 0) {
    throw new InvalidJsonVerboseException($violations, CreateCampaignRequestDto::class);
}
```

### 5. Rich Domain Model
**SHOULD** use business methods instead of getters/setters.

```php
// ✅ CORRECT - Rich model with business logic
final class User
{
    private string $email;
    private UserStatus $status;
    
    public function activate(DateTimeImmutable $activatedAt): void
    {
        if (UserStatus::ACTIVE === $this->status) {
            throw new AlreadyActivatedException();
        }
        
        $this->status = UserStatus::ACTIVE;
        $this->activatedAt = $activatedAt;
    }
}
```

## Naming Conventions

### General Rules
**MUST** use these naming patterns:

| Element | Convention | Example |
|---------|------------|---------|
| Classes | PascalCase | `StatisticsService` |
| Methods | camelCase | `calculateTotal()` |
| Properties | camelCase | `$userName` |
| Constants | UPPER_SNAKE | `PUBLIC_CONST` |
| Routes | dot.notation | `user.address.update` |
| URL params | snake_case | `/user/{userId}/postal_code` |

### Route Standards
**MUST** follow route naming convention:

```yaml
# ✅ CORRECT - Separate routes for different actions
user.list:
  path: /user
  controller: App\Controller\User\ListUserController

user.read:
  path: /user/{userId}
  controller: App\Controller\User\ReadUserController
  requirements:
    userId: '\d+'

# ❌ INCORRECT - One route with optional parameter
user.read:
  path: /user/{userId?}
  controller: App\Controller\User\ReadUserController
```

**Rule**: One route = One controller = One action

## Implementation Checklist

### Class Creation
- [ ] Class declared as `final` (if not extended)
- [ ] Constructor uses property promotion
- [ ] Components ordered correctly
- [ ] Constants use UPPER_SNAKE_CASE
- [ ] Methods use camelCase

### Method Implementation
- [ ] Yoda conditions with explicit type checks
- [ ] Early returns for invalid inputs
- [ ] DTOs for data transfer
- [ ] Business logic in domain models
- [ ] Single responsibility

### Route Creation
- [ ] Uses dot notation naming
- [ ] One controller per route
- [ ] URL params use snake_case
- [ ] Requirements defined for IDs

## Quick Decision Matrix

| Scenario | Pattern | Example |
|----------|---------|---------|
| Null check | Yoda explicit | `if (null === $value)` |
| Type check | Explicit verification | `if (false === is_string($value))` |
| Instance check | Yoda instanceof | `if (false === $obj instanceof Type)` |
| Multiple conditions | PSR-12 multiline | See multiline example |
| Data transfer | DTO with Assert | See DTO example |
| Entity logic | Rich model methods | `$user->activate()` |
| New class | Final + promotion | See class examples |

## Project-Specific Examples

### Controller
```php
final class ReadStatisticsController
{
    public function __construct(
        private readonly StatisticsService $service,
    ) {}
    
    public function __invoke(StatisticsQueryParamDto $dto): Response
    {
        if (false === $this->isValidDateRange($dto)) {
            throw new InvalidDateRangeException();
        }
        
        return new JsonResponse($this->service->getStatistics($dto));
    }
}
```

### Service
```php
final class StatisticsService
{
    public function getStatistics(StatisticsQueryParamDto $dto): array
    {
        // Defensive + Yoda + Explicit
        if (null === $dto->startDate) {
            throw new InvalidArgumentException('Start date required');
        }
        
        if (false === is_string($dto->groupBy)) {
            $dto->groupBy = self::DEFAULT_GROUP_BY;
        }
        
        return $this->repository->findByDto($dto);
    }
}
```

### Entity
```php
final class UserAcquisition
{
    private ?DateTimeImmutable $convertedAt = null;
    private string $status = self::STATUS_PENDING;
    
    public function markAsConverted(DateTimeImmutable $convertedAt): void
    {
        if (null !== $this->convertedAt) {
            throw new AlreadyConvertedException();
        }
        
        if (self::STATUS_PENDING !== $this->status) {
            throw new InvalidStatusException();
        }
        
        $this->convertedAt = $convertedAt;
        $this->status = self::STATUS_CONVERTED;
    }
}
```

## AI Usage Guidelines
When using LLMs or agents to generate/modify code:
- **Always specify** PHP 8 features and final classes
- **Reference specific patterns** like Yoda conditions and DTO usage
- **Request examples** following the project-specific examples section
- **Ensure compliance** with SOLID and KISS principles
- **Use the decision matrix** for quick pattern selection
- **Validate against checklist** before finalizing code