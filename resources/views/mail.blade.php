<div>
    @if($title)
        <h3>{{ $title }}</h3>
    @endif
    @if($body)
        <pre>
            {{$body}}
        </pre>
    @endif
</div>

<style>
    pre{
        font-family: 'Nunito', sans-serif;
        font-size: 16px;
        line-height: 26px;
    }
</style>
