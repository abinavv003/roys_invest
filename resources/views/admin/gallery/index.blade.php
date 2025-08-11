@extends('admin.layouts.admin')
@section('content')
<div class="page-inner">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="fw-bold">Gallery</h3>
    <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">Add Photo</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card card-round">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table" id="basic-datatables">
          <thead>
            <tr>
              <th>Preview</th>
              <th>Title</th>
              <th>Order</th>
              <th>Active</th>
              <th>Created</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($photos as $photo)
              <tr>
                <td style="width:120px">
                  <img src="{{ asset('storage/' . $photo->image_path) }}" alt="{{ $photo->title }}" class="img-thumbnail" style="max-width:100px">
                </td>
                <td>{{ $photo->title }}</td>
                <td>{{ $photo->display_order }}</td>
                <td>
                  <span class="badge {{ $photo->is_active ? 'badge-success' : 'badge-danger' }}">{{ $photo->is_active ? 'Yes' : 'No' }}</span>
                </td>
                <td>{{ $photo->created_at->format('Y-m-d') }}</td>
                <td class="text-end">
                  <a href="{{ route('admin.gallery.edit', $photo) }}" class="btn btn-sm btn-warning">Edit</a>
                  <form action="{{ route('admin.gallery.destroy', $photo) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this photo?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">No photos yet</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection