@php
    $initialAssignments = isset($initialAssignments) && is_array($initialAssignments) ? $initialAssignments : [];
    $subjectCatalog = isset($subjectCatalog) && is_array($subjectCatalog) ? $subjectCatalog : [];
    $durationYears = old('duration_years', isset($course) ? $course->duration_years : 4);
@endphp

<div class="row">
  <div class="col-md-12">
    <div style="border-bottom: 2px solid #E6F0F3; margin-bottom: 20px; padding-bottom: 5px;">
      <h3 style="color: #2A3F54; font-weight: 600; font-size: 16px; margin: 0;"><i class="fa fa-bars"></i> Curriculum Structure Setup</h3>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="well" style="margin-bottom: 15px;">
      Total Credits: <strong id="course-credit-total">0</strong>
      <span class="pull-right">Subjects Chosen: <strong id="course-subject-count">0</strong></span>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="x_panel">
      <div class="x_title"><h2>Subject Matrix</h2><div class="clearfix"></div></div>
      <div class="x_content">
        <div id="semester-grid">
          @for ($year = 1; $year <= 4; $year++)
            <div class="year-row" data-year="{{ $year }}" style="margin-bottom: 18px; {{ $year <= $durationYears ? '' : 'display:none;' }}">
              <h4 style="margin-top: 0;">Academic Year {{ $year }} <small>Year Credits: <strong class="year-credit-counter">0</strong></small></h4>
              <div class="row">
                @for ($semester = 1; $semester <= 2; $semester++)
                  <div class="col-md-6">
                    <div class="panel panel-default semester-panel" data-year="{{ $year }}" data-semester="{{ $semester }}">
                      <div class="panel-heading">
                        Semester {{ $semester }} <span class="badge pull-right">Credits: <span class="sem-credit-counter">0</span></span>
                      </div>
                      <div class="panel-body" style="max-height: 320px; overflow: auto; background: #fafafa;">
                        <input type="text" class="form-control input-sm semester-search-input" placeholder="Search subjects">
                        <div style="margin-top: 10px;">
                          @foreach ($subjectCatalog as $subject)
                            @php
                              $isAssigned = isset($initialAssignments[$year][$semester][$subject['id']]['selected']) && $initialAssignments[$year][$semester][$subject['id']]['selected'];
                            @endphp
                            <div class="subject-item-row" data-subject-id="{{ $subject['id'] }}" data-name="{{ strtolower($subject['name']) }}" data-code="{{ strtolower($subject['code']) }}" style="padding: 6px 8px; border: 1px solid #e5e5e5; margin-bottom: 6px; background: #fff; {{ $isAssigned ? '' : '' }}">
                              <label style="display:block; font-weight: normal; margin: 0; cursor: pointer;">
                                <input type="checkbox"
                                       class="subject-checkbox"
                                       data-credit="{{ $subject['credit'] }}"
                                       data-subject-id="{{ $subject['id'] }}"
                                       data-year="{{ $year }}"
                                       data-semester="{{ $semester }}"
                                       name="subjects[{{ $year }}][{{ $semester }}][{{ $subject['id'] }}][selected]"
                                       value="1"
                                       {{ $isAssigned ? 'checked' : '' }}>
                                <strong>{{ $subject['code'] }}</strong> - {{ $subject['name'] }} <span class="text-muted">({{ $subject['credit'] }} credits)</span>
                              </label>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  </div>
                @endfor
              </div>
            </div>
          @endfor
        </div>

        @if($errors->has('subjects'))
          <div class="alert alert-danger" style="margin-top: 10px;">{{ $errors->first('subjects') }}</div>
        @endif
      </div>
    </div>
  </div>
</div>