@if (request()->is('/'))
    <!-- Don't show the back-button on home page -->
@else
    <!-- Floating Back Button -->
    <button onclick="window.history.back()" 
            class="floating-back-button"
            aria-label="Kembali ke halaman sebelumnya">
      <i class="bi bi-arrow-left"></i>
    </button>
@endif

<style>
  .floating-back-button {
    position: fixed;
    bottom: 30px;
    left: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3494e6, #ec6ead);
    color: white;
    border: none;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 9999;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.95;
  }

  .floating-back-button:hover {
    background: linear-gradient(135deg, #2c83d6, #e55a9f);
    transform: scale(1.05) translateY(-5px);
    opacity: 1;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
  }

  .floating-back-button:active {
    transform: scale(0.98);
  }

  /* Animation when page loads */
  @keyframes floatIn {
    from { 
      transform: translateX(-30px) scale(0.8); 
      opacity: 0; 
    }
    to { 
      transform: translateX(0) scale(1); 
      opacity: 0.95; 
    }
  }

  .floating-back-button {
    animation: floatIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }

  /* Pulse animation to attract attention */
  @keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(52, 148, 230, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(52, 148, 230, 0); }
    100% { box-shadow: 0 0 0 0 rgba(52, 148, 230, 0); }
  }

  .floating-back-button {
    animation: floatIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }

  /* Add a subtle pulse effect on page load */
  .floating-back-button.pulse {
    animation: 
      floatIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275),
      pulse 2s 1s;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .floating-back-button {
      bottom: 20px;
      left: 20px;
      width: 50px;
      height: 50px;
      font-size: 20px;
    }
  }

  /* Hide on print */
  @media print {
    .floating-back-button {
      display: none;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const backButton = document.querySelector('.floating-back-button');
    
    // Add pulse effect class on page load
    backButton.classList.add('pulse');
    
    // Remove pulse class after animation completes
    setTimeout(() => {
      backButton.classList.remove('pulse');
    }, 3000);
    
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