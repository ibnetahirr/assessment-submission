# Assessment Frontend - React + TypeScript

## Tech Stack

- **React 18** - UI library
- **TypeScript** - Type safety
- **Vite** - Build tool and dev server
- **Axios** - HTTP client
- **Plain CSS** - No framework dependency

## Structure

```
src/
├── components/
│   └── AssessmentResults.tsx  # Main results display component
├── App.tsx                     # Root component with instance selector
├── main.tsx                    # React entry point
└── *.css                       # Component styles
```

## Key Components

### AssessmentResults

Displays complete assessment results including:
- Progress circle (completion percentage)
- Overall score with color coding
- Element-by-element breakdown
- Insights with positive/negative indicators

**Props:**
```typescript
interface Props {
  instanceId: string  // UUID of assessment instance
}
```

**API Integration:**
```typescript
const response = await axios.get(
  `${API_URL}/api/assessment/results/${instanceId}`
)
```

## Styling Approach

Uses plain CSS with:
- Flexbox for layouts
- CSS variables could be added for theming
- Responsive design with media queries
- Color coding based on score thresholds:
  - Green (≥80%): Strong
  - Yellow (60-80%): Good
  - Red (<60%): Needs development

## Running Locally

```bash
# Install dependencies
npm install

# Start dev server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## Development Tips

- API URL configured via `VITE_API_URL` environment variable
- Default: `http://localhost:8002`
- Uses React hooks (useState, useEffect) for state management
- Axios for HTTP (could be replaced with fetch)
- TypeScript interfaces for type safety
- No state management library needed (simple app)

## Extending the Frontend

If implementing additional features:

1. **Add React Query:**
   ```bash
   npm install @tanstack/react-query
   ```
   Provides caching, loading states, error handling

2. **Add routing:**
   ```bash
   npm install react-router-dom
   ```
   For multiple pages

3. **Add UI library:**
   - Material-UI, Chakra UI, or Ant Design
   - Would replace custom CSS components

4. **Add charting:**
   ```bash
   npm install recharts
   ```
   For historical trends, comparisons

## Testing

To add tests:

```bash
npm install --save-dev vitest @testing-library/react @testing-library/jest-dom
```

Example test:
```typescript
import { render, screen } from '@testing-library/react'
import AssessmentResults from './AssessmentResults'

test('displays loading state', () => {
  render(<AssessmentResults instanceId="test-id" />)
  expect(screen.getByText(/loading/i)).toBeInTheDocument()
})
```

## Environment Variables

Create `.env.local`:
```
VITE_API_URL=http://localhost:8002
```

Accessed in code via:
```typescript
import.meta.env.VITE_API_URL
```
