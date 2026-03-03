# Assessment Backend - Symfony 6.4

## Architecture

This backend follows Domain-Driven Design principles:

### Layers

1. **Controller** (`src/Controller/`)
   - HTTP layer
   - Route definitions via attributes
   - Minimal logic - delegates to services

2. **Domain** (`src/Domain/`)
   - Core business entities
   - Business logic (services)
   - Repository interfaces
   - Domain-specific logic

3. **Infrastructure** (minimal)
   - Database repositories extend Doctrine

### Key Patterns

**Repository Pattern:**
```php
class AssessmentRepository extends EntityRepository
{
    public function findAllAssessmentInstanceAnswers(AssessmentInstance $instance): array
    {
        // Query logic
    }
}
```

**Service Layer:**
```php
class AssessmentService
{
    public function getAssessmentResults(AssessmentInstance $instance): array
    {
        // Business logic for scoring
    }
}
```

**Entity Design:**
- Rich domain models with behavior
- Doctrine ORM annotations for persistence
- Gedmo Timestampable for audit fields
- UUID primary keys via Ramsey\Uuid

## Scoring Algorithm

The normalization formula converts Likert scale (1-5) to percentage (0-100%):

```php
$normalizedScore = $answeredQuestions > 0
    ? ($totalScore - $answeredQuestions)
    : 0;

$normalizedMaxScore = $answeredQuestions > 0
    ? ($maxScore - $answeredQuestions)
    : 0;

$percentage = $normalizedMaxScore > 0
    ? round(($normalizedScore / $normalizedMaxScore) * 100, 2)
    : 0;
```

**Why normalize?**
- Likert scales start at 1, not 0
- Answering "1" on all questions gives 20% instead of 0%
- Normalization maps 1→0%, 3→50%, 5→100%

**Example:**
- 2 questions answered: values 4 and 5
- Raw scores: total=9, max=10
- Normalized: (9-2) / (10-2) = 7/8 = 87.5%

## API Endpoints

### GET /api/assessment/results/{instanceId}

Returns calculation results for an assessment instance.

**Response:**
```json
{
  "instance": {
    "id": "uuid",
    "completed": false,
    "element": "1.1"
  },
  "total_questions": 4,
  "answered_questions": 2,
  "completion_percentage": 50,
  "scores": {
    "total_score": 9,
    "max_score": 10,
    "percentage": 87.5
  },
  "element_scores": {...},
  "insights": [...]
}
```

## Database Schema

**Tables:**
- `assessment` - Templates
- `assessment_questions` - Question bank
- `assessment_answer_options` - Multiple choice options
- `assessments_questions` - Join table (N:M)
- `assessment_session` - User's assessment journey
- `assessment_instance` - Individual response
- `assessment_answers` - Individual question responses

## Running Locally

```bash
# Install dependencies
composer install

# Start PHP server (local development, not Docker)
php -S localhost:8000 -t public

# Or use Docker (recommended - runs on port 8002)
docker-compose up backend
# Backend will be accessible at http://localhost:8002
```

## Development Tips

- Entities use Doctrine annotations (not attributes in PHP 8+)
- Repositories extend `EntityRepository` from Doctrine
- Service layer doesn't depend on HTTP/Controllers
- All IDs are UUIDs (not auto-increment)
- Timestamps managed by Gedmo extensions
- No authentication - focus on domain logic
