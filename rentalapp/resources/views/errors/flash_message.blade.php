@if(count($errors) > 0)
    <div class="col-sm-12">
        <div class="alert alert-danger alert-list">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
@if(\Session::has('status'))
    <div class="col-sm-12">
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p>{{ \Session::get('status')}}</p>
        </div>
    </div>
@endif
@if(\Session::has('success'))
    <div class="col-sm-12">
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p>{{ \Session::get('success')}}</p>
        </div>
    </div>
@endif
@if(\Session::has('error_msg'))
    <div class="col-sm-12">
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p>{{ \Session::get('error_msg')}}</p>
        </div>
    </div>
@endif
@if(\Illuminate\Support\Facades\Session::has('errors_array'))
    <div class="col-sm-12">
        <div class="alert alert-danger">
            @foreach(session('errors_array') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    </div>
@endif
<div class="msg-box alert" style="display: none; width: 100%;">
    <ul style="text-decoration: none;" id="msg-list">

    </ul>
</div>
