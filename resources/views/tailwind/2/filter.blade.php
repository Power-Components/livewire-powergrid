<div class="w-full my-2 pb-2">
    <div x-data={show:true} class="rounded-sm">
        <div class="border border-b-0 bg-blue-100 px-4 py-3 cursor-pointer" id="headingOne" @click="show=!show">
            <button class="appearance-none text-black font-medium text-gray-500 hover:text-blue-700 focus:outline-none"
                    type="button">
                {{ trans('livewire-powergrid::datatable.buttons.filter') }}
            </button>
        </div>
        <div x-show="show" class="border border-b-1 px-6 py-4">
            <div>
                @php
                    $customConfig = [];
                @endphp
                <div class="md:flex md:flex-wrap">
                    @if(isset($make_filters['date_picker']))
                        @foreach($make_filters['date_picker'] as $field => $date)
                            <div class="flex flex-col mb-4 md:w-1/4">
                                @include('livewire-powergrid::tailwind.2.components.date_picker', [
                                    'inline' => false,
                                    'class_attr' => 'w-full'
                                ])
                            </div>
                        @endforeach
                @endif
                @if(isset($make_filters['select']))
                    @foreach($make_filters['select'] as $field => $select)
                        <div class="flex flex-col mb-4 md:w-1/4">
                            @include('livewire-powergrid::tailwind.2.components.select', [
                                'inline' => false,
                                'class_attr' => 'w-full'
                            ])
                        </div>
                    @endforeach
                @endif
                @if(isset($make_filters['number']))
                    @foreach($make_filters['number'] as $field => $number)
                        <div class="flex flex-col mb-4 md:w-1/4">
                            @include('livewire-powergrid::tailwind.2.components.number', [
                                'inline' => false,
                                'class_attr' => 'w-full'
                            ])
                        </div>
                    @endforeach
                @endif
            </div>
            </div>
        </div>
    </div>
</div>
