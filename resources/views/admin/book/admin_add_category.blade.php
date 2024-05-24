@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.categories') }}">Book Categories</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Book Category</li>
        </ol>
    </nav>

    <div class="row">

        <div class="col-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add Book Category Name</h4>

                    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="category" class="form-label">Book Category</label>
                            <input id="category" class="form-control" name="category_name" type="text">
                        </div>

                        <input class="btn btn-primary" type="submit" value="Add Category">
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
