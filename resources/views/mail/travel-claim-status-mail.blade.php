@component('mail::message')
    <p style="text-align: center;">
        {{ $recipient->name }} your Travel Claim has been <strong style="text-transform: uppercase;">{{$model['status']}}</strong>.
    </p>
    <br>
    <p style="text-align: center;">Click the link below to navigate to Dayang HR Portal.</p>
    @component('mail::button', ['url' => 'https://app.desb.net/hq/login', 'color' => 'success'])
        app.desb.net
    @endcomponent
@endcomponent
