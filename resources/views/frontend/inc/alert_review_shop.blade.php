@if(Auth::user()->seller->verification_status !== 1)
    <div class="alert alert-danger text-center text-main text-dark text-bold">{{ __('Your Shop has been created successfully!').' '. __('Please wait approved, Your request will be reviewed by iBuy.ps team.') }}</div>
@endif
