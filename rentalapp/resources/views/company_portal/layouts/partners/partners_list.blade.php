@extends( Auth::user()->Roles == 2 ? 'company_portal.layouts.app' :  (Auth::user()->Roles == 1 ? 'super_admin_portal.layouts.app' : 'partners_portal.layouts.app'))
@section('content')
    @php
        $counter = 1;
    @endphp
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/company/dashboard')}}">{{ trans('labels.dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ trans('labels.partners_list') }}</li>
        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <a class="btn" href="#"><i class="icon-graph"></i> &nbsp;{{ $balance . ' '. $currencyName }}</a>
            </div>
        </li>
    </ol>
    <!-- Main Content of the Page -->
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fa fa-align-justify"></i> {{ trans('labels.partners_list') }}
                                </div>
                                <div class="card-body">
                                    <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ trans('labels.username') }}</th>
                                            <th>{{ trans('labels.full_name') }}</th>
                                            <th>{{ trans('labels.date_registered') }}</th>
                                            <th>{{ trans('labels.currency') }}</th>
                                            <th>{{ trans('labels.status') }}</th>
                                            <th>{{ trans('labels.action') }}</th>
                                            <th>{{ trans('labels.reports') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(isset($partners) && count($partners)>0)
                                            @foreach($partners as $member)
                                                <tr>
                                                    <td>{{$counter}}</td>
                                                    <td>{{$member['Username']}}</td>
                                                    <td>
                                                        {{ucwords($member['staff_members']['staff_name'])}}
                                                    </td>
                                                    <td>{!! GeneralFunctions::convertToDateTimeToString($member['created_at']) !!}</td>
                                                    <td>{{ $member['partner_settings']['currency']['currency_list']['currency'].' ('.$member['partner_settings']['currency']['currency_list']['code'].')' }}</td>
                                                    <td>
                                                        @if($member['AccountStatus'] == 0)
                                                            <span class="badge badge-danger">{{ trans('labels.inactive') }}</span>
                                                        @else
                                                            <span class="badge badge-success">{{ trans('labels.active') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $edit_url_path = '/admin/company/partners/form?id='.GeneralFunctions::encryptString($member['id']);
                                                            $delete_url_path = '/admin/company/partners/delete/'.$member['id'];
                                                        @endphp
                                                        <div class="btn-group" role="group">
                                                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                {{ trans('labels.action') }}
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                                @if(GeneralFunctions::check_edit_permission('partner_list'))
                                                                    <a href="{{url($edit_url_path)}}" class="dropdown-item"><i class="fa fa-edit"></i>&nbsp; {{ trans('labels.edit') }}</a>
                                                                @endif
                                                                @if(GeneralFunctions::check_delete_permission('partner_list'))
                                                                    <a href="javascript:void(0)" class="dropdown-item delete" data-id="{{ \Illuminate\Support\Facades\Crypt::encryptString($member['id'])}}"><i class="fa fa-trash"></i>&nbsp; {{ trans('labels.delete') }}</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if(\App\Helpers\GeneralFunctions::check_view_permission('partner_account_reports'))
                                                            <a href="{{ route('individual.report',['id' =>  \Illuminate\Support\Facades\Crypt::encryptString($member['id'])]) }}" class="btn btn-sm btn-info" target="_blank"><i class="fa fa-external-link"></i> {{ trans('labels.view') }}</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--/.col-->
                    </div>
            </div>
            {{--Modal--}}
            <div class="remodal" data-remodal-id="delete_modal"
                 data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
                <button data-remodal-action="close" class="remodal-close"></button>
                <h1>{{ trans('labels.delete_partner') }}</h1>
                <p>
                    {{ trans('labels.are_you_sure_to_delete_it') }}
                </p>
                <form id="state_form" action="{{route('company.delete.partner')}}" method="POST">
                    <input type="hidden" id="data-id" name="id" value=""/>
                    {{ csrf_field() }}
                </form>
                <br>
                <button data-remodal-action="cancel" class="remodal-cancel">{{ trans('labels.cancel') }}</button>
                <button data-remodal-action="confirm" class="remodal-confirm">{{ trans('labels.ok') }}</button>
            </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $(document).on('click', '.delete', function () {
                var id = $(this).attr('data-id');
                $('#data-id').val(id);
                var inst = $('[data-remodal-id=delete_modal]').remodal();
                inst.open();
            });
            $(document).on('confirmation', '.remodal', function () {
                $('#state_form').submit()[0];
            });
        });
    </script>
@endsection