@extends('layouts.backend')

@section('content')
<div class="container">
    <div class="row">
        @include('admin.sidebar')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Bookings</div>
                <div class="card-body">
                    <!--<a href="{{ url('/admin/bookings/create') }}" class="btn btn-success btn-sm" title="Add New booking">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add New
                    </a> -->
                    <div class = "row">
                        <div id = "calendar"></div>
                    </div>
                    </br>
                    {!! Form::open(['method' => 'GET', 'url' => '/admin/bookings', 'class' => 'form-inline my-2 my-lg-0 float-right', 'role' => 'search'])  !!}
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
                        <span class="input-group-append">
                            <button class="btn btn-secondary" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                    {!! Form::close() !!}

                    <br/>
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>#</th><th>Profile </th><th>Customer Name</th><th>Time</th><th>Date</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $item)
                                <tr>
                                    <td>{{ $loop->iteration or $item->id }}</td>
                                    <td>{{ $item->bookedProfile->name }}</td>
                                    <td>{{ $item->customer_name }}</td>
                                    <td>{{ $item->time_converted }}</td>
                                    <td>{{ $item->date_converted }}</td>
                                    <td>
                                        <a href="{{ url('/admin/bookings/' . $item->id) }}" title="View booking"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
                                        <a href="{{ url('/admin/bookings/' . $item->id . '/edit') }}" title="Edit booking"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
                                        {!! Form::open([
                                        'method' => 'DELETE',
                                        'url' => ['/admin/bookings', $item->id],
                                        'style' => 'display:inline'
                                        ]) !!}
                                        {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i>', array(
                                        'type' => 'submit',
                                        'class' => 'btn btn-danger btn-sm',
                                        'title' => 'Delete booking',
                                        'onclick'=>'return confirm("Confirm delete?")'
                                        )) !!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $bookings->appends(['search' => Request::get('search')])->render() !!} </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="fullCalModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          
        </div>
        <div class="modal-body" id = "modalBody">
          <p>Some text in the modal.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
<script>
    $(document).ready(function() {
    // page is now ready, initialize the calendar...
    $('#calendar').fullCalendar({
    // put your options and callbacks here
    events : [
            @foreach($bookings as $task)
    {

    title : '{{ $task->customer_name }}',
    description : '<h2>Booking Details</h2><p> Booked Profile: <b>{{ $task->bookedProfile->name}}</b></p><p>Date: <b>{{ $task->date_converted }}</b></p><p>Time: <b>{{$task->time_converted}}</b></p><p>Customer Name: <b>{{ $task->customer_name }}</b></p><p>Services: <b>{{ $task->service_type }}</b></p>',
            start : '{{ gmdate("Y-m-d H:i:s", strtotime("$task->date_converted $task->time_converted "))}}',
            allDay: false,
    },
            @endforeach
    ],
    eventColor: '#5bc0de',
            eventRender: function(event, element) {
            $(element).tooltip({title: event.title});
            },
            eventClick: function(event){
            $('#modalTitle').html(event.title);
            $('#modalBody').html(event.description);
            $('#fullCalModal').modal();
            },
    })
    });
</script>
@endsection
