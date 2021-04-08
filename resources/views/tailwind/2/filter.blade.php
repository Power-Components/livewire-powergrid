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
                <div class="mb-4 md:flex md:flex-wrap">
                    @if(isset($make_filters['date_picker']))
                        <div class="flex flex-col mb-4 md:w-1/4">
                            @foreach($make_filters['date_picker'] as $field => $date)

                                @include('livewire-powergrid::tailwind.2.components.date_picker', [
                                    'date' => $date,
                                    'inline' => false,
                                    'class_attr' => 'w-full'
                                ])

                            @endforeach
                        </div>
                    @endif
                    @if(isset($make_filters['select']))
                        <div class="flex flex-col mb-4 md:w-1/4">
                            @foreach($make_filters['select'] as $field => $select)
                                @include('livewire-powergrid::tailwind.2.components.select', [
                                    'select' => $select,
                                    'inline' => false,
                                    'class_attr' => 'w-full'
                                ])
                            @endforeach
                        </div>
                    @endif
                </div>


            </div>
        </div>
    </div>
</div>
