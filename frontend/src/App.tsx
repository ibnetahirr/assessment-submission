import { useState } from 'react'
import AssessmentResults from './components/AssessmentResults'
import './App.css'

function App() {
  const [instanceId, setInstanceId] = useState('d1111111-1111-1111-1111-111111111111')

  return (
    <div className="app">
      <header className="app-header">
        <h1>Assessment Results System</h1>
        <p>Technical Interview Task</p>
      </header>

      <main className="app-main">
        <div className="instance-selector">
          <label htmlFor="instance-id">Assessment Instance ID:</label>
          <input
            id="instance-id"
            type="text"
            value={instanceId}
            onChange={(e) => setInstanceId(e.target.value)}
            placeholder="Enter instance ID"
          />
        </div>

        <AssessmentResults instanceId={instanceId} />
      </main>
    </div>
  )
}

export default App
