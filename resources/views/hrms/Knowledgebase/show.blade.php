@extends('layouts.index')

@section('content')
<div class="container mt-4">
    <!-- Main Article Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h3>{{ $article->asset_name ?? 'Title not available' }}</h3>
                    <p>Created: {{ $article->created_at ?? 'N/A' }}</p>
                    <p>Manufacturer: {{ $article->manufacturer ?? 'N/A' }}</p> <!-- Display manufacturer -->
                    <p>Model: {{ $article->model ?? 'N/A' }}</p> <!-- Display model -->
                    <p>Serial Number: {{ $article->serial_number ?? 'N/A' }}</p> <!-- Display serial number -->
                    <p>Status: {{ $article->status ?? 'N/A' }}</p> <!-- Display status -->
                    <p>Value: {{ $article->value ?? 'N/A' }}</p> <!-- Display value -->
                    <p>{{ $article->description ?? 'Content not available' }}</p>
                </div>
            </div>
        </div>
        

        <!-- Sidebar Section -->
        <div class="col-lg-4">
            <!-- Popular Articles -->
            <div class="card mb-4">
                <div class="card-header">Popular Articles</div>
                <ul class="list-group list-group-flush">
                    @if(isset($popularArticles) && !$popularArticles->isEmpty())
                        @foreach($popularArticles as $article)
                            <li class="list-group-item">
                                <a href="{{ route('knowledgebase.show', ['knowledgebase' => $article->id]) }}">{{ $article->asset_name }}</a>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item">No popular articles available.</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
