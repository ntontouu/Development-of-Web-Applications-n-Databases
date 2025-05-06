</main>
    
<footer class="main-footer">
  <div class="footer-content">
    <p>© <?= date('Y') ?> Training Tracker by ntontouu</p>
    <div class="footer-info">
      <span><i class="fas fa-file-contract"></i> Όροι</span>
      <span><i class="fas fa-lock"></i> Απόρρητο</span>
      <span><i class="fas fa-envelope"></i> Επικοινωνία</span>
    </div>
  </div>
</footer>

<script>
  // Mobile menu functionality
  document.getElementById('mobileMenuBtn').addEventListener('click', function() {
    document.querySelector('.main-nav').classList.toggle('active');
  });
</script>
</body>
</html>