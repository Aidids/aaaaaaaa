@component('mail::message')
    <p style="text-align: center;">
        {{ $user->name }} your <br>
        <strong style="text-transform: uppercase;"> Redeem Offshore Leave</strong>
        Request has been
        <strong style="text-transform: uppercase;">{{ $model->overall_status }}</strong>.
    @if($model['overall_status'] === 'completed')
        <p style="text-align: center;"> Your Offshore leave balance has been updated.</p>
        <p style="text-align: center;"> New Balance added: {{ $model['balance_received'] }} Days</p>
    @endif
    </p>
    <br>
    <p>
        <u>Here is the brief summary of the Redeem Offshore Leave:-</u>
        <br>Start Date: {{ $model['start_date'] }}
        <br>End Date: {{ $model['end_date'] }}
        <br>Status: {{ $model['overall_status'] }}
    </p>
    <br>
    <p style="text-align: center;">Click the link below to navigate to Dayang HR Portal.</p>
    @component('mail::button', ['url' => 'https://app.desb.net/hq/login', 'color' => 'success'])
        app.desb.net
    @endcomponent
@endcomponent
