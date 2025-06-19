@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <h1>Website-Wide Search</h1>
        <div class="input-group mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search for blogs, products, pages, or FAQs...">
            <button class="btn btn-primary" onclick="search()">Search</button>
        </div>
        <div id="suggestions" class="list-group mb-3"></div>
        <div id="results"></div>
    </div>
</div>
<script>
async function search() {
    const query = document.getElementById('searchInput').value;
    const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
    const data = await response.json();
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '';
    data.results.forEach(result => {
        resultsDiv.innerHTML += `
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title">[${result.type}] ${result.title}</h5>
                    <p class="card-text">${result.snippet}</p>
                    <a href="${result.link}" class="btn btn-link">View</a>
                </div>
            </div>`;
    });
}

async function fetchSuggestions() {
    const query = document.getElementById('searchInput').value;
    if (query.length < 2) return;
    const response = await fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`);
    const suggestions = await response.json();
    const suggestionsDiv = document.getElementById('suggestions');
    suggestionsDiv.innerHTML = '';
    suggestions.forEach(suggestion => {
        suggestionsDiv.innerHTML += `<a href="#" class="list-group-item list-group-item-action">${suggestion}</a>`;
    });
}

document.getElementById('searchInput').addEventListener('input', fetchSuggestions);
</script>
@endsection