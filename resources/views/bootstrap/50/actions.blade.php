@if(isset($actionBtns) && count($actionBtns))
    <td class="text-center">
        <ul class="table-controls">
            @foreach($actionBtns as $action)
                @php
                    $parameters = [];
                    foreach ($action->param as $param => $value) {
                        $parameters[$param] = $row->$value;
                    }
                @endphp
                <li>
                    <form action="{{ route($action->route, $parameters) }}" method="post">
                        @method($action->method)
                        @csrf

                        @if(count($action->i) == 0)
                            <button id="actionCall" type="submit" class="{{$action->class}}">
                                {{ $action->caption }}
                            </button>
                        @endif
                        @if(count($action->i))
                            <button type="submit" id="actionCall" class="{{$action->class}}">
                                <i class="{{$action->i['class']}}"
                                   title="{{$action->i['text']}}"></i> {{($action->i['caption'])? $action->i['text']: ""}}
                            </button>
                        @endif
                    </form>
                </li>
            @endforeach
        </ul>
    </td>
@endif
