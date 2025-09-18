<!-- Floating Back Button -->
<button onclick="window.history.back()" 
        class="floating-back-button"
        aria-label="Go back to previous page">
  ←
</button>

{{-- <a href="javascript:history.back()" class="floating-back-button" title="Go back">
  <i class="bi bi-arrow-left"></i> Back
</a> --}}

<style>
.floating-back-button {
  position: fixed;
  bottom: 30px;
  left: 30px;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: #4a6fa5;
  color: white;
  border: none;
  font-size: 24px;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  z-index: 9999;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.9;
}

.floating-back-button:hover {
  background-color: #3a5a8a;
  transform: scale(1.1);
  opacity: 1;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

/* Animation when page loads */
@keyframes floatIn {
  from { transform: translateX(-20px); opacity: 0; }
  to { transform: translateX(0); opacity: 0.9; }
}

.floating-back-button {
  animation: floatIn 0.5s ease-out;
}

/* Hide on print */
@media print {
  .floating-back-button {
    display: none;
  }
}
</style>

<!-- Optional: Fade in/out based on scroll position -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const backButton = document.querySelector('.floating-back-button');
  let lastScrollPosition = 0;
  
  window.addEventListener('scroll', function() {
    const currentScrollPosition = window.pageYOffset;
    
    // Hide when scrolling down, show when scrolling up
    if (currentScrollPosition > lastScrollPosition && currentScrollPosition > 100) {
      backButton.style.transform = 'translateX(-100px)';
    } else {
      backButton.style.transform = 'translateX(0)';
    }
    
    lastScrollPosition = currentScrollPosition;
  });
});
</script>