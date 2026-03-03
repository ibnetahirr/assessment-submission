# Backend Interview Task

**Time:** 2-3 hours | **Focus:** Symfony, PHP, Doctrine ORM, API Design

## Overview

Build a POST endpoint for submitting assessment answers. This tests your ability to understand existing code, follow established patterns, and implement features in a domain-driven architecture.

**AI Usage:** You're welcome to use AI tools (ChatGPT, Claude, Copilot, etc.) for coding assistance - it's difficult to police and reflects real-world development. Please mention in your SOLUTION.md which AI tools you used and how, if any. However, all written explanations must be your own work, not AI-generated.


## Phase 1: Entity Relationship Diagram (10-15 min)

Create an ERD documenting the assessment domain model:
- All entities involved in the assessment system
- Relationships between entities with cardinality (1:1, 1:N, N:M)
- Primary and foreign keys
- Brief written description explaining the overall structure

Review the database schema (`backend/migrations/001_init_and_seed.sql`) and entity files (`backend/src/Domain/*.php`) to understand the model.

**Deliverable:** Diagram (hand-drawn, digital tool, or text-based) + short description.

## Phase 2: Review Results Calculation (30-45 min)

**Review existing code:**
1. Study `AssessmentService.getProgressAndScore()` - understand the scoring algorithm
2. Verify normalization formula: `(total_score - answered) / (max_score - answered) * 100`
3. Test with instance `d1111111-1111-1111-1111-111111111111`
   - Expected: 2/4 questions answered, 53.85% score
4. Add error handling where missing

**Deliverable:** Notes in SOLUTION.md explaining the scoring algorithm and any error handling you added.

## Phase 3: Implement Answer Submission (1-1.5 hours)

**Create POST endpoint:** `POST /api/assessment/answers`

**Request body:**
```json
{
  "instance_id": "d1111111-1111-1111-1111-111111111111",
  "question_id": "a3333333-3333-3333-3333-333333333333",
  "answer_option_id": "b3333333-3333-3333-3333-333333333333"
}
```

**Validation:**
- Instance exists
- Question exists and belongs to assessment
- For Likert: answer_option_id is valid
- For reflection: text_answer is provided

**Implementation:**
- Create controller (or add to existing)
- Validate input
- Create `AssessmentAnswer` entity
- Persist to database
- Return 201 Created or appropriate error

**Test:**
```bash
# Submit answer to Q3 (currently unanswered)
curl -X POST http://localhost:8002/api/assessment/answers \
  -H "Content-Type: application/json" \
  -d '{
    "instance_id": "d1111111-1111-1111-1111-111111111111",
    "question_id": "a3333333-3333-3333-3333-333333333333",
    "answer_option_id": "b3333333-3333-3333-3333-333333333333"
  }'

# Verify score updates (53.85% → 75%)
curl http://localhost:8002/api/assessment/results/d1111111-1111-1111-1111-111111111111
```

**Deliverable:** Working POST endpoint with validation, error handling, and test results documented in SOLUTION.md.

## Getting Started

1. Review database schema: `backend/migrations/001_init_and_seed.sql`
2. Review entities: `backend/src/Domain/`
3. Study existing code:
   - `backend/src/Domain/AssessmentService.php`
   - `backend/src/Controller/Assessment/AssessmentResultsController.php`
4. Test existing API:
   ```bash
   curl http://localhost:8002/api/assessment/results/d1111111-1111-1111-1111-111111111111
   ```
5. View frontend: http://localhost:3000

## Deliverables

1. ERD diagram
2. Implemented POST endpoint
3. **SOLUTION.md** with:
   - Explanation of scoring algorithm
   - Implementation approach
   - Tools used (including AI tools if any)
   - Testing steps (include curl commands)
   - Edge cases considered
   - Challenges and solutions

## Hints

- Normalization formula: `(total_score - answered) / (max_score - answered) * 100`
- Reflection questions don't affect scores
- Check existing controller for request/response patterns
- Use repositories to find entities
- Persist with EntityManager
- Question/answer IDs are in the migration file

## Stretch Goals (Optional)

- `PUT /api/assessment/answers/{id}` - Update existing answer
- `GET /api/assessment/instances/{id}/questions` - List questions with options
- Handle duplicate submissions
- Write unit/integration tests
- Add frontend form to submit answers
