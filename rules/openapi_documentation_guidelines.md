# OpenAPI Documentation Guidelines

## 🎯 Purpose
This document defines the conventions and standards for creating OpenAPI documentation in the `api-tracking` project. These guidelines are derived from the existing documentation patterns and MUST be followed by AI agents and LLMs when creating new API documentation.

---

## 📁 File Structure & Organization

### Directory Structure
```
docs/openAPI/
├── {Domain}/                          # Domain folder (PascalCase)
│   ├── {ActionResourceByParam}.yaml   # Endpoint definition
│   └── Schemas/                       # Schemas subfolder
│       ├── {ActionResource}RequestAttributes.yaml
│       └── {ActionResource}ResponseAttributes.yaml
├── Common/
│   └── Schemas/
│       └── Error.yaml                 # Shared error schema
└── openapi.yaml                       # Main OpenAPI file
```

### Naming Conventions

#### 1. Domain Folders
- **Format**: `PascalCase`
- **Examples**: `Account/`, `Lead/`, `Tracker/`, `Payment/`, `Redirect/`, `User/`, `WebHandler/`, `HealthCheck/`, `Metadata/`, `Analytics/`
- **Rule**: Use singular form, match the business domain

#### 2. Endpoint Files
- **Format**: `{Action}{Resource}By{Parameter}.yaml`
- **Pattern**: `VerbNounByParameter` (PascalCase)
- **Examples**:
  - `ReadTdnInformationByAccountId.yaml`
  - `GetUserInfoByTrackerId.yaml`
  - `GetUserMetadataInfoByTrackerId.yaml`
  - `ReadPaymentFormByTrackerId.yaml`
  - `RedirectBillingPageByTrackerId.yaml`
  - `RedirectLandingPageByCampaignId.yaml`
  - `SaveUserLead.yaml` (no parameter in name when POST without path params)
  - `CreateTrackingWithLead.yaml`
  - `UpdateUserMetadataByTrackerId.yaml`

#### 3. Schema Files
- **Request**: `{Action}{Resource}RequestAttributes.yaml`
- **Response**: `{Action}{Resource}ResponseAttributes.yaml`
- **Examples**:
  - `SaveUserLeadRequestAttributes.yaml` / `SaveUserLeadResponseAttributes.yaml`
  - `CreateTrackingWithLeadRequestAttributes.yaml` / `CreateTrackingWithLeadResponseAttributes.yaml`
  - `ReadTdnInformationByAccountIdResponseAttributes.yaml`
  - `GetUserInfoByTrackerIdResponseAttributes.yaml`

---

## 🏗️ Endpoint File Structure

### Template Structure
```yaml
{httpMethod}:
    summary: '{Short Description}'
    operationId: {camelCaseOperationId}
    description: '{Detailed Description}'
    tags:
        - {DomainTag}
    parameters:  # Only for GET/PATCH/DELETE with path params
        -   in: path
            name: {paramName}
            required: {true|false}
            description: '{param description}'
            schema:
                type: {type}
                format: {format}  # Optional
                example: {example}
    requestBody:  # Only for POST/PATCH/PUT
        content:
            application/json:
                schema:
                    $ref: './Schemas/{SchemaName}.yaml'
    responses:
        {statusCode}:
            description: '{Response description}'
            content:  # Optional, omit if no body
                application/json:
                    schema:
                        $ref: './Schemas/{SchemaName}.yaml'
        default:
            description: 'Error'
            content:
                application/json:
                    schema:
                        $ref: '../Common/Schemas/Error.yaml'
```

---

## 📝 Naming Patterns Analysis

### operationId Conventions

| Pattern | Format | Examples |
|---------|--------|----------|
| **GET (read)** | `{verb}{Resource}` | `getAccountInfo`, `getPaymentForm`, `userInfo`, `userMetadataInfo`, `userMarketingInformation` |
| **POST (create)** | `{verb}{Resource}` | `saveLead`, `createTracker`, `redirectD2C` |
| **PATCH (update)** | `{verb}{Resource}` | `updateMetadata` |
| **GET (redirect)** | `redirect{Resource}` | `redirectBillingPage`, `redirectLandingPage` |
| **GET (healthcheck)** | `healthCheck` | `healthCheck` |
| **GET (webhandler)** | `webHandler{Resource}` | `webHandlerCustomerUsername` |

**Rules**:
- Use `camelCase` (NOT PascalCase)
- Start with verb: `get`, `save`, `create`, `update`, `redirect`
- Be concise but descriptive
- Omit "By{Parameter}" from operationId (it's in the file name)

### summary Conventions

| Pattern | Examples |
|---------|----------|
| **GET** | `'Get Account Info'`, `'Get Payment Form'`, `'User Info'`, `'User Metadata Info'` |
| **POST** | `'Save Lead'`, `'Create Tracker'`, `'Redirect d2c'` |
| **PATCH** | `'Update Metadata'` |
| **Redirect** | `'Redirect Billing Page'`, `'Redirect Landing Page'` |
| **Special** | `'Health check endpoint'`, `'Web Handler Customer Username'` |

**Rules**:
- Use Title Case
- Keep it short (2-4 words)
- Can match or be shorter than description
- No "by parameter" suffix

### description Conventions

**Rules**:
- Usually identical to `summary` OR slightly more detailed
- Use Title Case
- Examples:
  - `'Get Account Info'`
  - `'Validate API and services health'`
  - `'Redirect to Billing Page'`

### tags Conventions

**Rules**:
- Use the **Domain folder name** as the tag
- PascalCase, singular form
- Examples: `Account`, `Lead`, `Tracker`, `Payment`, `Redirect`, `User`, `WebHandler`, `HealthCheck`, `Metadata`

---

## 🔧 Parameters

### Path Parameters

**Standard format**:
```yaml
parameters:
    -   in: path
        name: {paramName}
        required: {true|false}
        description: '{param description}'
        schema:
            type: {type}
            format: {format}  # Optional: uuidV4, email
            example: {example}
```

**Common patterns**:

| Parameter | Type | Format | Required | Example |
|-----------|------|--------|----------|---------|
| `trackerId` | `string` | `uuidV4` | `true` | `f43e0e51-e5b1-4fb5-bfb2-57ced9617562` |
| `campaignId` | `string` | `uuidV4` | `true` | `f43e0e51-e5b1-4fb5-bfb2-57ced9617562` |
| `offerId` | `string` | `uuidV4` | `true` | `f43e0e51-e5b1-4fb5-bfb2-57ced9617562` |
| `billingPageId` | `string` | `uuidV4` | `false` | `f43e0e51-e5b1-4fb5-bfb2-57ced9617562` |
| `landingPageId` | `integer` | - | `false` | `1974` |
| `accountId` | `string` | - | `true` | `'520906517'` |
| `language` | `string` | - | `true` | `en` |
| `status` | `string` | - | `true` | `ok` |

**Rules**:
- Use `camelCase` for parameter names
- Always include `description` (lowercase, no period)
- Always include `example`
- Use `format: uuidV4` for UUID parameters
- Use `required: false` for optional parameters (not `nullable`)

---

## 📤 Responses

### Success Responses

**Pattern 1: JSON response with schema**
```yaml
responses:
    200:
        description: 'Successful response.'
        content:
            application/json:
                schema:
                    $ref: './Schemas/{SchemaName}ResponseAttributes.yaml'
```

**Pattern 2: Redirect response (no body)**
```yaml
responses:
    302:
        description: 'Successful redirect response.'
```

**Pattern 3: Simple success (no body)**
```yaml
responses:
    200:
        description: 'Successful response.'
```

**Pattern 4: Multiple schemas (oneOf)**
```yaml
responses:
    200:
        description: 'Successful response.'
        content:
            application/json:
                schema:
                    oneOf:
                        - $ref: './Schemas/Schema1.yaml'
                        - $ref: './Schemas/Schema2.yaml'
```

**Pattern 5: Health check with multiple status codes**
```yaml
responses:
    200:
        description: 'Healthy API endpoint response'
        content:
            application/json:
                schema:
                    $ref: './Schemas/HealthCheckResponseAttributes.yaml'
    503:
        description: 'Unhealthy API endpoint response'
        content:
            application/json:
                schema:
                    $ref: './Schemas/HealthCheckResponseAttributes.yaml'
```

### Error Responses

**Standard pattern** (ALWAYS include):
```yaml
default:
    description: 'Error'
    content:
        application/json:
            schema:
                $ref: '../Common/Schemas/Error.yaml'
```

**Rules**:
- ALWAYS include `default` error response
- Use relative path `../Common/Schemas/Error.yaml`
- Description is always `'Error'`

---

## 📋 Schema Files

### Request Schema Structure

```yaml
type: object
required:
  - {field1}
  - {field2}
properties:
  {fieldName}:
    type: {type}
    format: {format}  # Optional
    example: {example}
    nullable: {true}  # Optional
    default: {value}  # Optional
    enum:             # Optional
      - {value1}
      - {value2}
```

**Examples from codebase**:
```yaml
type: object
required:
  - email
  - password
  - tracker
  - platform
properties:
  email:
    type: string
    format: email
    example: 'test@example.com'
  password:
    type: string
  tracker:
    type: string
    format: uuidV4
    example: f43e0e51-e5b1-4fb5-bfb2-57ced9617562
  sso:
    type: boolean
    example: true
    default: false
  platform:
    type: string
    enum:
      - google
      - facebook
      - apple
    nullable: true
    example: 'google'
```

### Response Schema Structure

```yaml
type: object
properties:
  {fieldName}:
    type: {type}
    format: {format}  # Optional
    example: {example}
    nullable: {true}  # Optional
    description: '{description}'  # Optional
```

**Rules**:
- Use `camelCase` for property names
- Always include `example` values
- Use `format: uuidV4` for UUIDs
- Use `format: email` for emails
- Use `nullable: true` for optional fields (not `required: false`)
- Use `enum` for restricted values
- Keep schemas simple and flat when possible

---

## 🎨 HTTP Methods & Status Codes

### Method Usage

| Method | Use Case | Success Code | Examples |
|--------|----------|--------------|----------|
| `GET` | Read/Retrieve data | `200` | User info, Payment form, Account info |
| `GET` | Redirect | `302` | Redirect to billing, landing page |
| `POST` | Create/Save | `200` | Save lead, Create tracker |
| `PATCH` | Update | `200` | Update metadata |
| `DELETE` | Delete | `200` or `204` | (not in current examples) |

### Status Codes

| Code | Usage | Description Pattern |
|------|-------|---------------------|
| `200` | Success (data) | `'Successful response.'` |
| `302` | Redirect | `'Successful redirect response.'` |
| `503` | Service unavailable | `'Unhealthy API endpoint response'` |
| `default` | Any error | `'Error'` |

---

## ✅ Checklist for New Documentation

When creating new OpenAPI documentation, verify:

- [ ] **File naming**: `{Action}{Resource}By{Parameter}.yaml` in PascalCase
- [ ] **Domain folder**: Exists and uses PascalCase
- [ ] **Schemas folder**: Created inside domain folder
- [ ] **operationId**: Uses camelCase, starts with verb
- [ ] **summary**: Title Case, concise (2-4 words)
- [ ] **description**: Matches or expands summary
- [ ] **tags**: Matches domain folder name (PascalCase)
- [ ] **parameters**: camelCase names, includes description & example
- [ ] **UUID format**: Uses `format: uuidV4` for all UUIDs
- [ ] **UUID example**: Uses `f43e0e51-e5b1-4fb5-bfb2-57ced9617562`
- [ ] **Schema refs**: Use relative paths (`./Schemas/...` or `../Common/Schemas/...`)
- [ ] **Error response**: Includes `default` with `../Common/Schemas/Error.yaml`
- [ ] **Schema naming**: `{Action}{Resource}RequestAttributes.yaml` / `ResponseAttributes.yaml`
- [ ] **Required fields**: Listed in `required:` array in schemas
- [ ] **Examples**: All properties have example values
- [ ] **Main file**: Updated `docs/openAPI/openapi.yaml` with new path

---

## 🚫 Common Mistakes to Avoid

1. ❌ **Wrong file naming**: `GetAnalyticsByTrackerId.yaml` → ✅ `ReadAnalyticsByTrackerId.yaml`
2. ❌ **Wrong operationId**: `getAnalyticsByTrackerId` → ✅ `readAnalytics` or `analyticsInfo`
3. ❌ **Wrong tag**: `Analytics` when folder doesn't exist → ✅ Create folder first
4. ❌ **Missing default error**: Always include `default` response
5. ❌ **Wrong UUID format**: `uuid` → ✅ `uuidV4`
6. ❌ **Wrong case**: `analytics_info` → ✅ `analyticsInfo` (camelCase)
7. ❌ **Verbose summary**: `'Get Analytics Information By Tracker ID'` → ✅ `'Analytics Info'`
8. ❌ **Wrong schema path**: `./AnalyticsResponse.yaml` → ✅ `./Schemas/ReadAnalyticsByTrackerIdResponseAttributes.yaml`
9. ❌ **Missing examples**: Always include example values
10. ❌ **Inconsistent descriptions**: Use existing patterns as reference

---

## 📚 Reference Examples

### GET Endpoint with Path Parameter
**File**: `User/GetUserInfoByTrackerId.yaml`
```yaml
get:
    summary: 'User Info'
    operationId: userInfo
    description: 'User Info'
    tags:
        - User
    parameters:
        -   in: path
            name: trackerId
            required: true
            description: 'tracker id'
            schema:
                type: string
                format: uuidV4
                example: f43e0e51-e5b1-4fb5-bfb2-57ced9617562
    responses:
        200:
            description: 'Successful response.'
            content:
                application/json:
                    schema:
                        $ref: './Schemas/GetUserInfoByTrackerIdResponseAttributes.yaml'
        default:
            description: 'Error'
            content:
                application/json:
                    schema:
                        $ref: '../Common/Schemas/Error.yaml'
```

### POST Endpoint with Request Body
**File**: `Lead/SaveUserLead.yaml`
```yaml
post:
    summary: 'Save Lead'
    operationId: saveLead
    description: 'Save Lead'
    tags:
        - Lead
    requestBody:
        content:
            application/json:
                schema:
                    $ref: './Schemas/SaveUserLeadRequestAttributes.yaml'
    responses:
        200:
            description: 'Successful response.'
            content:
                application/json:
                    schema:
                        $ref: './Schemas/SaveUserLeadResponseAttributes.yaml'
        default:
            description: 'Error'
            content:
                application/json:
                    schema:
                        $ref: '../Common/Schemas/Error.yaml'
```

### PATCH Endpoint
**File**: `Metadata/UpdateUserMetadataByTrackerId.yaml`
```yaml
patch:
    summary: 'Update Metadata'
    operationId: updateMetadata
    description: 'Update Metadata'
    tags:
        - Metadata
    parameters:
        -   in: path
            name: trackerId
            required: true
            description: 'tracker id'
            schema:
                type: string
                format: uuidV4
                example: f43e0e51-e5b1-4fb5-bfb2-57ced9617562
    responses:
        200:
            description: 'Successful response.'
        default:
            description: 'Error'
            content:
                application/json:
                    schema:
                        $ref: '../Common/Schemas/Error.yaml'
```

### Redirect Endpoint
**File**: `Redirect/RedirectBillingPageByTrackerId.yaml`
```yaml
get:
    summary: 'Redirect Billing Page'
    operationId: redirectBillingPage
    description: 'Redirect to Billing Page'
    tags:
        - Redirect
    parameters:
        -   in: path
            name: trackerId
            required: true
            description: 'tracker id'
            schema:
                type: string
                format: uuidV4
                example: f43e0e51-e5b1-4fb5-bfb2-57ced9617562
        -   in: path
            name: billingPageId
            required: false
            description: 'billing page id'
            schema:
                type: string
                format: uuidV4
                example: f43e0e51-e5b1-4fb5-bfb2-57ced9617562
    responses:
        302:
            description: 'Successful redirect response.'
        default:
            description: 'Error'
            content:
                application/json:
                    schema:
                        $ref: '../Common/Schemas/Error.yaml'
```

---

## 🤖 AI Agent Instructions

When asked to create OpenAPI documentation:

1. **ALWAYS read this file first** before creating any documentation
2. **Analyze the controller** to understand the endpoint behavior
3. **Check existing similar endpoints** in the same domain for consistency
4. **Follow the naming conventions** exactly as specified
5. **Use the templates** provided in this document
6. **Verify against the checklist** before proposing changes
7. **Create all necessary files**: endpoint YAML, request/response schemas, update main openapi.yaml
8. **Be consistent** with existing patterns, don't invent new ones

---

## 📅 Document Version

- **Created**: 2025-10-01
- **Last Updated**: 2025-10-01
- **Based on**: Analysis of 12 existing OpenAPI endpoint definitions
- **Maintained by**: Development Team + AI Agents
