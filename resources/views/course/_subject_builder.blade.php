@php
    $initialAssignments = isset($initialAssignments) && is_array($initialAssignments) ? array_values($initialAssignments) : [];
    $subjectCatalog = isset($subjectCatalog) && is_array($subjectCatalog) ? $subjectCatalog : [];
@endphp

<div class="form-group">
  <label class="control-label col-md-3 col-sm-3 col-xs-12">Subjects</label>
  <div class="col-md-9 col-sm-9 col-xs-12">
    <div class="row">
      <div class="col-md-4">
        <div class="x_panel">
          <div class="x_title"><h4>Subject Pool</h4></div>
          <div class="x_content">
            <input type="text" id="subject-search" class="form-control" placeholder="Search subjects">
            <div id="subject-pool" class="course-subject-pool" style="max-height: 520px; overflow: auto; margin-top: 10px;"></div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="x_panel">
          <div class="x_title">
            <h4>Academic Year Grid</h4>
          </div>
          <div class="x_content">
            <div class="alert alert-info" style="margin-bottom: 15px;">
              Drag a subject into a semester box. Removing it returns it to the pool.
            </div>
            <div id="semester-grid"></div>
            <div class="well" style="margin-top: 15px;">
              Total Credits: <strong id="course-credit-total">0</strong>
              <span class="pull-right">Selected Subjects: <strong id="course-subject-count">0</strong></span>
            </div>
            <div class="text-danger">{{ $errors->first('subjects') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>