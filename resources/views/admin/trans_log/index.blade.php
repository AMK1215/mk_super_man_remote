@extends('admin_layouts.app')
@section('styles')
<style>
  .transparent-btn {
    background: none;
    border: none;
    padding: 0;
    outline: none;
    cursor: pointer;
    box-shadow: none;
    appearance: none;
    /* For some browsers */
  }


  .custom-form-group {
    margin-bottom: 20px;
  }

  .custom-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
  }

  .custom-form-group input,
  .custom-form-group select {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e1e1e1;
    border-radius: 5px;
    font-size: 16px;
    color: #333;
  }

  .custom-form-group input:focus,
  .custom-form-group select:focus {
    border-color: #d33a9e;
    box-shadow: 0 0 5px rgba(211, 58, 158, 0.5);
  }

  .submit-btn {
    background-color: #d33a9e;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
    font-weight: bold;
  }

  .submit-btn:hover {
    background-color: #b8328b;
  }
</style>
@endsection
@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header pb-0">
        <div class="card-body">
          <h5 class="mb-0">Transfer Logs</h5>
        </div>
        <form action="" method="GET">
          <div class="row mt-3">
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">Type</label>
                <select name="type" id="" class="form-control">
                  <option value="">Select Type</option>
                  <option value="withdraw">Withdraw</option>
                  <option value="deposit">Deposit</option>
                </select>
              </div>
            </div>
            @can('master_access')
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">CreatedBy</label>
                <select name="agent_id" id="" class="form-control">
                  <option value="">Select Agent</option>
                  <option value="{{Auth::id()}}">{{Auth::user()->name}}</option>
                  @foreach(Auth::user()->children as $agent)
                  <option value="{{$agent->id}}">{{$agent->name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            @endcan
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">Start Date</label>
                <input type="date" class="form-control" name="start_date" value="{{request()->get('start_date')}}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">EndDate</label>
                <input type="date" class="form-control" name="end_date" value="{{request()->get('end_date')}}">
              </div>
            </div>
            <div class="col-md-3">
              <button class="btn btn-sm btn-primary mt-3" id="search" type="submit">Search</button>
              <a href="{{route('admin.transferLog')}}" class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Refresh">
                <i class="material-icons text-lg mt-0">refresh</i>
              </a>
            </div>
          </div>
        </form>
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="users-search">
          <thead class="thead-light">

            <tr>
              <th>Date</th>
              <th>To User</th>
              <th>Amount</th>
              <th>Type</th>
              <th>CreatedBy</th>
              <th>Note</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transferLogs as $log)

            <tr>
              <td>
                {{ $log->created_at }}
              </td>
              <td>{{ $log->targetUser->name }}</td>
              <td>
                <div class="d-flex align-items-center text-{{$log->type =='withdraw' ? 'success' : 'danger'}} text-gradient text-sm font-weight-bold ms-auto"> {{ abs($log->amountFloat)}}</div>
              </td>
              <td>
                @if($log->type == 'withdraw')
                <p class="text-success font-weight-bold">Deposit</p>
                @else
                <p class="text-danger font-weight-bold">Withdraw</p>
                @endif
              </td>
              <td>{{$log->user->name ?? ''}}</td>
              <td>{{$log->note}}</td>
            </tr>
            @endforeach
          </tbody>

        </table>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
{{-- <script>
    const dataTableSearch = new simpleDatatables.DataTable("#datatable-search", {
      searchable: true,
      fixedHeight: true
    });
  </script> --}}
<script>
  if (document.getElementById('users-search')) {
    const dataTableSearch = new simpleDatatables.DataTable("#users-search", {
      searchable: true,
      fixedHeight: false,
      perPage: 7
    });

    document.querySelectorAll(".export").forEach(function(el) {
      el.addEventListener("click", function(e) {
        var type = el.dataset.type;

        var data = {
          type: type,
          filename: "material-" + type,
        };

        if (type === "csv") {
          data.columnDelimiter = "|";
        }

        dataTableSearch.export(data);
      });
    });
  };
</script>
<script>
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
</script>
@endsection