# Frontend Interview Task

**Time:** 2-3 hours | **Focus:** React, TypeScript, Data Visualization, UX

## Overview

Enhance the basic results display by adding visualizations, detailed breakdowns, and UX improvements. Transform API data into meaningful charts and create an intuitive dashboard.

**AI Usage:** You're welcome to use AI tools (ChatGPT, Claude, Copilot, etc.) for coding assistance - it's difficult to police and reflects real-world development. Please mention in your SOLUTION.md which AI tools you used and how. However, all written explanations must be your own work, not AI-generated.

## Phase 1: Dashboard Design (15-20 min)

Create a wireframe showing:
- Enhanced dashboard layout
- Visualizations to add (charts, graphs)
- Component hierarchy
- How data will be transformed

**Deliverable:** Sketch, wireframe, or diagram.

## Phase 2: Create Visualizations (45-60 min)

**Required:**
1. **Question-by-question breakdown**
   - Display each question with its answer
   - Show selected options and values
   - Indicate unanswered questions

2. **Score visualization** (choose at least one):
   - Bar chart (score per question)
   - Radar chart (element-based scores)
   - Gauge chart (overall percentage)

3. **Data transformation**
   - Extract/structure data from API
   - Calculate derived metrics
   - Handle missing data

**Recommendations:**
- Use charting library (Recharts, Chart.js, Victory)
- Ensure responsive sizing
- Use accessible colors
- Add clear labels and legends

**Deliverable:** Working components displaying question breakdown and at least one chart/visualization.

## Phase 3: UX Enhancements (45-60 min)

**Choose and implement at least 2:**

1. **Detailed question view** - Modal or expandable with full details
2. **Filtering/sorting** - By type, status, score, or element
3. **Comparison view** - Expected vs actual, highlight improvements
4. **Export** - PDF, clipboard, or JSON download
5. **Loading/error states** - Skeletons, retry, empty states
6. **Responsive design** - Mobile-friendly, touch interactions

**Focus on:**
- Smooth transitions
- Consistent design
- Professional polish
- Accessibility

**Deliverable:** At least 2 UX enhancements implemented and documented in SOLUTION.md.

## Getting Started

1. Review existing components:
   - `frontend/src/components/AssessmentResults.tsx`
   - `frontend/src/App.tsx`

2. Test API to understand data:
   ```bash
   curl http://localhost:8002/api/assessment/results/d1111111-1111-1111-1111-111111111111
   ```

3. View current UI: http://localhost:3000

4. Plan your approach:
   - Choose visualizations
   - Select charting library
   - Sketch layout

## Success Criteria

- Data loads and displays correctly
- At least one chart renders accurately
- Question breakdown shows all 4 questions (2 answered, 1 unanswered, 1 reflection)
- Loading states appear during fetch
- Error handling works (test with invalid ID)
- Mobile responsive
- Professional, polished design

## Deliverables

1. Dashboard wireframe
2. Implemented components with visualizations
3. **SOLUTION.md** with:
   - Design decisions
   - Visualizations chosen and why
   - Libraries/tools used (including AI tools if any)
   - UX enhancements implemented
   - Challenges and solutions
   - Testing approach

## Hints

- API returns all data needed: `element_scores`, `insights`, `total_questions`, etc.
- Recommended libraries: Recharts, Chart.js, Victory
- Use `useMemo` for data transformations
- Make chart components reusable
- Extract colors/themes to constants
- Use Flexbox/Grid for responsive layouts

## Stretch Goals (Optional)

- Interactive tooltips and drill-down
- Compare multiple instances
- Dark mode toggle
- Keyboard navigation and ARIA labels
- Lazy load chart libraries
- React Testing Library tests
