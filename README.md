# Assessment Results System - Technical Interview Task

A simplified assessment management system focusing on results calculation and display. This project demonstrates working with multi-entity domain models, business logic, and full-stack development.

## Getting Started

### Step 1: Create Your Repository

**First, create your own copy of this project:**

1. Click the **"Use this template"** button at the top of this repository
2. Click "Create a new repository"
3. Name it whatever you like (e.g., "assessment-submission")
4. Choose public or private (your choice)
5. Click "Create repository"

Your repository will be an independent copy - other candidates cannot find it through this template.

### Step 2: Prerequisites

Make sure you have these installed:
- **Docker Desktop** - Running and ready
- **Git**
- A terminal/command prompt

### Step 3: Clone and Setup

```bash
# Clone your new repository
git clone <your-new-repo-url>
cd <your-repo-name>

# Copy environment file
cp backend/.env.example backend/.env

# Start all services
docker-compose up -d
```

Dependencies are automatically installed during the Docker build process. **Wait ~30 seconds** for all services to be ready.

### Step 4: Verify Everything Works

**Check services are running:**
```bash
docker-compose ps
```

All services should show "Up" and "healthy".

**Test the API:**
```bash
curl http://localhost:8002/api/assessment/results/d1111111-1111-1111-1111-111111111111
```

Should return JSON with assessment results.

**Open the frontend:**
```
http://localhost:3000
```

You should see the Assessment Results interface with pre-loaded data.

## Your Assessment Task

**Expected Time Needed:** 2-3 hours (not including setup time)

Choose the appropriate task based on your role:

- **Backend/Full-Stack Role:** Complete [TASK_FULL_STACK.md](TASK_FULL_STACK.md)
- **Frontend Role:** Complete [TASK_FRONTEND.md](TASK_FRONTEND.md)

### What to Do

1. Read your task file carefully
2. Follow the phases in order
3. Commit your work regularly (we like to see your process)
4. Create a `SOLUTION.md` file explaining:
   - Your approach
   - Design decisions
   - Tools/libraries used (including AI tools if any)
   - Testing steps
   - Challenges faced and how you solved them

### What We're Looking For

- **Understanding** - Do you grasp the existing architecture?
- **Implementation** - Does your code work and follow good practices?
- **Problem Solving** - How do you approach challenges?
- **Communication** - Can you explain your decisions clearly?

### Rules & Guidelines

✅ **Allowed:**
- Use the internet, documentation, Stack Overflow
- Use AI tools (ChatGPT, Claude, Copilot, etc.) - just mention which ones in SOLUTION.md
- Ask us clarifying questions via email

❌ **Not Allowed:**
- Copying solutions from other candidates
- Having someone else complete it for you
- AI-generated explanations in SOLUTION.md (code is fine, explanations must be yours)

## Project Overview

### Tech Stack

- **Backend**: Symfony 6.4 (PHP 8.2) with Doctrine ORM
- **Frontend**: React 18 with TypeScript and Vite
- **Database**: PostgreSQL 15
- **Architecture**: Domain-Driven Design with Repository and Service patterns

### Services

| Service | URL | Credentials |
|---------|-----|-------------|
| Frontend | http://localhost:3000 | N/A |
| Backend API | http://localhost:8002 | N/A |
| Database | localhost:5432 | interview / password |
| DB Name | interview_db | - |

**Pre-seeded Instance ID:** `d1111111-1111-1111-1111-111111111111`

### Database Schema

The database is automatically initialized with seed data including:
- 1 assessment template (element "1.1")
- 4 questions (3 Likert scale + 1 reflection)
- 1 session with 1 instance
- Partial answers (2/3 Likert questions answered)

### Backend Structure

```
backend/
├── config/
│   └── packages/          # Symfony configuration
├── src/
│   ├── Controller/
│   │   └── Assessment/    # API endpoints
│   └── Domain/            # Domain entities, services
│       ├── Assessment.php
│       ├── AssessmentQuestion.php
│       ├── AssessmentAnswerOption.php
│       ├── AssessmentSession.php
│       ├── AssessmentInstance.php
│       ├── AssessmentAnswer.php
│       ├── AssessmentRepository.php
│       └── AssessmentService.php
└── migrations/            # Database initialization
```

### Frontend Structure

```
frontend/
└── src/
    ├── components/
    │   └── AssessmentResults.tsx  # Main results display
    ├── App.tsx                     # Main application
    └── main.tsx                    # Entry point
```


### Scoring Algorithm

The system uses a normalization formula to convert Likert scale (1-5) to percentage (0-100%):

```
normalized_score = (total_score - answered_count) / (max_score - answered_count) * 100
```


## Troubleshooting

### Database not initializing

```bash
# Check database logs
docker-compose logs db

# If needed, manually run migration
docker-compose exec db psql -U interview -d interview_db -f /docker-entrypoint-initdb.d/001_init_and_seed.sql
```

### Backend errors

```bash
# View logs
docker-compose logs backend

# Check PHP syntax
docker-compose exec backend php -l src/Domain/Assessment.php

# Verify composer installed correctly
docker-compose exec backend composer dump-autoload
```

### Frontend not loading

```bash
# View logs
docker-compose logs frontend

# Reinstall dependencies
docker-compose exec frontend rm -rf node_modules
docker-compose exec frontend npm install

# Check Vite config
docker-compose exec frontend cat vite.config.ts
```

### Port conflicts

**Error: `Bind for 0.0.0.0:5432 failed: port is already allocated`**

This means PostgreSQL is already running on your machine. Choose one solution:

**Option 1: Stop local PostgreSQL** (Recommended)
```bash
# Linux
sudo service postgresql stop

# macOS (Homebrew)
brew services stop postgresql

# Windows (Run as Administrator in PowerShell)
Stop-Service postgresql-x64-*
```

**Option 2: Stop conflicting Docker containers**
```bash
# See what's using the port
docker ps

# Stop the conflicting container
docker stop <container-name>
```

**Option 3: Change the port in docker-compose.yml**
```yaml
db:
  ports:
    - "5433:5432"  # Use 5433 instead of 5432
```

After changing ports, restart:
```bash
docker-compose down
docker-compose up -d
```

**Other port conflicts:**
- Port 3000 (frontend): Change to `3001:3000`
- Port 8002 (backend): Change to `8003:8000`

### Resetting Everything

To start fresh:

```bash
# Stop and remove all containers, networks, volumes
docker-compose down -v

# Restart
docker-compose up -d

# Reinstall dependencies if needed
docker-compose exec backend composer install
docker-compose exec frontend npm install
```

## Useful Commands

```bash
# View all logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f backend
docker-compose logs -f frontend

# Restart a service
docker-compose restart backend

# Stop all services
docker-compose down

# Access database
docker-compose exec db psql -U interview -d interview_db

# Run SQL query
docker-compose exec db psql -U interview -d interview_db -c "SELECT * FROM assessment;"

# Access backend shell
docker-compose exec backend sh

# Access frontend shell
docker-compose exec frontend sh
```

## Submission

When you're ready to submit:

1. Ensure all your code is committed and pushed to your repository
2. Include a `SOLUTION.md` file with your explanations
3. See [SUBMISSION_INSTRUCTIONS.md](SUBMISSION_INSTRUCTIONS.md) for detailed submission steps

**Deadline:** Please submit even if incomplete. We'd rather see partial work with good explanations than nothing at all.

## Questions?

If you have questions about:
- **Setup issues:** Check the troubleshooting section above
- **Task requirements:** Email us - we're happy to clarify!
- **Submission process:** See [SUBMISSION_INSTRUCTIONS.md](SUBMISSION_INSTRUCTIONS.md) or email us

## Development Notes

- No authentication required - focus is on assessment logic
- Entities use Doctrine annotations for ORM mapping
- Timestamps handled by Gedmo Timestampable
- Repository extends Doctrine EntityRepository
- Service layer handles business logic
- Frontend uses Axios for API calls
- Styling uses plain CSS (no framework dependency)

## License

MIT - Created for technical interview purposes

---

Good luck! We're excited to see what you build.
