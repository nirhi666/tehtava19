</div> <!-- /.container -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function(){
  setTimeout(function(){
    document.querySelectorAll('.alert').forEach(function(el){
      el.style.transition="opacity 1s ease"; el.style.opacity="0";
      setTimeout(()=>el.classList.add('d-none'), 1000);
    });
  }, 3000);
});
</script>
</body>
</html>



