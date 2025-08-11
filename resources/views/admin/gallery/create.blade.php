@extends('admin.layouts.admin')
@section('content')
<div class="page-inner">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="fw-bold">Add Photo</h3>
    <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">Back</a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card card-round">
    <div class="card-body">
      <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
          <label class="form-label">Title (optional)</label>
          <input type="text" name="title" class="form-control" value="{{ old('title') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Image</label>
          <input type="file" name="image" class="form-control" required accept="image/*">
        </div>
        <div class="mb-3">
          <label class="form-label">Display Order (optional)</label>
          <input type="number" name="display_order" class="form-control" value="{{ old('display_order') }}" min="0">
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
          <label class="form-check-label" for="is_active">Active</label>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
      </form>
    </div>
  </div>
</div>
@endsection