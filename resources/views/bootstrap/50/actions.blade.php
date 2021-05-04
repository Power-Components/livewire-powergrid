@if(isset($actionBtns) && count($actionBtns))
    <td class="text-center">
        <div class="d-flex justify-content-center">
            @foreach($actionBtns as $action)
                @php
                    $parameters = [];
                    foreach ($action->param as $param => $value) {
                        $parameters[$param] = $row->$value;
                    }
                @endphp
                    <div class="px-2">
                        <form @if($action->method !== 'delete') target="_blank" @endif action="{{ route($action->route, $parameters) }}" method="post">
                            @method($action->method)
                            @csrf

                            @if(count($action->i) == 0)
                                <button type="submit" class="{{$action->class}}">
                                    {{ $action->caption }}
                                </button>
                            @endif
                            @if(count($action->i))
                                <button id="actionCall" class="{{$action->class}}">
                                    <i class="{{$action->i['class']}}"
                                       title="{{$action->i['text']}}"></i> {{($action->i['caption'])? $action->i['text']: ""}}
                                </button>
                            @endif
                        </form>
                    </div>
            @endforeach
        </div>
    </td>
@endif
