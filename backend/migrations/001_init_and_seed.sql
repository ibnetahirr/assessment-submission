-- Assessment Interview Task - Initial Schema and Seed Data

-- Create tables
CREATE TABLE IF NOT EXISTS assessment (
    id UUID PRIMARY KEY,
    element VARCHAR(255) NOT NULL,
    version VARCHAR(50),
    type VARCHAR(20) DEFAULT 'self',
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS assessment_questions (
    id UUID PRIMARY KEY,
    title TEXT NOT NULL,
    sequence INTEGER,
    question_type VARCHAR(50) DEFAULT 'likert',
    is_reflection BOOLEAN,
    reflection_prompt TEXT,
    responder_types JSON,
    element VARCHAR(255),
    question_suite VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS assessment_answer_options (
    id UUID PRIMARY KEY,
    assessment_question_id UUID NOT NULL,
    answer TEXT NOT NULL,
    value INTEGER NOT NULL,
    option_number INTEGER,
    explanation TEXT,
    FOREIGN KEY (assessment_question_id) REFERENCES assessment_questions(id)
);

CREATE TABLE IF NOT EXISTS assessments_questions (
    assessment_id UUID NOT NULL,
    assessmentquestion_id UUID NOT NULL,
    PRIMARY KEY (assessment_id, assessmentquestion_id),
    FOREIGN KEY (assessment_id) REFERENCES assessment(id),
    FOREIGN KEY (assessmentquestion_id) REFERENCES assessment_questions(id)
);

CREATE TABLE IF NOT EXISTS assessment_session (
    id UUID PRIMARY KEY,
    user_name VARCHAR(255),
    assessment_id UUID NOT NULL,
    responder_type VARCHAR(50) DEFAULT 'self',
    expected_responses INTEGER,
    invitation_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessment(id)
);

CREATE TABLE IF NOT EXISTS assessment_instance (
    id UUID PRIMARY KEY,
    session_id UUID NOT NULL,
    responder_name VARCHAR(255),
    completed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES assessment_session(id)
);

CREATE TABLE IF NOT EXISTS assessment_answers (
    id UUID PRIMARY KEY,
    assessment_instance_id UUID NOT NULL,
    assessment_answer_option_id UUID,
    text_answer TEXT,
    numeric_value INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_instance_id) REFERENCES assessment_instance(id),
    FOREIGN KEY (assessment_answer_option_id) REFERENCES assessment_answer_options(id)
);

-- Seed Data

-- 1. Assessment Template
INSERT INTO assessment (id, element, version, type, active) VALUES
('a1111111-1111-1111-1111-111111111111', '1.1', 'v4', 'self', true);

-- 2. Questions (3 Likert + 1 reflection)
INSERT INTO assessment_questions (id, title, sequence, question_type, element, is_reflection, reflection_prompt) VALUES
('a1111111-1111-1111-1111-111111111111', 'How confident are you in planning engaging lessons?', 1, 'likert', '1.1', false, null),
('a2222222-2222-2222-2222-222222222222', 'How often do you use formative assessment strategies?', 2, 'likert', '1.1', false, null),
('a3333333-3333-3333-3333-333333333333', 'To what extent do you differentiate instruction for diverse learners?', 3, 'likert', '1.1', false, null),
('a4444444-4444-4444-4444-444444444444', 'Reflection', 4, 'text', '1.1', true, 'What is one area you would like to develop further in your teaching practice?');

-- 3. Answer Options for Q1
INSERT INTO assessment_answer_options (id, assessment_question_id, answer, value, option_number) VALUES
('b1111111-1111-1111-1111-111111111111', 'a1111111-1111-1111-1111-111111111111', 'Not at all confident', 1, 1),
('b1111111-1111-1111-1111-111111111112', 'a1111111-1111-1111-1111-111111111111', 'Slightly confident', 2, 2),
('b1111111-1111-1111-1111-111111111113', 'a1111111-1111-1111-1111-111111111111', 'Moderately confident', 3, 3),
('b1111111-1111-1111-1111-111111111114', 'a1111111-1111-1111-1111-111111111111', 'Very confident', 4, 4),
('b1111111-1111-1111-1111-111111111115', 'a1111111-1111-1111-1111-111111111111', 'Extremely confident', 5, 5);

-- 4. Answer Options for Q2
INSERT INTO assessment_answer_options (id, assessment_question_id, answer, value, option_number) VALUES
('b2222222-2222-2222-2222-222222222221', 'a2222222-2222-2222-2222-222222222222', 'Never', 1, 1),
('b2222222-2222-2222-2222-222222222222', 'a2222222-2222-2222-2222-222222222222', 'Rarely', 2, 2),
('b2222222-2222-2222-2222-222222222223', 'a2222222-2222-2222-2222-222222222222', 'Sometimes', 3, 3),
('b2222222-2222-2222-2222-222222222224', 'a2222222-2222-2222-2222-222222222222', 'Often', 4, 4),
('b2222222-2222-2222-2222-222222222225', 'a2222222-2222-2222-2222-222222222222', 'Always', 5, 5);

-- 5. Answer Options for Q3
INSERT INTO assessment_answer_options (id, assessment_question_id, answer, value, option_number) VALUES
('b3333333-3333-3333-3333-333333333331', 'a3333333-3333-3333-3333-333333333333', 'Not at all', 1, 1),
('b3333333-3333-3333-3333-333333333332', 'a3333333-3333-3333-3333-333333333333', 'To a small extent', 2, 2),
('b3333333-3333-3333-3333-333333333333', 'a3333333-3333-3333-3333-333333333333', 'To some extent', 3, 3),
('b3333333-3333-3333-3333-333333333334', 'a3333333-3333-3333-3333-333333333333', 'To a great extent', 4, 4),
('b3333333-3333-3333-3333-333333333335', 'a3333333-3333-3333-3333-333333333333', 'To a very great extent', 5, 5);

-- 6. Link Questions to Assessment
INSERT INTO assessments_questions (assessment_id, assessmentquestion_id) VALUES
('a1111111-1111-1111-1111-111111111111', 'a1111111-1111-1111-1111-111111111111'),
('a1111111-1111-1111-1111-111111111111', 'a2222222-2222-2222-2222-222222222222'),
('a1111111-1111-1111-1111-111111111111', 'a3333333-3333-3333-3333-333333333333'),
('a1111111-1111-1111-1111-111111111111', 'a4444444-4444-4444-4444-444444444444');

-- 7. Session
INSERT INTO assessment_session (id, user_name, assessment_id, responder_type, expected_responses) VALUES
('c1111111-1111-1111-1111-111111111111', 'Test Teacher', 'a1111111-1111-1111-1111-111111111111', 'self', 1);

-- 8. Instance
INSERT INTO assessment_instance (id, session_id, responder_name, completed_at) VALUES
('d1111111-1111-1111-1111-111111111111', 'c1111111-1111-1111-1111-111111111111', 'Test Teacher', null);

-- 9. Partial Answers (2 out of 3 Likert questions answered)
INSERT INTO assessment_answers (id, assessment_instance_id, assessment_answer_option_id, text_answer, numeric_value) VALUES
('e1111111-1111-1111-1111-111111111111', 'd1111111-1111-1111-1111-111111111111', 'b1111111-1111-1111-1111-111111111114', null, null),
('e2222222-2222-2222-2222-222222222222', 'd1111111-1111-1111-1111-111111111111', 'b2222222-2222-2222-2222-222222222225', null, null),
('e4444444-4444-4444-4444-444444444444', 'd1111111-1111-1111-1111-111111111111', null, 'I would like to improve my classroom management skills and develop more effective strategies for engaging all learners.', null);

-- Expected calculation results for instance d1111111-1111-1111-1111-111111111111:
-- total_questions: 4
-- answered_questions: 2 (only Likert questions with answer options count toward scoring)
-- completion_percentage: 50% (2 answered out of 4 total, including reflection)
-- total_score: 9 (4 + 5)
-- max_score: 10 (5 + 5)
-- normalized_score: (9 - 2) / (10 - 2) * 100 = 87.5%
