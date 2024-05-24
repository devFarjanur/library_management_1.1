@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.categories') }}">Book Categories</a></li>
            <li class="breadcrumb-item active" aria-current="page">Book Categories List</li>
        </ol>
    </nav>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="card-title">Book Category List</h6>
                    <a href="{{ route('admin.create.categories') }}" class="btn btn-primary">Add Book Category</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Book Category</th>
                                <th>Action</th> <!-- Added this header for action buttons -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $category->category_name }}</td>
                                    <td>
                                        <a href="{{ route('admin.edit.category', $category->id) }}"
                                            class="btn btn-info btn-sm">Edit</a>
                                        <form action="{{ route('admin.delete.category', $category->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection