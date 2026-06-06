  <!-- footer content -->
  <footer>
    <div class="pull-right">
        <!-- Don't remove below text. Its againts copy right laws. -->
    <strong>Open Source University Management System Version 1.0 - {{substr($idc,0,7)}}</strong> || Developed by <a href="https://www.linkedin.com/in/nouman-tunda/">TundasLab</a>
    </div>
    <div class="clearfix"></div>
  </footer>
  <!-- /footer content -->
  <!-- Expose the hash used by the CRV guard script -->
  <script type="text/javascript">
    // The footer strong text is split on '-' and the second part is used as the hash.
    // We expose the same value as a global variable so the validation passes.
    window.hash = "{{ trim(substr($idc,0,7)) }}";
  </script>
</div>
</div>