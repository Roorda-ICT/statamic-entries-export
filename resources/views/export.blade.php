@extends('statamic::layout')
@section('title', __('Export entries'))

@section('content')
    <div class="flex items-center justify-between">
        <h1>{{ __('Export entries') }}</h1>
    </div>

    <div class="mt-3 card">
        {{ __('Select the collection you wish to export.') }}

        <form type="POST" action="{{ cp_route('utilities.entries-export.download') }}">
            @csrf

            <div class="select-input-container relative w-full">
                <select class="pr-4" name="collection">
                    <option value="" selected disabled>-</option>

                    @foreach($collections as $collection)
                        <option value="{{ $collection->id() }}">{{ $collection->title() }}</option>
                    @endforeach
                </select>

                <svg-icon name="chevron-down-xs" class="absolute inset-y-0 right-0 w-2 h-full mr-1.5 pointer-events-none"></svg-icon>
            </div>

            <button class="btn-primary ml-1">{{ __('Export entries') }}</button>
        </form>
    </div>
@stop
