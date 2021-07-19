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

                @if($action->event !== '')
                    <button wire:click='$emit("{{ $action->event }}", @json($parameters))' class="{{ $action->class }}">
                        {{ $action->caption ?? '' }}
                    </button>

                @elseif($action->view !== '')
                    <button wire:click='$emit("openModal", "{{$action->view}}", @json($parameters))' class="{{ $action->class }}">
                        {{ $action->caption ?? '' }}
                    </button>

                @else
                    <div class="px-2">
                        <form @if($action->method !== 'delete') target="_blank"
                              @endif action="{{ route($action->route, $parameters) }}" method="post">
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
                @endif
            @endforeach
        </div>
    </td>
@endif
