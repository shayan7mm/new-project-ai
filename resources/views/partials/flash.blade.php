@if (session('status'))
    <div class="flash ok" role="status"><i class="fas fa-circle-check"></i> {{ session('status') }}</div>
@endif
@if (session('error'))
    <div class="flash err" role="alert"><i class="fas fa-circle-exclamation"></i> {{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="flash err" role="alert"><i class="fas fa-circle-exclamation"></i> لطفاً خطاهای فرم را بررسی کنید.</div>
@endif
