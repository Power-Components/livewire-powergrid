<div class="accordion pt-3 pb-2" id="accordion">
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse"
                    aria-expanded="true" aria-controls="collapse">
                {{ trans('livewire-powergrid::datatable.buttons.filter') }}
            </button>
        </h2>
        <div id="collapse" class="accordion-collapse collapse show" aria-labelledby="headingOne"
             data-bs-parent="#accordion">
            <div class="accordion-body">

                <div class="row">
                    @php
                        $customConfig = [];
                    @endphp
                    @if(isset($make_filters['date_picker']))
                        @foreach($make_filters['date_picker'] as $field => $date)
                            @include('livewire-powergrid::bootstrap.50.components.date_picker', [
                                'inline' => false
                        ])
                        @endforeach
                    @endif
                    @if(isset($make_filters['select']))
                        @foreach($make_filters['select'] as $field => $select)
                            @include('livewire-powergrid::bootstrap.50.components.select', [
                                'inline' => false
                            ])
                        @endforeach
                    @endif
                    @if(isset($make_filters['number']))
                        @foreach($make_filters['number'] as $field => $number)
                            @include('livewire-powergrid::bootstrap.50.components.number', [
                                'inline' => false
                            ])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
