<script>
  $(function() {
    var subjectCatalog = {!! json_encode($subjectCatalog) !!};
    var initialAssignments = {!! json_encode($initialAssignments) !!};
    var subjectMap = {};

    function toInt(value) {
      var parsed = parseInt(value, 10);
      return isNaN(parsed) ? 0 : parsed;
    }

    function durationYears() {
      return Math.max(1, Math.min(4, toInt($('#duration_years').val() || 4)));
    }

    subjectCatalog.forEach(function(subject) {
      subjectMap[String(subject.id)] = subject;
    });

    function updateCounters() {
      var totalCredits = 0;
      var selectedCount = 0;

      $('.year-row:visible').each(function() {
        var yearTotal = 0;
        var yearRow = $(this);

        yearRow.find('.semester-panel').each(function() {
          var panel = $(this);
          var semTotal = 0;

          panel.find('.subject-checkbox:checked').each(function() {
            semTotal += parseFloat($(this).data('credit')) || 0;
            selectedCount++;
          });

          panel.find('.sem-credit-counter').text(semTotal.toFixed(2));
          yearTotal += semTotal;
        });

        yearRow.find('.year-credit-counter').text(yearTotal.toFixed(2));
        if (yearRow.is(':visible')) {
          totalCredits += yearTotal;
        }
      });

      $('#course-credit-total').text(totalCredits.toFixed(2));
      $('#course-subject-count').text(selectedCount);
    }

    function syncSubjectVisibility(changed) {
      var subjectId = String(changed.data('subject-id'));
      if (changed.is(':checked')) {
        $('.subject-checkbox[data-subject-id="' + subjectId + '"]').not(changed).each(function() {
          $(this).prop('checked', false).closest('.subject-item-row').hide();
        });
      } else {
        $('.subject-checkbox[data-subject-id="' + subjectId + '"]').not(changed).closest('.subject-item-row').show();
      }
      updateCounters();
    }

    function refreshSubjectState() {
      $('.subject-item-row').show();
      $('.subject-checkbox:checked').each(function() {
        syncSubjectVisibility($(this));
      });
      updateCounters();
    }

    $('.semester-search-input').on('keyup', function() {
      var query = $(this).val().toLowerCase().trim();
      $(this).closest('.semester-panel').find('.subject-item-row').each(function() {
        var row = $(this);
        var matches = row.data('name').indexOf(query) !== -1 || row.data('code').indexOf(query) !== -1;
        row.toggle(matches || !query);
      });
    });

    $('#duration_years').on('input change', function() {
      var years = durationYears();
      $('.year-row').each(function() {
        var rowYear = parseInt($(this).data('year'), 10);
        if (rowYear <= years) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
      refreshSubjectState();
    });

    $(document).on('change', '.subject-checkbox', function() {
      syncSubjectVisibility($(this));
    });

    refreshSubjectState();
  });
</script>