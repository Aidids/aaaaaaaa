@component('mail::message')
    <p style="text-align: center;">
        {{ $requester->name }} has cancel their <br>
        <strong style="text-transform: uppercase;">Travel Authorization (E-Form) Request</strong>
        <br> on <strong>{{ date('d M Y', strtotime($eform->created_at)) }}</strong>
    </p>
    <p style="text-align: center;">Click the link below to navigate to Dayang HR Portal.</p>
    @component('mail::button', ['url' => 'https://app.desb.net/hq/login', 'color' => 'success'])
        app.desb.net
    @endcomponent
@endcomponent
