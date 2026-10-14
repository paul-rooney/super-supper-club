import './style.css'

// Proof the bundled TypeScript runs on every page PHP serves.
// Edit this line while `npm run dev` is running and HMR hot-swaps the module
// with no full reload.
console.log('main.ts loaded — HMR is live ✨')

// Progressive enhancement: wire behaviour only where the markup exists.
// (The button is only on the home page, but the same bundle loads everywhere.)
const counter = document.querySelector<HTMLButtonElement>('#counter')
if (counter) {
  let count = 0
  const render = () => (counter.textContent = `count is ${count}`)
  counter.addEventListener('click', () => {
    count += 1
    render()
  })
  render()
}
