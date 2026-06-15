@extends('layouts.index')

@section('content')
<div class="container mt-4">
    <!-- Knowledgebase Title and Breadcrumb -->
    <div class="row">
        <div class="col-12 mb-3">
            <h2>Knowledgebase</h2>
            <p>Dashboard / Knowledgebase</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Knowledgebase Sections -->
        <div class="col-md-12 mb-4">
            <h4>Installation & Activation</h4>
            <ul class="list-group">
                @foreach($popularArticles as $article)
                    <li class="list-group-item">
                        <a href="{{ route('knowledgebase.show', ['knowledgebase' => $article->id]) }}">{{ $article->asset_name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Repeat for other sections like Premium Members Features and API Usage -->
        <div class="col-md-12 mb-4">
            <h4>Premium Members Features</h4>
            <ul class="list-group">
                <li class="list-group-item"><a href="{{ route('knowledgebase.show', ['knowledgebase' => 5]) }}">Sed ut perspiciatis unde omnis?</a></li>
                <li class="list-group-item"><a href="{{ route('knowledgebase.show', ['knowledgebase' => 6]) }}">Sed ut perspiciatis unde omnis?</a></li>
                <li class="list-group-item"><a href="{{ route('knowledgebase.show', ['knowledgebase' => 7]) }}">Sed ut perspiciatis unde omnis?</a></li>
                <li class="list-group-item"><a href="{{ route('knowledgebase.show', ['knowledgebase' => 8]) }}">Sed ut perspiciatis unde omnis?</a></li>
            </ul>
        </div>

        <div class="col-md-12 mb-4">
            <h4>API Usage & Guidelines</h4>
            <ul class="list-group">
                <li class="list-group-item"><a href="{{ route('knowledgebase.show', ['knowledgebase' => 9]) }}">Sed ut perspiciatis unde omnis?</a></li>
                <li class="list-group-item"><a href="{{ route('knowledgebase.show', ['knowledgebase' => 10]) }}">Sed ut perspiciatis unde omnis?</a></li>
                <li class="list-group-item"><a href="{{ route('knowledgebase.show', ['knowledgebase' => 11]) }}">Sed ut perspiciatis unde omnis?</a></li>
                <li class="list-group-item"><a href="{{ route('knowledgebase.show', ['knowledgebase' => 12]) }}">Sed ut perspiciatis unde omnis?</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection
