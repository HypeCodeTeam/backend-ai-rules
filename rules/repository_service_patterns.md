# Repository & Service Patterns

*Requirements Level: MUST, MUST NOT, SHOULD, MAY (RFC 2119)*

## Overview
This document defines patterns for repositories, domain, services, and controllers in the API Config project. Key principles include OrThrow methods for centralized exception handling, dedicated service methods for special routes, thin controllers as orchestrators, and no hardcoded values (use constants). Follow these patterns to maintain clean architecture with proper separation of concerns and DRY principles.

## Repository Patterns

### 1. OrThrow Methods Pattern
**MUST** provide `OrThrow` variants for common find methods to centralize exception handling.

```php
// ✅ CORRECT - OrThrow pattern
final class DomainRepository extends ServiceEntityRepository
{
    public function findOneByCode(string $code): ?Domain
    {
        return $this->createQueryBuilder('c')
            ->where('c.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findOneByCodeOrThrow(string $code): Domain
    {
        $domain = $this->findOneByCode($code);
        
        if (null === $domain) {
            throw new EntityNotFoundException(
                Domain::class,
                sprintf('%s with code "%s"', ExceptionMessageValueObject::ENTITY_NOT_FOUND, $code)
            );
        }
        
        return $domain;
    }
}

// ❌ INCORRECT - Exception logic in service
public function getDomain(string $code): Domain
{
    $domain = $this->repository->findOneByCode($code);
    if (null === $domain) {
        throw new EntityNotFoundException(); // Should be in repository
    }
    return $domain;
}
```

**Reason**: KISS principle, centralized exception handling, cleaner service layer.

### 2. Repository Method Naming
**MUST** use consistent naming patterns:

| Pattern | Returns | Throws | Example |
|---------|---------|--------|---------|
| `findOne*` | `?Entity` | Never | `findOneByCode(string $code): ?Domain` |
| `findOne*OrThrow` | `Entity` | EntityNotFoundException | `findOneByCodeOrThrow(string $code): Domain` |
| `find*` | `array` | Never | `findByStatus(string $status): array` |
| `count*` | `int` | Never | `countByStatus(string $status): int` |

## Service Patterns

### 1. Static Values as Constants
**MUST NOT** hardcode static values directly in methods.
**MUST** declare them as class constants using UPPER_SNAKE_CASE.

```php
// ✅ CORRECT - Using constants
final class DomainService implements DomainServiceInterface
{
    private const DOMAIN_CODE_EXPECTATION = 'expectation';
    private const DEFAULT_LIMIT = 100;
    private const CACHE_TTL_SECONDS = 3600;
    
    public function readDomainExpectation(): Domain
    {
        return $this->repository->findOneByCodeOrThrow(self::DOMAIN_CODE_EXPECTATION);
    }
}

// ❌ INCORRECT - Hardcoded values
public function readDomainExpectation(): Domain
{
    return $this->repository->findOneByCodeOrThrow('expectation'); // Hardcoded!
}
```

### 2. Service Method Patterns for Special Routes
**MUST** create dedicated service methods for special/static routes.

```php
// ✅ CORRECT - Dedicated method for special route
interface DomainServiceInterface
{
    public function readDomainExpectation(): Domain; // For /domain/expectation route
    public function readById(string $id): Domain; // For /domain/{id} route
}

// ❌ INCORRECT - Controller deciding what to fetch
public function __invoke()
{
    return $this->service->readByCode('expectation'); // Controller shouldn't know 'expectation'
}
```

### 3. Service Responsibility
**MUST** keep business logic in services, not controllers.

```php
// ✅ CORRECT - Service handles business logic
final class DomainService
{
    public function readDomainExpectation(): Domain
    {
        $domain = $this->repository->findOneByCodeOrThrow(self::DOMAIN_CODE_EXPECTATION);
        
        // Business logic in service
        if (false === $domain->isActive()) {
            throw new InactiveDomainException();
        }
        
        $this->enrichDomainData($domain);
        
        return $domain;
    }
}

// ❌ INCORRECT - Business logic in controller
final class ReadDomainController
{
    public function __invoke()
    {
        $domain = $this->service->getDomain();
        
        // Business logic should NOT be in controller
        if (!$domain->isActive()) {
            throw new InactiveDomainException();
        }
    }
}
```

## Controller Patterns

### 1. Controller Simplicity
**MUST** keep controllers as thin orchestrators only.

```php
// ✅ CORRECT - Thin controller
final class ReadDomainExpectationController
{
    public function __invoke(
        NormalizerInterface $normalizer,
        DomainServiceInterface $domainService,
    ): JsonResponse {
        return new JsonResponse($normalizer->normalize(
            $domainService->readDomainExpectation(),
            'json',
            ['groups' => ['domain:read']]
        ));
    }
}
```

**Controller responsibilities:**
- ✅ Deserialize and validate request DTOs
- ✅ Call service method
- ✅ Normalize/serialize response
- ✅ Return HTTP response

**Controller MUST NOT:**
- ❌ Contain business logic
- ❌ Make repository calls directly
- ❌ Decide which data to fetch (no hardcoded values)
- ❌ Validate business rules
- ❌ Manually create DTOs from request parameters

### 2. DTO Handling
**MUST** deserialize request content into RequestDto and validate before processing.

```php
// ✅ CORRECT - Proper DTO handling
final class CreateDomainController
{
    public function __invoke(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        DomainServiceInterface $service,
        Request $request,
    ): JsonResponse {
        $dto = $serializer->deserialize(
            $request->getContent(),
            CreateDomainRequestDto::class,
            'json'
        );

        $violations = $validator->validate($dto);
        if ($violations->count() > 0) {
            throw new InvalidJsonVerboseException($violations, CreateDomainRequestDto::class);
        }

        $result = $service->createDomain($dto);

        return new JsonResponse($result);
    }
}

// ❌ INCORRECT - Manual DTO creation
$dto = new DomainDto(
    name: $request->get('name'),  // Avoid this!
    affiliateId: $request->get('affiliateId'),
);
```

### 3. One Route = One Controller = One Method
**MUST** create dedicated controllers for special routes.

```php
// ✅ CORRECT - Dedicated controllers
ReadDomainExpectationController // For GET /domain/expectation
ReadDomainByIdController  // For GET /domain/{id}
ReadListDomainController      // For GET /domain

// ❌ INCORRECT - One controller handling multiple cases
DomainController
{
    public function read(?string $id = null) {
        if ($id === 'expectation') { ... }
        elseif ($id) { ... }
        else { ... }
    }
}
```

## Complete Flow Example

### Route: `GET /domain/expectation`

```yaml
domain.read_expectation:
  path: /expectation
  controller: App\Controller\Domain\ReadDomainExpectationController
```

```php
// Controller - Thin orchestrator
final class ReadDomainExpectationController
{
    public function __invoke(
        NormalizerInterface $normalizer,
        DomainServiceInterface $service
    ): JsonResponse {
        return new JsonResponse($normalizer->normalize(
            $service->readDomainExpectation(),
            'json',
            ['groups' => ['domain:read']]
        ));
    }
}

// Service Interface
interface DomainServiceInterface
{
    public function readDomainExpectation(): Domain;
}

// Service - Business logic + knows 'expectation'
final class DomainService implements DomainServiceInterface
{
    private const DOMAIN_CODE_EXPECTATION = 'foo';
    
    public function __construct(
        private readonly DomainRepository $repository
    ) {}
    
    public function readDomainExpectation(): Domain
    {
        return $this->repository->findOneByCodeOrThrow(self::DOMAIN_CODE_EXPECTATION);
    }
}

// Repository - Data access + exception if not found
final class DomainRepository extends ServiceEntityRepository
{
    public function findOneByCodeOrThrow(string $code): Domain
    {
        $domain = $this->findOneByCode($code);
        
        if (null === $domain) {
            throw new EntityNotFoundException(
                Domain::class,
                sprintf('Domain with code "%s" not found', $code)
            );
        }
        
        return $domain;
    }
}
```

## Implementation Checklist

### When Creating Special Routes (like /domain/expectation)
- [ ] Create dedicated controller (one route = one controller)
- [ ] Create dedicated service method (e.g., `readDomainExpectation()`)
- [ ] Declare static value as constant in service
- [ ] Use `OrThrow` repository method
- [ ] Keep controller thin (no business logic)

### Repository Methods
- [ ] Provide both nullable and OrThrow variants
- [ ] Centralize exception throwing in repository
- [ ] Use consistent naming patterns
- [ ] Include context in exception messages

### Service Methods
- [ ] No hardcoded values (use constants)
- [ ] Handle business logic
- [ ] Use repository OrThrow methods when appropriate
- [ ] Create dedicated methods for special routes

### Controllers
- [ ] Deserialize and validate RequestDto
- [ ] Only orchestrate (call service, normalize, return)
- [ ] No business logic
- [ ] No direct repository calls
- [ ] No manual DTO creation from request
- [ ] One controller per route

## Quick Decision Matrix

| Scenario | Where | Pattern |
|----------|-------|---------|
| Static value (like 'expectation') | Service constant | `private const DOMAIN_CODE_EXPECTATION = 'expectation'` |
| Entity not found exception | Repository OrThrow | `findOneByCodeOrThrow()` |
| Business validation | Service | `if (false === $domain->isActive())` |
| HTTP response | Controller | `new JsonResponse()` |
| Database query | Repository | `$this->createQueryBuilder()` |
| Special route logic | Dedicated service method | `readDomainExpectation()` |

## AI Usage Guidelines
When designing or modifying repository/service/controller code:
- **Use OrThrow patterns** for entity retrieval with exceptions
- **Create dedicated methods** in services for special routes (avoid hardcoded values)
- **Keep controllers thin** - only orchestration, no business logic
- **Reference the complete flow example** for new endpoints
- **Apply the decision matrix** to determine where logic belongs (service vs controller)