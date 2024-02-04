@component('mail::message')
    <p style="text-align: center;">
        {{$user->name}} from {{$user->department->name}} has applied Redeem Replacement Leave
        <br>and assigned <strong>YOU</strong> as the approver.
        <br>Click the link below to navigate to Dayang HR Portal.
    </p>
    <br>
        <p>
            <u>Here is the brief summary of the Redeem Replacement Leave:-</u>
            <br>Start Date: {{ date('d-m-Y', strtotime($model['start_date'])) }}
            <br>End Date: {{ date('d-m-Y', strtotime($model['end_date'])) }}
            <br>Status: {{ $model['overall_status'] }}
        </p>
    <br>
    <p style="text-align: center;">Click the link below to navigate to Dayang HR Portal.</p>
    @component('mail::button', ['url' => 'https://app.desb.net/hq/login', 'color' => 'success'])
        app.desb.net
    @endcomponent
@endcomponent

