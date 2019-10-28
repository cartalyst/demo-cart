@if ($errors->any())
<div class="alert alert-danger" role="alert">
    Please check the form below for errors
</div>
@endif

@if ($message = Session::get('message'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-minus-square"></i></button>
        <strong>Success</strong> {{ $message }}
    </div>
@endif
