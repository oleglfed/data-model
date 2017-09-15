<h3>{{ $title }}</h3>
<table class="table">
    <thead>
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Size</th>
        <th>Default</th>
    </tr>
    </thead>

    <tbody>
    @foreach($fields as $field)
        @continue($field->Field == 'id')

        @php
            preg_match('/(?<=\()(.+)(?=\))/is', $field->Type, $match);
            $type = preg_replace('/\(.{0,}\)/', '', $field->Type);
        @endphp

        <tr>
            <td>{{ $field->Field }}</td>
            <td>{{ $type }}</td>
            <td>{{ (is_string(array_first($match)) ? array_first($match) : '') }}</td>
            <td>{{ (is_null($field->Default) ? 'NULL' : (string) $field->Default) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<br>
<br>
<hr>