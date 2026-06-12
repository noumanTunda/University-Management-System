@extends('layouts.master')
@section('title', 'Journal Entries')
@section('content')
<div class="right_col" role="main">
  <div class="">
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title"><h2><i class="fa fa-book"></i> General Journal</h2><div class="clearfix"></div></div>
          <div class="x_content">
            <table class="table table-striped table-bordered">
              <thead><tr><th>Date</th><th>Description</th><th>Account</th><th>Debit</th><th>Credit</th></tr></thead>
              <tbody>
                @forelse($entries as $e)
                  @foreach($e->items as $j => $item)
                  <tr>
                    @if($j == 0)<td rowspan="{{$e->items->count()}}">{{$e->entry_date->format('d/m/Y')}}</td>
                    <td rowspan="{{$e->items->count()}}">{{$e->description}}</td>@endif
                    <td>{{$item->account->code ?? '-'}} - {{$item->account->name ?? '-'}}</td>
                    <td class="text-right">{{$item->debit > 0 ? number_format($item->debit, 2) : ''}}</td>
                    <td class="text-right">{{$item->credit > 0 ? number_format($item->credit, 2) : ''}}</td>
                  </tr>
                  @endforeach
                @empty <tr><td colspan="5">No entries.</td></tr> @endforelse
              </tbody>
            </table>
            <div class="text-center">{!! $entries->render() !!}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
