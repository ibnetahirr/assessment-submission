Muhammad Dawood Tahir
Full Stack Developer 
Time spent 2.5 hours including setting up locally the code.

Overview:
For this task, I first reviewed how the scoring logic works in the
existing system. After understanding the normalization approach, I
implemented a POST endpoint to allow submitting answers for an
assessment instance, making sure validation and domain integrity were
properly handled.

Scoring Algorithm – How It Works:
The scoring is handled inside AssessmentService::getProgressAndScore().

Each Likert question has numeric values (for example, 1 to 5). When a
user selects an option: - That value contributes to the total score. -
The highest possible value contributes to the maximum score. - Only
answered questions are considered in scoring.

Since the minimum Likert value is 1 (not 0), the system normalizes the
score using:

(total_score - answered) / (max_score - answered) * 100

This shifts the scale so that: - The lowest possible score becomes 0% -
The highest possible score becomes 100%

Additional safeguards were added to: - Prevent division by zero - Ensure
percentages stay between 0 and 100 - Ensure only answered questions
affect max score

Answer Submission – Implementation Approach:
Endpoint: POST /api/assessment/answers

Controller Flow:
1.  Parse and validate the JSON body.
2.  Ensure instance_id and question_id are provided.
3.  Verify the assessment instance exists.
4.  Verify the question exists.
5.  Ensure the question belongs to the instance’s assessment.
6.  Validate based on question type:
    -   Likert → requires valid answer_option_id
    -   Reflection → requires text_answer
7.  Ensure the selected answer option belongs to the question.
8.  Create the AssessmentAnswer entity.
9.  Persist using Doctrine.
10. Return 201 Created on success.

Proper HTTP status codes are returned:
400 for validation errors, 404 for missing entities, 500 for unexpected database/server errors

Testing:
Submitted an answer using:

curl -X POST http://localhost:8002/api/assessment/answers -H
“Content-Type: application/json” -d ‘{ “instance_id”:
“d1111111-1111-1111-1111-111111111111”, “question_id”:
“a3333333-3333-3333-3333-333333333333”, “answer_option_id”:
“b3333333-3333-3333-3333-333333333333” }’

Then verified updated results:

curl
http://localhost:8002/api/assessment/results/d1111111-1111-1111-1111-111111111111

Confirmed score update (e.g., 53.85% → 75%).

Additional tests included:
Missing fields, Invalid instance or question, Invalid answer option, Reflection without text, Duplicate
submissions

Edge Cases Considered:
1. Invalid JSON body
2. Question not belonging to assessment
3. Invalid numeric option values
4. Division by zero
5. Out-of-range percentages
6. Database failure during persist

Challenges:
The main challenge was ensuring score normalization was mathematically
correct and aligned with business logic. Initially, max score included
unanswered questions, which caused inaccurate percentages. This was
resolved by ensuring only answered questions contribute to the max score
calculation.

Final Outcome:
The API now: 
1. Correctly calculates normalized scores 
2. Safely validates answer submissions
3. Enforces domain integrity
4. Returns proper HTTP responses
5. Updates assessment results dynamically after submission

Future:
Automate the process.
Better understanding.
Try to make more easier.
Discussion with stakeholders.
having experience in such systems managing grade books. 
