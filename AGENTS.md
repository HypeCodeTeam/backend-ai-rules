# AGENTS.md - API Config Project Guidelines

## 🔒 Security Rules
- **NEVER** read or edit `.env.local` files
- **NEVER** expose or log secrets/API keys
- **NEVER** execute database migrations (`doctrine:migrations:migrate`)
- **ALWAYS** follow security best practices

## 📋 Development Guidelines

### 🏗️ Coding Principles
**Reference:** `./vendor/hypecodeteam/backend-ai-rules/rules/coding_principles.md`

### 🧪 Unit Testing Standards
**Reference:** `./vendor/hypecodeteam/backend-ai-rules/rules/unit_testing.md`

### 📝 Git Commit Convention
**Reference:** `./vendor/hypecodeteam/backend-ai-rules/rules/git_workflow_guideline.md`

<!-- If you need to use domain language, uncomment these 2 lines below, then create and fill the file you need with your domain rules-->
<!-- ### 🗣️ Domain Language -->
<!-- **Reference:** `./.rules/ubiquitous_language.md` -->

## 🤖 AI/LLM Integration

### ⚠️ MANDATORY RULES - SYSTEMATIC VERIFICATION

**CRITICAL: Before EVERY code proposal, the AI MUST imperatively:**

1. **Read and apply** `./vendor/hypecodeteam/backend-ai-rules/rules/repository_service_patterns.md` (controller/service logic, constants, OrThrow patterns)
2. **Read and apply** `./vendor/hypecodeteam/backend-ai-rules/rules/coding_principles.md` (PHP 8 patterns, Yoda conditions, defensive programming)
3.  **Read and apply** `./vendor/hypecodeteam/backend-ai-rules/rules/unit_testing.md` (Prophecy, AAA pattern, isolated tests)
4. **Read and apply** `./vendor/hypecodeteam/backend-ai-rules/rules/git_workflow_guideline.md` (commit message format)
<!-- Uncomment the line below if you need to use domain language -->
<!-- 5. **Read and apply** `./.rules/ubiquitous_language.md` (domain language terms and relations) -->
- **Explicitly verify compliance** before responding
- **Display in a bullet point the rules that have been read and loaded in the first answer**

**WITHOUT EXCEPTION:**
- ❌ No business logic in controllers
- ❌ No hardcoded values (use UPPER_SNAKE_CASE constants)
- ❌ No generic methods when a dedicated method is necessary
- ✅ Thin controllers (orchestration only)
- ✅ Private constants in services for static values
- ✅ Dedicated methods in services for special routes

**If these rules are not respected, the response is invalid and must be corrected.**

### Quick-Reference Tables for Agents

| Guideline | Key Rule | Example |
|-----------|----------|---------|
| **Class Declaration** | Final when not extended | `final class Service` |
| **Constructor** | Property promotion | `public function __construct(public readonly string $name)` |
| **Conditions** | Yoda + explicit | `if (false === $obj instanceof Type)` |
| **Testing** | Prophecy + AAA | Arrange → Act → Assert |
| **Commits** | type(scope): subject | `fix(service): correct calculation` |
| **Domain Terms** | Use exact terms | Affiliate (not Publisher), Campaign (not Link) |

| When to Use | Pattern | Reference |
|-------------|---------|-----------|
| New feature | feat(scope): add... | `./vendor/hypecodeteam/backend-ai-rules/rules/git_commit_guideline.md` |
| Bug fix | fix(scope): correct... | `./vendor/hypecodeteam/backend-ai-rules/rules/git_commit_guideline.md` |
| Business logic | Service method | `./vendor/hypecodeteam/backend-ai-rules/rules/repository_service_patterns.md` |
| Data access | Repository OrThrow | `./vendor/hypecodeteam/backend-ai-rules/rules/repository_service_patterns.md` |
| Controller input | Deserialize RequestDto + validate | `./vendor/hypecodeteam/backend-ai-rules/rules/repository_service_patterns.md` |
| Entity validation | Rich model methods | `./vendor/hypecodeteam/backend-ai-rules/rules/coding_principles.md` |
| Test setup | ProphecyTrait + setUp() | `./vendor/hypecodeteam/backend-ai-rules/rules/unit_testing.md` |

### AI Usage Guidelines
- **Always reference specific rules** when asking for code changes
- **Request examples** from existing codebase patterns
- **Specify testing requirements** using unit testing standards
- **Use consistent terminology** from domain language section
- **Provide context** from ubiquitous language for domain-specific features