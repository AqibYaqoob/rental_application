<nav>
  <ul class="nav">
  	<li>
  		<a class="nav-link {!! Request::input('page') == null ? 'btn btn-danger btn-block' : '' !!}" href="{!! url('/admin/company/details/'.\Illuminate\Support\Facades\Crypt::encryptString($company['id'])) !!}">Company Info</a>
  	</li>
    <li>
      <a class="nav-link {!! Request::input('page') == 2 ? 'btn btn-danger btn-block' : '' !!}" href="{!! url('/admin/company/transactions?id='.\Illuminate\Support\Facades\Crypt::encryptString($company['id']).'&page=2') !!}">Transaction Report</a>
    </li>
  </ul>
</nav>