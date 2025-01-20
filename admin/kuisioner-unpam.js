
// Setuju
document.querySelectorAll('[role="radio"]').forEach((radio) => {
  if (radio.getAttribute('aria-label') === "Setuju") {
    radio.click();
  }
});

// Full Random

const groups = document.querySelectorAll('[role="radiogroup"]'); 

groups.forEach((group) => {
  const radios = Array.from(group.querySelectorAll('[role="radio"]')); 
  if (radios.length > 0) {
    const randomIndex = Math.floor(Math.random() * radios.length); 
    radios[randomIndex].click();
  }
});

// Random
const groups = document.querySelectorAll('[role="radiogroup"]');

groups.forEach((group) => {
  const radios = Array.from(group.querySelectorAll('[role="radio"]'))
        .filter(radio => radio.getAttribute('aria-label') !== "Sangat Tidak Setuju");
    
  const adjustedRadios = radios.flatMap(radio => {
    const label = radio.getAttribute('aria-label');
    return label === "Tidak Setuju" ? [radio] : [radio, radio]; 
  });

  if (adjustedRadios.length > 0) {
    const randomIndex = Math.floor(Math.random() * adjustedRadios.length);
    adjustedRadios[randomIndex].click();
  }
});
