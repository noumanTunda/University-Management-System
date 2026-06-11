@extends('layouts.master')

@section('title', 'Edit User')

@section('content')
        <div class="right_col" role="main">
          <div class="">
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Edit User<small> Update user information.</small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form class="form-horizontal form-label-left" novalidate method="post" action="{{URL::route('user.update', $user->id)}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                              <div class="col-md-4">
                            <div class="form-group">
                              <label class="control-label" for="firstname">First Name <span class="required">*</span>
                              </label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="fa fa-info blue"></i></span>
                                  <input id="name" type="text" class="form-control"  name="firstname" value="{{ $user->firstname }}" required="required">
                              </div>
                              <span class="text-danger">{{ $errors->first('firstname') }}</span>
                            </div>
                          </div>
                              <div class="col-md-4">
                            <div class="form-group">
                              <label class="control-label" for="lastname">Last Name <span class="required">*</span>
                              </label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="fa fa-info blue"></i></span>
                                  <input id="name" type="text" class="form-control"  name="lastname" value="{{ $user->lastname }}" required="required">
                              </div>
                              <span class="text-danger">{{ $errors->first('lastname') }}</span>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label class="control-label" for="login">User Name<span class="required">*</span>
                              </label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-info blue"></i></span>
                                <input type="text" class="form-control"  name="login" value="{{ $user->login }}" required="required" disabled>
                                <input type="hidden" name="login" value="{{ $user->login }}">
                              </div>
                              <span class="text-danger">{{ $errors->first('login') }}</span>
                            </div>
                          </div>
                        </div>
                          <div class="row">
                            <div class="col-md-4">
                            <div class="form-group">
                              <label class="control-label" for="group">Group<span class="required">*</span>
                              </label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-info blue"></i></span>
                                <select class="form-control"  name="group" required="required">
                                  <option value="Admin" {{ $user->group == 'Admin' ? 'selected' : '' }}>Admin</option>
                                  <option value="Teacher" {{ $user->group == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                                  <option value="HeadOfDepartment" {{ $user->group == 'HeadOfDepartment' ? 'selected' : '' }}>Head of Department</option>
                                  <option value="Account" {{ $user->group == 'Account' ? 'selected' : '' }}>Account</option>
                                </select>
                              </div>
                              <span class="text-danger">{{ $errors->first('group') }}</span>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label class="control-label" for="email">Email
                              </label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope blue"></i></span>
                                <input type="text" id="email" name="email" value="{{ $user->email }}" class="form-control">
                              </div>
                              <span class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label class="control-label" for="description">Description
                              </label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-info blue"></i></span>
                                <textarea id="description" name="description" class="form-control col-md-7 col-xs-12">{{ $user->description }}</textarea>
                              </div>
                              <span class="text-danger">{{ $errors->first('description') }}</span>
                            </div>
                          </div>
                        </div>
                            <div class="row">
                            <div class="col-md-4">
                            <div class="form-group">
                              <label class="control-label" for="password">New Password <small>(leave blank to keep current)</small>
                              </label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-key blue"></i></span>
                                <input class="form-control"  name="password" value="" type="password">
                              </div>
                              <span class="text-danger">{{ $errors->first('password') }}</span>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"> Update</i></button>
                            <a href="{{URL::route('user.index')}}" class="btn btn-default pull-right">Cancel</a>
                        </div>
                      </form>
                  </div>
                </div>
              <div class="clearfix"></div>
          </div>
        </div>
        </div>
        </div>
@endsection
