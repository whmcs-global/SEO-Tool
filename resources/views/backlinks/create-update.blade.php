@extends('layouts.admin')

@section('title')
{{  $backlink ? __('Update Backlink') :  __('Create Backlink') }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{  $backlink ?  __('Update Backlink') :  __('Create Backlink') }}</div>
                    <div class="card-body">
                        <form method="POST" id="backlinkForm" action="{{ route('backlinks.create', $backlink) }}">
                            @csrf
                            @if($backlink) @method('PUT') @endif
                            
                            <div class="mb-3 row">
                                <label for="website" class="col-md-4 col-form-label text-md-end">{{ __('Website') }}</label>
                                <div class="col-md-6">
                                    <input id="website" type="text" class="form-control @error('website') is-invalid @enderror" placeholder="e.g., example.com" name="website" value="{{ $backlink ? $backlink->website : old('website') }}" >
                                    @error('website')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="url" class="col-md-4 col-form-label text-md-end">{{ __('URL') }}</label>
                                <div class="col-md-6">
                                    <input id="url" type="text" placeholder="e.g., http://www.example.com" class="form-control @error('url') is-invalid @enderror" name="url" value="{{ $backlink ? $backlink->url : old('url') }}" >
                                    @error('url')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="target_keyword" class="col-md-4 col-form-label text-md-end">{{ __('Target Keyword') }}</label>
                                <div class="col-md-6">
                                    <input id="target_keyword" placeholder="e.g., best SEO practices" type="text" class="form-control @error('target_keyword') is-invalid @enderror" name="target_keyword" value="{{ $backlink ? $backlink->target_keyword : old('target_keyword') }}" >
                                    @error('target_keyword')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="backlink_source" class="col-md-4 col-form-label text-md-end">{{ __('Backlink Source') }}</label>
                                <div class="col-md-6">
                                    <input id="backlink_source" placeholder="e.g., Blog Post, Directory" type="text" class="form-control @error('backlink_source') is-invalid @enderror" name="backlink_source" value="{{ $backlink ? $backlink->backlink_source : old('backlink_source') }}" >
                                    @error('backlink_source')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>                   
                            <div class="mb-3 row">
                                <label for="link_type" class="col-md-4 col-form-label text-md-end">{{ __('Link Type') }}</label>
                                <div class="col-md-6">
                                    <select id="link_type" class="form-control @error('link_type') is-invalid @enderror" name="link_type" >
                                        <option value="">Select Link Type</option>
                                        <option value="Guest Post" {{ $backlink && $backlink->link_type == 'Guest Post' ? 'selected' : '' }}>Guest Post</option>
                                        <option value="Infographics" {{ $backlink && $backlink->link_type == 'Infographics' ? 'selected' : '' }}>Infographics</option>
                                        <option value="Sponsored Content" {{ $backlink && $backlink->link_type == 'Sponsored Content' ? 'selected' : '' }}>Sponsored Content</option>
                                        <option value="Social Bookmarking" {{ $backlink && $backlink->link_type == 'Social Bookmarking' ? 'selected' : '' }}>Social Bookmarking</option>
                                        <option value="Infographic Submission" {{ $backlink && $backlink->link_type == 'Infographic Submission' ? 'selected' : '' }}>Infographic Submission</option>
                                        <option value="Business Listing" {{ $backlink && $backlink->link_type == 'Business Listing' ? 'selected' : '' }}>Business Listing</option>
                                        <option value="Web 2.0" {{ $backlink && $backlink->link_type == 'Web 2.0' ? 'selected' : '' }}>Web 2.0</option>
                                        <option value="PPT Submission" {{ $backlink && $backlink->link_type == 'PPT Submission' ? 'selected' : '' }}>PPT Submission</option>
                                        <option value="Podcast Submission" {{ $backlink && $backlink->link_type == 'Podcast Submission' ? 'selected' : '' }}>Podcast Submission</option>
                                        <option value="PDF Submission" {{ $backlink && $backlink->link_type == 'PDF Submission' ? 'selected' : '' }}>PDF Submission</option>
                                        <option value="Article Submission" {{ $backlink && $backlink->link_type == 'Article Submission' ? 'selected' : '' }}>Article Submission</option>
                                        <option value="Quora" {{ $backlink && $backlink->link_type == 'Quora' ? 'selected' : '' }}>Quora</option>
                                        <option value="Forum submission" {{ $backlink && $backlink->link_type == 'Forum submission' ? 'selected' : '' }}>Forum submission</option>
                                        <option value="PR Submission" {{ $backlink && $backlink->link_type == 'PR Submission' ? 'selected' : '' }}>PR Submission</option>
                                        <option value="Brand Mentions" {{ $backlink && $backlink->link_type == 'Brand Mentions' ? 'selected' : '' }}>Brand Mentions</option>
                                        <option value="Guest Post Submission" {{ $backlink && $backlink->link_type == 'Guest Post Submission' ? 'selected' : '' }}>Guest Post Submission</option>
                                        <option value="Broken Link Building" {{ $backlink && $backlink->link_type == 'Broken Link Building' ? 'selected' : '' }}>Broken Link Building</option>
                                    </select>
                                    @error('link_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>                            
                            <div class="mb-3 row">
                                <label for="anchor_text" class="col-md-4 col-form-label text-md-end">{{ __('Anchor Text') }}</label>
                                <div class="col-md-6">
                                    <input id="anchor_text" placeholder="e.g., Click Here" type="text" class="form-control @error('anchor_text') is-invalid @enderror" name="anchor_text" value="{{ $backlink ? $backlink->anchor_text : old('anchor_text') }}" >
                                    @error('anchor_text')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="domain_authority" class="col-md-4 col-form-label text-md-end">{{ __('Domain Authority') }}</label>
                                <div class="col-md-6">
                                    <input id="domain_authority" placeholder="e.g., 50" type="text" class="form-control @error('domain_authority') is-invalid @enderror" name="domain_authority" value="{{ $backlink ? $backlink->domain_authority : old('domain_authority') }}" >
                                    @error('domain_authority')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="page_authority" class="col-md-4 col-form-label text-md-end">{{ __('Page Authority') }}</label>
                                <div class="col-md-6">
                                    <input id="page_authority" placeholder="e.g., 45" type="text" class="form-control @error('page_authority') is-invalid @enderror" name="page_authority" value="{{ $backlink ? $backlink->page_authority : old('page_authority') }}" >
                                    @error('page_authority')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="contact_person" class="col-md-4 col-form-label text-md-end">{{ __('Contact Person') }}</label>
                                <div class="col-md-6">
                                    <input id="contact_person" placeholder="e.g., John Doe" type="text" class="form-control @error('contact_person') is-invalid @enderror" name="contact_person" value="{{ $backlink ? $backlink->contact_person : old('contact_person') }}" >
                                    @error('contact_person')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="notes_comments" class="col-md-4 col-form-label text-md-end">{{ __('Notes/Comments') }}</label>
                                <div class="col-md-6">
                                    <textarea id="notes_comments" placeholder="Enter a Note" class="form-control @error('notes_comments') is-invalid @enderror" name="notes_comments">{{ $backlink ? $backlink->notes_comments : old('notes_comments') }}</textarea>
                                    @error('notes_comments')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label for="status" class="col-md-4 col-form-label text-md-end">{{ __('Status') }}</label>
                                <div class="col-md-6">
                                    <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" >
                                        <option value="Inactive" {{ $backlink && $backlink->status == "Inactive" ? 'selected' : '' }}>Inactive</option>
                                        <option value="Active" {{ $backlink && $backlink->status == "Active" ? 'selected' : '' }}>Active</option>
                                        <option value="Pending" {{ $backlink && $backlink->status == "Pending" ? 'selected' : '' }}>Pending</option>
                                        <option value="Declined" {{ $backlink && $backlink->status == "Declined" ? 'selected' : '' }}>Declined</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-0 row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Save') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @includeFirst(['validation.js_backlinks'])
@endpush