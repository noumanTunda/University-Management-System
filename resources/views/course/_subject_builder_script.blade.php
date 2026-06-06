<script>
  $(function() {
    var subjectCatalog = {!! json_encode($subjectCatalog) !!};
    var initialAssignments = {!! json_encode($initialAssignments) !!};
    var subjectMap = {};
    var assignments = {};
    var searchTerm = '';

    function toInt(value) {
      var parsed = parseInt(value, 10);
      return isNaN(parsed) ? 0 : parsed;
    }

    function durationYears() {
      return Math.max(1, Math.min(4, toInt($('#duration_years').val() || 4)));
    }

    function semesterLabel(semester) {
      var year = Math.ceil(semester / 2);
      var semesterName = semester % 2 === 1 ? 'Semester 1' : 'Semester 2';
      return 'Year ' + year + ' - ' + semesterName;
    }

    function subjectLabel(subject) {
      return subject.code + ' - ' + subject.name + ' (' + subject.department + ', ' + subject.credit + ' cr)';
    }

    function cardMarkup(subject, selected) {
      var classes = selected ? 'panel panel-success selected-subject-card' : 'panel panel-default pool-subject-card';
      var removeButton = selected ? '<button type="button" class="btn btn-xs btn-danger pull-right remove-course-subject">&times;</button>' : '';
      var inputs = selected ? '<input type="hidden" name="subjects[' + selected.index + '][id]" value="' + subject.id + '"><input type="hidden" name="subjects[' + selected.index + '][semester]" value="' + selected.semester + '">' : '';
      return '' +
        '<div class="' + classes + '" draggable="true" data-subject-id="' + subject.id + '" style="cursor: move; margin-bottom: 10px;">' +
          '<div class="panel-heading" style="padding: 6px 10px;">' + subject.code + removeButton + '</div>' +
          '<div class="panel-body" style="padding: 10px;">' +
            '<div><strong>' + subject.name + '</strong></div>' +
            '<div><small>' + subject.department + '</small></div>' +
            '<div><small>' + subject.credit + ' credits</small></div>' +
          '</div>' +
          inputs +
        '</div>';
    }

    function renderPool() {
      var html = '';
      subjectCatalog.forEach(function(subject) {
        if (assignments[String(subject.id)]) {
          return;
        }
        if (searchTerm && subjectLabel(subject).toLowerCase().indexOf(searchTerm) === -1) {
          return;
        }
        html += cardMarkup(subject, null);
      });
      if (!html) {
        html = '<div class="text-muted">No subjects match the search.</div>';
      }
      $('#subject-pool').html(html);
    }

    function renderGrid() {
      var html = '';
      var semesterCredits = {};
      var totalCredits = 0;
      var selectedCount = 0;
      var maxSemester = durationYears() * 2;

      Object.keys(assignments).forEach(function(subjectId) {
        var assignment = assignments[subjectId];
        if (!assignment || assignment.semester > maxSemester) {
          delete assignments[subjectId];
          return;
        }
      });

      for (var year = 1; year <= durationYears(); year++) {
        html += '<div class="row" style="margin-bottom: 15px;">';
        html += '<div class="col-md-12"><h5 style="margin-top: 0;">Year ' + year + '</h5></div>';
        for (var semester = (year - 1) * 2 + 1; semester <= year * 2; semester++) {
          html += '<div class="col-md-6">';
          html += '<div class="panel panel-default">';
          html += '<div class="panel-heading">';
          html += semesterLabel(semester) + ' <span class="badge pull-right">Credits: <span id="semester-credit-' + semester + '">0</span></span>';
          html += '</div>';
          html += '<div class="panel-body semester-dropzone" data-semester="' + semester + '" style="min-height: 150px; background: #fafafa;">';
          Object.keys(assignments).forEach(function(subjectId) {
            var assignment = assignments[subjectId];
            if (assignment.semester !== semester) {
              return;
            }
            var subject = subjectMap[subjectId];
            if (!subject) {
              return;
            }
            selectedCount++;
            totalCredits += parseFloat(subject.credit);
            semesterCredits[semester] = (semesterCredits[semester] || 0) + parseFloat(subject.credit);
            html += cardMarkup(subject, { semester: semester, index: selectedCount - 1 });
          });
          html += '</div></div></div>';
        }
        html += '</div>';
      }

      $('#semester-grid').html(html || '<div class="text-muted">Choose a duration to show semester rows.</div>');
      Object.keys(semesterCredits).forEach(function(semester) {
        $('#semester-credit-' + semester).text(semesterCredits[semester].toFixed(2));
      });
      $('#course-credit-total').text(totalCredits.toFixed(2));
      $('#course-subject-count').text(selectedCount);
    }

    function normalizeAssignments() {
      assignments = {};
      initialAssignments.forEach(function(item) {
        if (!item || !item.id) {
          return;
        }
        assignments[String(item.id)] = {
          semester: toInt(item.semester)
        };
      });
    }

    function rerender() {
      renderPool();
      renderGrid();
    }

    subjectCatalog.forEach(function(subject) {
      subjectMap[String(subject.id)] = subject;
    });

    normalizeAssignments();
    rerender();

    $('#subject-search').on('keyup', function() {
      searchTerm = $(this).val().toLowerCase();
      renderPool();
    });

    $('#duration_years').on('input change', function() {
      renderGrid();
      renderPool();
    });

    $(document).on('dragstart', '.pool-subject-card, .selected-subject-card', function(event) {
      event.originalEvent.dataTransfer.setData('text/plain', $(this).data('subject-id'));
    });

    $(document).on('dragover', '.semester-dropzone', function(event) {
      event.preventDefault();
    });

    $(document).on('drop', '.semester-dropzone', function(event) {
      event.preventDefault();
      var subjectId = event.originalEvent.dataTransfer.getData('text/plain');
      var semester = toInt($(this).data('semester'));
      if (!subjectId || !subjectMap[String(subjectId)]) {
        return;
      }
      assignments[String(subjectId)] = { semester: semester };
      rerender();
    });

    $(document).on('click', '.remove-course-subject', function() {
      var subjectId = $(this).closest('[data-subject-id]').data('subject-id');
      delete assignments[String(subjectId)];
      rerender();
    });
  });
</script>