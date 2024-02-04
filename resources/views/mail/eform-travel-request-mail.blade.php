@component('mail::message')
    <p style="text-align: center;">
        {{$requester->name}} from {{$requester->department->name}} has applied for Travel Authorization (E-Form)
        <br>and assigned <strong>YOU</strong> as the approver.
        <br>Click the link below to navigate to Dayang HR Portal.
    </p>
    <br>
    <p>
        {{--  MAIL CLASS CANNOT DETECT COMMENT      --}}
        {{--        <u>Here is the brief summary of the Redeem Replacement Leave:-</u>--}}
        {{--        <br>Start Date: {{ date('d-m-Y', strtotime($model['start_date'])) }}--}}
        {{--        <br>End Date: {{ date('d-m-Y', strtotime($model['end_date'])) }}--}}
        {{--        <br>Status: {{ $model['overall_status'] }}--}}
    </p>
    <br>
    <p style="text-align: center;">Click the link below to navigate to Dayang HR Portal.</p>
    @component('mail::button', ['url' => 'https://app.desb.net/hq/login', 'color' => 'success'])
        app.desb.net
    @endcomponent
@endcomponent
