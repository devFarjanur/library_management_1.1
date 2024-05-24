@extends('admin.admin_dashboard')
@section('admin')



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<div class="page-content">

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.categories') }}">Product Categories</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Product Category</li>
        </ol>
    </nav>


    <div class="row">

        <div class="col-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Product Category Name</h4>

                    <form method="POST" action="{{ route('admin.update.category', $category->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="category" class="form-label">Product Category:</label>
                            <input id="category" class="form-control" name="category_name" type="text"
                                value="{{ $category->category_name }}">
                        </div>

                        <input class="btn btn-primary" type="submit" value="Update Category">
                    </form>


                </div>
            </div>
        </div>

    </div>

</div>





@endsection